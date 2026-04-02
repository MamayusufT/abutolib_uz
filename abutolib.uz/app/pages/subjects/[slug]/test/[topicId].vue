<script setup lang="ts">
import MatchQuestion from '~/components/Question/MatchQuestion.vue'
import MultipleQuestion from '~/components/Question/MultipleQuestion.vue'
import OpenQuestion from '~/components/Question/OpenQuestion.vue'
import SingleQuestion from '~/components/Question/SingleQuestion.vue'

definePageMeta({ middleware: 'auth' })

const config  = useRuntimeConfig()
const route   = useRoute()
const router  = useRouter()
const { saveProgress, checkResume, startAutoSave, stopAutoSave } = useTestSave()

interface Answer { id: number; answer: string }
interface MatchPair { id: number; left: string; right: string; order: number }
interface Question {
  id: number
  question: string
  difficulty: string
  type: 'single' | 'multiple' | 'open' | 'match'
  answers: Answer[]
  match_pairs?: MatchPair[]
}
interface Topic {
  id: number
  name: string
  questions_count: number
  time_limit?: number
  subject: { name: string; slug: string; color: string }
}

const topic     = ref<Topic | null>(null)
const questions = ref<Question[]>([])
const loading   = ref(true)
const current   = ref(0)
const selected  = ref<Record<number, number | number[] | string | Record<number, number>>>({})
const finished  = ref(false)
const saving    = ref(false)
const timeLeft  = ref(0)
const totalTime = ref(0)
let   timer: any = null
const showResumeModal = ref(false)
const resumeData      = ref<any>(null)

function isAnswered(q: Question) {
  const val = selected.value[q.id]
  if (val === undefined || val === null) return false
  if (Array.isArray(val)) return val.length > 0
  if (typeof val === 'string') return val.trim().length > 0
  if (typeof val === 'object') return Object.keys(val).length > 0
  return true
}

function isMultipleSelected(questionId: number, answerId: number) {
  const val = selected.value[questionId]
  return Array.isArray(val) && val.includes(answerId)
}

function getMatchValue(questionId: number) {
  const val = selected.value[questionId]
  if (val && typeof val === 'object' && !Array.isArray(val)) return val as Record<number, number>
  return {}
}

function selectSingle(questionId: number, answerId: number) {
  if (finished.value) return
  selected.value = { ...selected.value, [questionId]: answerId }
}

function selectMultiple(questionId: number, answerId: number) {
  if (finished.value) return
  const cur    = (selected.value[questionId] as number[]) || []
  const exists = cur.includes(answerId)
  selected.value = {
    ...selected.value,
    [questionId]: exists ? cur.filter(id => id !== answerId) : [...cur, answerId],
  }
}

function setOpenAnswer(questionId: number, text: string) {
  if (finished.value) return
  selected.value = { ...selected.value, [questionId]: text }
}

function setMatchPair(questionId: number, leftId: number, rightId: number) {
  if (finished.value) return
  const current = { ...(getMatchValue(questionId)) }
  const alreadyUsedLeft = Object.keys(current).find(k => Number(k) === leftId)
  if (alreadyUsedLeft) delete current[Number(alreadyUsedLeft)]
  const alreadyUsedRight = Object.keys(current).find(k => current[Number(k)] === rightId)
  if (alreadyUsedRight) delete current[Number(alreadyUsedRight)]
  current[leftId] = rightId
  selected.value = { ...selected.value, [questionId]: current }
}

function clearMatchPair(questionId: number, leftId: number) {
  if (finished.value) return
  const current = { ...(getMatchValue(questionId)) }
  delete current[leftId]
  selected.value = { ...selected.value, [questionId]: current }
}

async function fetchQuestions() {
  try {
    const res: any = await $fetch(`${config.public.apiBase}/topics/${route.params.topicId}/questions`)
    topic.value     = res.topic
    questions.value = res.questions
    totalTime.value = (res.topic.time_limit ?? res.questions.length) * 60
    timeLeft.value  = totalTime.value

    const resume = await checkResume(Number(route.params.topicId))
    if (resume && resume.status === 'in_progress') {
      resumeData.value      = resume
      showResumeModal.value = true
    } else {
      startTest()
    }
  } catch {
    await navigateTo(`/subjects/${route.params.slug}`)
  } finally {
    loading.value = false
  }
}

function startTest() {
  showResumeModal.value = false
  startTimer()
  startAutoSave(Number(route.params.topicId), () => selected.value, () => totalTime.value - timeLeft.value)
}

function continueTest() {
  if (resumeData.value) {
    selected.value = resumeData.value.answers || {}
    const spent    = resumeData.value.time_spent || 0
    timeLeft.value = Math.max(totalTime.value - spent, 0)
  }
  showResumeModal.value = false
  startTimer()
  startAutoSave(Number(route.params.topicId), () => selected.value, () => totalTime.value - timeLeft.value)
}

function startTimer() {
  timer = setInterval(() => {
    if (timeLeft.value <= 0) { finish('completed'); return }
    timeLeft.value--
  }, 1000)
}

function next() { if (current.value < questions.value.length - 1) current.value++ }
function prev() { if (current.value > 0) current.value-- }

async function finish(status: 'completed' | 'abandoned' = 'completed') {
  clearInterval(timer)
  stopAutoSave()
  finished.value = true
  saving.value   = true
  await saveProgress(Number(route.params.topicId), selected.value, totalTime.value - timeLeft.value, status)
  saving.value = false
}

async function handleBeforeUnload() {
  if (!finished.value && Object.keys(selected.value).length > 0) {
    stopAutoSave()
    clearInterval(timer)
    await saveProgress(Number(route.params.topicId), selected.value, totalTime.value - timeLeft.value, 'in_progress')
  }
}

const score = computed(() => {
  const answered = questions.value.filter(q => isAnswered(q)).length
  const total    = questions.value.length
  return { answered, total, skipped: total - answered, percent: total > 0 ? Math.round((answered / total) * 100) : 0 }
})

const timeFormatted = computed(() => {
  const m = Math.floor(timeLeft.value / 60).toString().padStart(2, '0')
  const s = (timeLeft.value % 60).toString().padStart(2, '0')
  return `${m}:${s}`
})

const timeSpentFormatted = computed(() => {
  const spent = totalTime.value - timeLeft.value
  const m = Math.floor(spent / 60).toString().padStart(2, '0')
  const s = (spent % 60).toString().padStart(2, '0')
  return `${m}:${s}`
})

const currentQ = computed(() => questions.value[current.value])

const difficultyColor: Record<string, string> = {
  easy: 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800',
  medium: 'text-amber-600 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800',
  hard: 'text-rose-600 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800',
}
const difficultyLabel: Record<string, string> = { easy: 'Oson', medium: "O'rta", hard: 'Qiyin' }
const typeInfo: Record<string, { label: string; color: string; icon: string }> = {
  single: { label: 'Bitta javob', color: 'text-blue-600 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800', icon: '⊙' },
  multiple: { label: "Ko'p javob", color: 'text-violet-600 bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-800', icon: '⊞' },
  open: { label: 'Ochiq savol', color: 'text-orange-600 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800', icon: '✎' },
  match: { label: 'Moslashtirish', color: 'text-teal-600 bg-teal-50 dark:bg-teal-900/20 border border-teal-200 dark:border-teal-800', icon: '⇄' },
}

const dotColor = (q: Question, i: number) => {
  if (i === current.value) return ''
  if (!isAnswered(q)) return 'bg-gray-100 dark:bg-slate-800 text-slate-400 hover:bg-gray-200 dark:hover:bg-slate-700'
  const map: Record<string, string> = { multiple: 'bg-violet-100 dark:bg-violet-900/30 text-violet-600', open: 'bg-orange-100 dark:bg-orange-900/30 text-orange-600', match: 'bg-teal-100 dark:bg-teal-900/30 text-teal-600', single: 'bg-blue-100 dark:bg-blue-900/30 text-blue-600' }
  return map[q.type] || map.single
}

onMounted(fetchQuestions)
onBeforeUnmount(handleBeforeUnload)

useSeoMeta({ title: topic.value?.subject?.name ? `Test: ${topic.value.subject.name}` : 'Test', description: topic.value ? `${topic.value.subject.name} - ${topic.value.name} mavzulari bo'yicha test` : "Mavzu bo'yicha testni boshlang." })

useHead(() => ({ meta: [{ name: 'robots', content: finished.value ? 'index, follow' : 'noindex, nofollow' }] }))
</script>

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-slate-950 py-6 px-4">
    <div class="max-w-4xl mx-auto">

      <div v-if="loading" class="space-y-4 animate-pulse">
        <div class="h-20 bg-white dark:bg-slate-900 rounded-2xl shadow-sm" />
        <div class="h-12 bg-white dark:bg-slate-900 rounded-2xl shadow-sm" />
        <div class="h-64 bg-white dark:bg-slate-900 rounded-2xl shadow-sm" />
        <div class="grid grid-cols-4 gap-2">
          <div v-for="i in 8" :key="i" class="h-10 bg-white dark:bg-slate-900 rounded-xl" />
        </div>
      </div>

      <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100">
        <div v-if="showResumeModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md">
          <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-2xl p-8 w-full max-w-sm">
            <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center mb-5 mx-auto shadow-lg shadow-blue-500/30">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/>
              </svg>
            </div>
            <h3 class="text-xl font-black text-slate-800 dark:text-white text-center mb-1">Davom ettirish</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 text-center mb-4">Bu mavzuda saqlangan jarayoningiz mavjud.</p>
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-2xl p-4 mb-6 text-center border border-blue-100 dark:border-blue-800">
              <div class="text-xs text-blue-500 font-semibold uppercase tracking-wide mb-1">Javob berilgan</div>
              <div class="text-3xl font-black text-blue-600 dark:text-blue-400">
                {{ Object.keys(resumeData?.answers || {}).length }}
                <span class="text-slate-400 font-medium text-xl">/ {{ questions.length }}</span>
              </div>
            </div>
            <div class="flex gap-3">
              <button class="flex-1 py-3 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl font-bold text-sm transition-all" @click="startTest">
                Yangidan
              </button>
              <button class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-blue-500/30" @click="continueTest">
                Davom ettirish
              </button>
            </div>
          </div>
        </div>
      </Transition>

      <div v-if="finished && !loading" class="text-center">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm p-10">
          <div v-if="saving" class="flex flex-col items-center gap-3 py-8">
            <svg class="animate-spin w-10 h-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <span class="text-sm font-semibold text-slate-400">Natija saqlanmoqda...</span>
          </div>
          <template v-else>
            <div class="w-24 h-24 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl"
              :class="score.percent >= 70 ? 'bg-emerald-500 shadow-emerald-400/30' : 'bg-orange-500 shadow-orange-400/30'">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-white" viewBox="0 0 24 24" fill="currentColor">
                <path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.491 4.491 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.491 4.491 0 01-1.307 3.497 4.491 4.491 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.491 4.491 0 01-3.497-1.307 4.491 4.491 0 01-1.307-3.497A4.49 4.49 0 012.25 12a4.49 4.49 0 011.549-3.397 4.491 4.491 0 011.307-3.497 4.491 4.491 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/>
              </svg>
            </div>
            <h2 class="text-3xl font-black text-slate-800 dark:text-white mb-2">Test yakunlandi!</h2>
            <p class="text-slate-400 text-sm mb-8">{{ topic?.name }} — {{ topic?.subject?.name }}</p>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
              <div class="bg-blue-50 dark:bg-blue-900/20 rounded-2xl p-4 border border-blue-100 dark:border-blue-800">
                <div class="text-3xl font-black text-blue-600 dark:text-blue-400">{{ score.answered }}</div>
                <div class="text-xs text-slate-500 font-semibold mt-1">Javob berildi</div>
              </div>
              <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-4">
                <div class="text-3xl font-black text-slate-400">{{ score.skipped }}</div>
                <div class="text-xs text-slate-500 font-semibold mt-1">O'tkazildi</div>
              </div>
              <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-4">
                <div class="text-3xl font-black text-slate-800 dark:text-white">{{ score.total }}</div>
                <div class="text-xs text-slate-500 font-semibold mt-1">Jami savol</div>
              </div>
              <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-4">
                <div class="text-2xl font-black text-slate-500 dark:text-slate-300">{{ timeSpentFormatted }}</div>
                <div class="text-xs text-slate-500 font-semibold mt-1">Vaqt sarflandi</div>
              </div>
            </div>
            <div class="flex justify-center gap-3 flex-wrap">
              <NuxtLink :to="`/subjects/${route.params.slug}`"
                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm transition-all active:scale-95 shadow-lg shadow-blue-500/25">
                Boshqa mavzu
              </NuxtLink>
              <NuxtLink to="/stats"
                class="px-6 py-3 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl font-bold text-sm transition-all active:scale-95">
                Natijalarim
              </NuxtLink>
            </div>
          </template>
        </div>
      </div>

      <div v-else-if="currentQ && !loading && !showResumeModal" class="space-y-4">

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-4">
          <div class="flex items-center justify-between gap-4">
            <div class="min-w-0 flex-1">
              <div class="text-xs text-slate-400 font-semibold uppercase tracking-wide">{{ topic?.subject?.name }}</div>
              <div class="text-base font-black text-slate-800 dark:text-white truncate mt-0.5">{{ topic?.name }}</div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
              <div class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-black tabular-nums"
                :class="timeLeft < 60
                  ? 'bg-rose-50 dark:bg-rose-900/20 text-rose-600 border border-rose-200 dark:border-rose-800'
                  : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700'">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ timeFormatted }}
              </div>
              <button
                class="px-3 py-2 bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 rounded-xl text-xs font-bold hover:bg-rose-100 dark:hover:bg-rose-900/40 transition-colors border border-rose-200 dark:border-rose-800"
                @click="finish('completed')">
                Tugatish
              </button>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm px-4 py-3">
          <div class="flex items-center justify-between text-xs font-semibold text-slate-400 mb-2.5">
            <span>{{ current + 1 }} / {{ questions.length }} savol</span>
            <span>{{ score.answered }} ta javob berildi</span>
          </div>
          <div class="h-2 bg-gray-100 dark:bg-slate-800 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all duration-500 ease-out"
              :style="`width: ${((current + 1) / questions.length) * 100}%; background: ${topic?.subject?.color || '#3b82f6'}`" />
          </div>
          <div class="flex flex-wrap gap-1.5 mt-3">
            <button
              v-for="(q, i) in questions"
              :key="q.id"
              class="w-8 h-8 rounded-lg text-xs font-bold transition-all duration-200 hover:scale-105"
              :class="dotColor(q, i)"
              :style="i === current ? `background: ${topic?.subject?.color || '#3b82f6'}; color: white; box-shadow: 0 4px 12px ${topic?.subject?.color || '#3b82f6'}40` : ''"
              @click="current = i">
              {{ i + 1 }}
            </button>
          </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-6">
          <div class="flex items-center gap-2 flex-wrap mb-5">
            <span class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-black text-white shadow-sm flex-shrink-0"
              :style="`background: ${topic?.subject?.color || '#3b82f6'}`">
              {{ current + 1 }}
            </span>
            <span class="px-2.5 py-1 rounded-lg text-xs font-bold" :class="difficultyColor[currentQ.difficulty]">
              {{ difficultyLabel[currentQ.difficulty] }}
            </span>
            <span class="px-2.5 py-1 rounded-lg text-xs font-bold" :class="typeInfo[currentQ.type]?.color">
              {{ typeInfo[currentQ.type]?.icon }} {{ typeInfo[currentQ.type]?.label }}
            </span>
          </div>

          <p class="text-base sm:text-lg font-bold text-slate-800 dark:text-white leading-relaxed mb-6" v-html="currentQ.question" />

          <SingleQuestion
            v-if="currentQ.type === 'single'"
            :question="currentQ"
            :selected="selected[currentQ.id] as number | undefined"
            :finished="finished"
            :color="topic?.subject?.color"
            @select="selectSingle(currentQ.id, $event)"
          />

          <MultipleQuestion
            v-else-if="currentQ.type === 'multiple'"
            :question="currentQ"
            :selected="(selected[currentQ.id] as number[]) || []"
            :finished="finished"
            @select="selectMultiple(currentQ.id, $event)"
          />

          <OpenQuestion
            v-else-if="currentQ.type === 'open'"
            :question="currentQ"
            :value="(selected[currentQ.id] as string) || ''"
            :finished="finished"
            @input="setOpenAnswer(currentQ.id, $event)"
          />

          <MatchQuestion
            v-else-if="currentQ.type === 'match'"
            :question="currentQ"
            :value="getMatchValue(currentQ.id)"
            :finished="finished"
            @pair="setMatchPair(currentQ.id, $event.leftId, $event.rightId)"
            @clear="clearMatchPair(currentQ.id, $event)"
          />
        </div>

        <div class="flex items-center justify-between gap-3">
          <button
            :disabled="current === 0"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm border border-gray-200 dark:border-gray-700 text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-900 hover:border-blue-400 hover:text-blue-600 dark:hover:border-blue-500 transition-all disabled:opacity-40 disabled:cursor-not-allowed"
            @click="prev">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
            Oldingi
          </button>

          <button v-if="current < questions.length - 1"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm text-white transition-all active:scale-95 shadow-md"
            :style="`background: ${topic?.subject?.color || '#3b82f6'}; box-shadow: 0 4px 14px ${topic?.subject?.color || '#3b82f6'}40`"
            @click="next">
            Keyingi
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
            </svg>
          </button>

          <button v-else
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm bg-emerald-600 hover:bg-emerald-700 text-white transition-all active:scale-95 shadow-md shadow-emerald-500/30"
            @click="finish('completed')">
            Yakunlash
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
            </svg>
          </button>
        </div>

      </div>
    </div>
  </div>
</template>