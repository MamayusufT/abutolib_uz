<script setup lang="ts">
import {
  Trophy, Users, Lock, Globe, Info, PenLine,
  BarChart2, UserCheck, ShieldAlert, ClipboardList,
  AlertTriangle
} from 'lucide-vue-next'
import Olympiadparticipantstab from '~/components/Olympiad/Olympiadparticipantstab.vue'

const config = useRuntimeConfig()
definePageMeta({ middleware: 'auth' })
const { $api } = useApi()
const route  = useRoute()
const router = useRouter()

type Tab            = 'info' | 'test' | 'leaderboard' | 'participants'
type OlympiadStatus = 'upcoming' | 'active' | 'ended'

interface Answer   { id: number; answer: string }
interface Question { id: number; question: string; difficulty: string; answers: Answer[] }
interface LeaderboardRow {
  rank: number; user_id: number; name: string
  score_percent: number; correct_answers: number; wrong_answers: number
  skipped_questions: number; time_spent: number; finished_at: string
}
interface Participant { id: number; name: string; registered_at: string }
interface MyResult {
  status: string; score_percent: number
  correct_answers: number; wrong_answers: number
  skipped_questions: number; time_spent: number
}

const olympiad     = ref<any>(null)
const questions    = ref<Question[]>([])
const leaderboard  = ref<LeaderboardRow[]>([])
const participants = ref<Participant[]>([])
const myResult     = ref<MyResult | null>(null)
const isRegistered = ref(false)
const loading      = ref(true)
const regLoading   = ref(false)
const showUnregisterConfirm = ref(false)

const current   = ref(0)
const selected  = ref<Record<number, number>>({})
const finished  = ref(false)
const saving    = ref(false)
const timeLeft  = ref(0)
const totalTime = ref(0)
const now = ref(new Date())

let timer:     ReturnType<typeof setInterval> | null = null
let clockTick: ReturnType<typeof setInterval> | null = null

let   removeBeforeUnload: ((e: BeforeUnloadEvent) => void) | null = null

const isTestOngoing = computed(() => testIsActive.value && !finished.value)

function registerNavigationGuards() {
  if (removeBeforeUnload) return

  const handler = (e: BeforeUnloadEvent) => {
    if (!isTestOngoing.value) return
    e.preventDefault()
    e.returnValue = ''
  }
  window.addEventListener('beforeunload', handler)
  removeBeforeUnload = handler
}

function unregisterNavigationGuards() {
  if (removeBeforeUnload) {
    window.removeEventListener('beforeunload', removeBeforeUnload)
    removeBeforeUnload = null
  }
}


const VALID_TABS: Tab[] = ['info', 'test', 'leaderboard', 'participants']
const tab = computed<Tab>({
  get() {
    const q = route.query.tab as Tab
    return VALID_TABS.includes(q) ? q : 'info'
  },
  set(val: Tab) {
    router.replace({ query: { ...route.query, tab: val } })
  },
})

function goTab(key: Tab) {
  if (key === 'test' && !canOpenTest.value) return
  tab.value = key
}

const canOpenTest = computed(() =>
  isRegistered.value && (status.value === 'active' || testIsActive.value || finished.value)
)

async function fetchOlympiad() {
  const res: any = await $api(`/olympiads/${route.params.id}`)
  olympiad.value     = res
  isRegistered.value = !!res.is_registered
}

async function fetchMyResult() {
  try {
    const res: any = await $api(`/olympiads/${route.params.id}/my-result`)
    if (res?.status) {
      myResult.value = res
      if (res.status === 'completed') finished.value = true
    }
  } catch {}
}

async function fetchLeaderboard() {
  try {
    const res: any = await $api(`/olympiads/${route.params.id}/leaderboard`)
    leaderboard.value = Array.isArray(res)
      ? res.map((r: any) => ({
          rank:              r.rank              ?? 0,
          user_id:           r.user_id           ?? 0,
          name:              r.name              ?? '',
          score_percent:     r.score_percent     ?? 0,
          correct_answers:   r.correct_answers   ?? 0,
          wrong_answers:     r.wrong_answers     ?? 0,
          skipped_questions: r.skipped_questions ?? 0,
          time_spent:        r.time_spent        ?? 0,
          finished_at:       r.finished_at       ?? '',
        }))
      : []
  } catch { leaderboard.value = [] }
}

async function fetchParticipants() {
  try {
    const res: any = await $api(`/olympiads/${route.params.id}/participants`)
    participants.value = Array.isArray(res) ? res : []
  } catch { participants.value = [] }
}

async function handleRegister() {
  regLoading.value = true
  try {
    await $api(`/olympiads/${route.params.id}/register`, { method: 'POST' })
    isRegistered.value = true
    if (olympiad.value) olympiad.value.participants_count++
    await fetchParticipants()
  } catch (e: any) {
    alert(e?.data?.message || 'Xatolik yuz berdi')
  } finally { regLoading.value = false }
}

async function handleUnregister() {
  regLoading.value = true
  try {
    await $api(`/olympiads/${route.params.id}/unregister`, { method: 'DELETE' })
    isRegistered.value = false
    if (olympiad.value) olympiad.value.participants_count = Math.max(0, olympiad.value.participants_count - 1)
    await fetchParticipants()
  } catch (e: any) {
    alert(e?.data?.message || 'Xatolik yuz berdi')
  } finally { regLoading.value = false }
}

async function startTest() {
  try {
    await $api(`/olympiads/${route.params.id}/start`, { method: 'POST' })
    const res: any = await $api(`/olympiads/${route.params.id}/questions`)
    questions.value = Array.isArray(res.questions) ? res.questions : []
    totalTime.value = olympiad.value?.duration
      ? olympiad.value.duration * 60
      : questions.value.length * 60
    timeLeft.value = totalTime.value
    current.value  = 0
    tab.value = 'test'
    startTimer()
    registerNavigationGuards()
  } catch (e: any) {
    alert(e?.data?.message || 'Testni boshlashda xatolik')
  }
}

function startTimer() {
  if (timer) clearInterval(timer)
  timer = setInterval(() => {
    if (timeLeft.value <= 0) { finish(); return }
    timeLeft.value--
  }, 1000)
}

function select(qId: number, aId: number) {
  if (finished.value) return
  selected.value = { ...selected.value, [qId]: aId }
}

async function finish() {
  if (timer) clearInterval(timer)
  finished.value = true
  saving.value   = true
  unregisterNavigationGuards()
  try {
    const res: any = await $api(`/olympiads/${route.params.id}/submit`, {
      method: 'POST',
      body: { answers: selected.value, time_spent: totalTime.value - timeLeft.value },
    })
    myResult.value = res
    await fetchLeaderboard()
    tab.value = 'leaderboard'
  } finally { saving.value = false }
}

function subscribeRealtime() {
  const echo = (window as any).Echo
  if (!echo) return
  echo.channel(`olympiad.${route.params.id}`)
    .listen('.leaderboard.updated', () => fetchLeaderboard())
}

function getStatus(o: any): OlympiadStatus {
  const start = o.starts_at ? new Date(o.starts_at) : null
  const end   = o.ends_at   ? new Date(o.ends_at)   : null
  if (start && now.value < start) return 'upcoming'
  if (end   && now.value > end)   return 'ended'
  return 'active'
}

const status = computed<OlympiadStatus>(() =>
  olympiad.value ? getStatus(olympiad.value) : 'upcoming'
)

const countdownUnits = computed(() => {
  if (!olympiad.value) return []
  const target = status.value === 'upcoming' ? olympiad.value.starts_at : olympiad.value.ends_at
  if (!target || status.value === 'ended') return []
  const diff = new Date(target).getTime() - now.value.getTime()
  if (diff <= 0) return []
  const d = Math.floor(diff / 86400000)
  const h = Math.floor((diff % 86400000) / 3600000)
  const m = Math.floor((diff % 3600000) / 60000)
  const s = Math.floor((diff % 60000) / 1000)
  if (d > 0) return [
    { value: String(d).padStart(2, '0'), label: 'kun' },
    { value: String(h).padStart(2, '0'), label: 'soat' },
    { value: String(m).padStart(2, '0'), label: 'daqiqa' },
  ]
  return [
    { value: String(h).padStart(2, '0'), label: 'soat' },
    { value: String(m).padStart(2, '0'), label: 'daqiqa' },
    { value: String(s).padStart(2, '0'), label: 'sekund' },
  ]
})

const timerFormatted = computed(() => {
  const m = Math.floor(timeLeft.value / 60).toString().padStart(2, '0')
  const s = (timeLeft.value % 60).toString().padStart(2, '0')
  return `${m}:${s}`
})

const currentQ     = computed(() => questions.value[current.value])
const testIsActive = computed(() => questions.value.length > 0 && !finished.value)
const canStart = computed(() =>
  status.value === 'active' && isRegistered.value && !myResult.value && !finished.value
)

function formatDate(str: string) {
  if (!str) return ''
  return new Date(str).toLocaleString('uz-UZ', {
    day: '2-digit', month: '2-digit', year: 'numeric',
    hour: '2-digit', minute: '2-digit',
  })
}

function formatTime(s: number) {
  return `${Math.floor(s / 60).toString().padStart(2, '0')}:${(s % 60).toString().padStart(2, '0')}`
}

function scoreColor(v: number) {
  if (v >= 80) return 'text-emerald-600 dark:text-emerald-400'
  if (v >= 60) return 'text-amber-600 dark:text-amber-400'
  return 'text-red-500 dark:text-red-400'
}

const difficultyMap: Record<string, { label: string; cls: string }> = {
  easy:   { label: 'Oson',  cls: 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20' },
  medium: { label: "O'rta", cls: 'text-amber-600 bg-amber-50 dark:bg-amber-900/20' },
  hard:   { label: 'Qiyin', cls: 'text-red-600 bg-red-50 dark:bg-red-900/20' },
}

const tabList = computed(() => [
  { key: 'info'         as Tab, label: 'Haqida',          icon: Info,         locked: false },
  { key: 'test'         as Tab, label: 'Test',             icon: canOpenTest.value ? PenLine : ShieldAlert, locked: !canOpenTest.value },
  { key: 'leaderboard'  as Tab, label: 'Natijalar',        icon: BarChart2,    locked: false },
  { key: 'participants' as Tab, label: 'Qatnashuvchilar',  icon: ClipboardList,locked: false },
])

onMounted(async () => {
  try {
    await Promise.all([fetchOlympiad(), fetchMyResult(), fetchLeaderboard(), fetchParticipants()])
  } finally {
    loading.value = false
  }
  clockTick = setInterval(() => { now.value = new Date() }, 1000)
  subscribeRealtime()
})

onBeforeUnmount(() => {
  if (timer)     clearInterval(timer)
  if (clockTick) clearInterval(clockTick)
  unregisterNavigationGuards()
  const echo = (window as any).Echo
  if (echo) echo.leave(`olympiad.${route.params.id}`)
})

useSeoMeta({
  title: () => olympiad.value ? `${olympiad.value.name_uz} — Onlayn Olimpiada` : 'Olimpiada yuklanmoqda...',
  ogTitle: () => olympiad.value ? `${olympiad.value.name_uz} — Onlayn Olimpiada` : 'Olimpiada',
  description: () => olympiad.value?.description_uz || 'Bilimingizni sinash uchun onlayn olimpiadada qatnashing.',
  ogDescription: () => olympiad.value?.description_uz || 'Onlayn olimpiada, testlar va natijalar.',
  ogImage: () => olympiad.value?.image || `${config.public.siteUrl}/og-olympiad.jpg`,
  ogType: 'article',
  twitterCard: 'summary_large_image',
})

useHead({
  link: [{ rel: 'canonical', href: () => `${config.public.siteUrl}/olympiads/${route.params.id}` }],
  script: [{
    type: 'application/ld+json',
    children: computed(() => JSON.stringify({
      "@context": "https://schema.org",
      "@type": "Event",
      "name": olympiad.value?.name_uz,
      "description": olympiad.value?.description_uz,
      "startDate": olympiad.value?.starts_at,
      "endDate": olympiad.value?.ends_at,
      "eventStatus": status.value === 'active'
        ? "https://schema.org/EventScheduled"
        : "https://schema.org/EventMovedOnline",
      "eventAttendanceMode": "https://schema.org/OnlineEventAttendanceMode",
      "location": {
        "@type": "VirtualLocation",
        "url": `${config.public.siteUrl}/olympiads/${route.params.id}`
      },
      "organizer": {
        "@type": "Organization",
        "name": config.public.siteName,
        "url": config.public.siteUrl
      }
    }))
  }]
})
</script>

<template>
  <div class="py-6 px-4 max-w-7xl mx-auto">
    <div v-if="loading" class="space-y-4 animate-pulse">
      <div class="h-12 bg-gray-100 dark:bg-slate-800 rounded-2xl" />
      <div class="h-10 bg-gray-100 dark:bg-slate-800 rounded-2xl" />
      <div class="h-64 bg-gray-100 dark:bg-slate-800 rounded-2xl" />
    </div>

    <template v-else-if="olympiad">
      <div class="mb-5">
        <div class="flex items-center gap-2 mb-2 flex-wrap">
          <span
            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold"
            :class="{
              'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400': status === 'active',
              'bg-blue-50   dark:bg-blue-900/20   text-blue-700   dark:text-blue-400':       status === 'upcoming',
              'bg-gray-100  dark:bg-slate-800     text-slate-500':                           status === 'ended',
            }"
          >
            <span
              class="w-1.5 h-1.5 rounded-full"
              :class="{
                'bg-emerald-500 animate-pulse': status === 'active',
                'bg-blue-500':                  status === 'upcoming',
                'bg-slate-400':                 status === 'ended',
              }"
            />
            {{ { active: 'Faol', upcoming: 'Kutilmoqda', ended: 'Tugagan' }[status] }}
          </span>

          <span
            v-if="olympiad.type === 'private'"
            class="inline-flex items-center gap-1 px-2.5 py-1 bg-violet-50 dark:bg-violet-900/20 text-violet-600 dark:text-violet-400 rounded-lg text-xs font-bold"
          >
            <Lock class="w-3 h-3" /> Yopiq
          </span>
          <span
            v-else
            class="inline-flex items-center gap-1 px-2.5 py-1 bg-sky-50 dark:bg-sky-900/20 text-sky-600 dark:text-sky-400 rounded-lg text-xs font-bold"
          >
            <Globe class="w-3 h-3" /> Ochiq
          </span>

          <span
            v-if="isRegistered"
            class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-lg text-xs font-bold"
          >
            <UserCheck class="w-3 h-3" /> Ro'yhatdan o'tilgan
          </span>
        </div>

        <h1 class="text-xl font-black text-slate-800 dark:text-white">{{ olympiad.name_uz }}</h1>
        <p
          v-if="olympiad.description_uz"
          class="text-sm text-slate-500 dark:text-slate-400 mt-1"
          v-html="olympiad.description_uz"
        />
      </div>

      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-1.5 mb-5 flex gap-1 overflow-x-auto">
        <button
          v-for="t in tabList"
          :key="t.key"
          class="flex-1 min-w-max py-2 px-3 rounded-xl text-sm font-bold transition-all whitespace-nowrap flex items-center justify-center gap-1.5 relative"
          :class="[
            tab === t.key
              ? 'bg-blue-600 text-white shadow-sm'
              : t.locked
                ? 'text-slate-300 dark:text-slate-600 cursor-not-allowed'
                : 'text-slate-500 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-800',
          ]"
          :disabled="t.locked"
          :title="t.locked ? 'Ro\'yhatdan o\'ting va olimpiada boshlanishini kuting' : ''"
          @click="goTab(t.key)"
        >
          <component :is="t.icon" class="w-4 h-4" />
          {{ t.label }}
          <span
            v-if="t.locked && tab !== t.key"
            class="absolute -top-1 -right-1 w-4 h-4 bg-slate-300 dark:bg-slate-600 rounded-full flex items-center justify-center"
          >
            <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
            </svg>
          </span>
        </button>
      </div>

      <OlympiadInfoTab
        v-if="tab === 'info'"
        :olympiad="olympiad"
        :my-result="myResult"
        :can-start="canStart"
        :is-registered="isRegistered"
        :reg-loading="regLoading"
        :status="status"
        :countdown-units="countdownUnits"
        :format-date="formatDate"
        @start="startTest"
        @register="handleRegister"
        @unregister="showUnregisterConfirm = true"
      />

      <OlympiadTestTab
        v-else-if="tab === 'test'"
        :questions="questions"
        :current="current"
        :selected="selected"
        :finished="finished"
        :saving="saving"
        :my-result="myResult"
        :can-start="canStart"
        :test-is-active="testIsActive"
        :is-registered="isRegistered"
        :status="status"
        :time-left="timeLeft"
        :timer-formatted="timerFormatted"
        :current-q="currentQ"
        :difficulty-map="difficultyMap"
        :score-color="scoreColor"
        :format-time="formatTime"
        @select="select"
        @prev="current--"
        @next="current++"
        @jump="(i: number) => current = i"
        @finish="finish"
        @start="startTest"
        @view-leaderboard="tab = 'leaderboard'"
      />

      <OlympiadLeaderboardTab
        v-else-if="tab === 'leaderboard'"
        :leaderboard="leaderboard"
        :score-color="scoreColor"
        :format-time="formatTime"
      />

      <Olympiadparticipantstab
        v-else-if="tab === 'participants'"
        :participants="participants"
        :format-date="formatDate"
      />
    </template>

    <Teleport to="body">
      <div
        v-if="showUnregisterConfirm"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        @click.self="showUnregisterConfirm = false"
      >
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-2xl p-6 w-full max-w-sm">
          <div class="w-12 h-12 bg-red-50 dark:bg-red-900/20 rounded-2xl flex items-center justify-center mb-4 mx-auto">
            <span class="text-2xl">⚠️</span>
          </div>
          <h3 class="text-lg font-black text-slate-800 dark:text-white text-center mb-2">Ro'yhatdan chiqish</h3>
          <p class="text-sm text-slate-500 dark:text-slate-400 text-center mb-6">Olimpiadadan ro'yhatdan chiqmoqchimisiz?</p>
          <div class="flex gap-3">
            <button
              class="flex-1 py-2.5 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl font-bold text-sm transition-all"
              @click="showUnregisterConfirm = false"
            >
              Bekor qilish
            </button>
            <button
              class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl font-bold text-sm transition-all"
              @click="showUnregisterConfirm = false; handleUnregister()"
            >
              Ha, chiqish
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <Teleport to="body">
      <Transition name="slide-up">
        <div
          v-if="isTestOngoing"
          class="fixed bottom-4 left-1/2 -translate-x-1/2 z-[90] w-full max-w-lg px-4 pointer-events-none"
        >
          <div class="bg-amber-50 dark:bg-amber-950 border border-amber-300 dark:border-amber-700 rounded-2xl shadow-xl px-4 py-3 flex items-center gap-3 pointer-events-auto">
            <div class="w-8 h-8 shrink-0 bg-amber-100 dark:bg-amber-900/50 rounded-xl flex items-center justify-center">
              <AlertTriangle class="w-4 h-4 text-amber-600 dark:text-amber-400" />
            </div>
            <p class="text-xs text-amber-800 dark:text-amber-300 font-medium flex-1 leading-relaxed">
              🚨 Test davomida <strong>brauzer yoki tab yopish taqiqlangan</strong>. Sayt ichida erkin harakat qilishingiz mumkin.
            </p>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<style scoped>
.slide-up-enter-active,
.slide-up-leave-active {
  transition: transform 0.3s ease, opacity 0.3s ease;
}
.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateX(-50%) translateY(20px);
  opacity: 0;
}
</style>