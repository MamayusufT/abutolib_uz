<script setup lang="ts">
import {
  Clock, Trophy, Play, ChevronLeft, ChevronRight,
  Check, CheckCircle2, CircleDot, Circle
} from 'lucide-vue-next'

interface Answer   { id: number; answer: string }
interface Question { id: number; question: string; difficulty: string; answers: Answer[] }
interface MyResult {
  status: string; score_percent: number
  correct_answers: number; wrong_answers: number
  skipped_questions: number; time_spent: number
}

const props = defineProps<{
  questions:      Question[]
  current:        number
  selected:       Record<number, number>
  finished:       boolean
  saving:         boolean
  myResult:       MyResult | null
  canStart:       boolean
  testIsActive:   boolean
  isRegistered:   boolean
  status:         'upcoming' | 'active' | 'ended'
  timeLeft:       number
  timerFormatted: string
  currentQ:       Question | undefined
  difficultyMap:  Record<string, { label: string; cls: string }>
  scoreColor:     (v: number) => string
  formatTime:     (s: number) => string
}>()

const emit = defineEmits<{
  (e: 'select', qId: number, aId: number): void
  (e: 'prev'): void
  (e: 'next'): void
  (e: 'jump', i: number): void
  (e: 'finish'): void
  (e: 'start'): void
  (e: 'view-leaderboard'): void
}>()

const answeredCount = computed(() => Object.keys(props.selected).length)
const progressPct   = computed(() =>
  props.questions.length > 0
    ? (answeredCount.value / props.questions.length) * 100
    : 0
)

function qStatus(q: Question, index: number): 'active' | 'answered' | 'unanswered' {
  if (index === props.current) return 'active'
  if (props.selected[q.id] !== undefined) return 'answered'
  return 'unanswered'
}

function forceFinish() {
  if (!props.finished && props.testIsActive) {
    emit('finish')
  }
}
</script>

<template>
  <div>

    <!-- ── FINISHED ─────────────────────────────────────────── -->
    <div v-if="finished && myResult"
      class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-10 text-center">
      <div v-if="saving" class="flex flex-col items-center gap-3 py-6">
        <svg class="animate-spin w-10 h-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        <span class="text-sm font-semibold text-slate-500">Natija saqlanmoqda...</span>
      </div>
      <template v-else>
        <div class="w-20 h-20 rounded-3xl flex items-center justify-center mx-auto mb-5 shadow-lg"
          :class="myResult.score_percent >= 70
            ? 'bg-emerald-100 dark:bg-emerald-900/30'
            : 'bg-orange-100 dark:bg-orange-900/30'">
          <Trophy class="w-10 h-10"
            :class="myResult.score_percent >= 70 ? 'text-emerald-600' : 'text-orange-500'" />
        </div>
        <h2 class="text-2xl font-black text-slate-800 dark:text-white mb-1">Olimpiada yakunlandi!</h2>
        <div class="text-5xl font-black mt-4 mb-6" :class="scoreColor(myResult.score_percent)">
          {{ myResult.score_percent }}%
        </div>
        <div class="grid grid-cols-3 gap-3 max-w-sm mx-auto mb-6">
          <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl p-4">
            <div class="text-2xl font-black text-emerald-600">{{ myResult.correct_answers }}</div>
            <div class="text-xs text-slate-500 font-semibold mt-1">To'g'ri</div>
          </div>
          <div class="bg-red-50 dark:bg-red-900/20 rounded-2xl p-4">
            <div class="text-2xl font-black text-red-500">{{ myResult.wrong_answers }}</div>
            <div class="text-xs text-slate-500 font-semibold mt-1">Noto'g'ri</div>
          </div>
          <div class="bg-gray-100 dark:bg-slate-800 rounded-2xl p-4">
            <div class="text-2xl font-black text-slate-400">{{ myResult.skipped_questions }}</div>
            <div class="text-xs text-slate-500 font-semibold mt-1">O'tkazildi</div>
          </div>
        </div>
        <div class="flex items-center justify-center gap-1.5 text-sm text-slate-400 mb-6">
          <Clock class="w-4 h-4" /> {{ formatTime(myResult.time_spent) }}
        </div>
        <button
          class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm transition-all active:scale-95"
          @click="emit('view-leaderboard')">
          🏆 Natijalar jadvalini ko'rish
        </button>
      </template>
    </div>

    <!-- ── GATE (test boshlanmagan) ──────────────────────────── -->
    <div v-else-if="!testIsActive"
      class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-16 text-center space-y-4">

      <template v-if="canStart">
        <div class="w-16 h-16 bg-blue-50 dark:bg-blue-900/20 rounded-2xl flex items-center justify-center mx-auto">
          <Play class="w-8 h-8 text-blue-600" />
        </div>
        <p class="text-slate-600 dark:text-slate-300 font-bold">Olimpiadani boshlashga tayyormisiz?</p>
        <button
          class="inline-flex items-center gap-2 px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm transition-all active:scale-95"
          @click="emit('start')">
          <Play class="w-4 h-4" /> Boshlash
        </button>
      </template>

      <template v-else>
        <div class="w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto">
          <span class="text-3xl">ℹ️</span>
        </div>
        <p class="text-slate-400 font-bold">Test mavjud emas</p>
      </template>
    </div>

    <!-- ── ACTIVE TEST ───────────────────────────────────────── -->
    <template v-else-if="currentQ">

      <!-- Timer + progress bar -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-4 mb-3 flex items-center justify-between gap-4">
        <div class="text-sm font-black text-slate-600 dark:text-slate-300">
          {{ current + 1 }}<span class="font-normal text-slate-400"> / {{ questions.length }}</span>
          <span class="ml-2 text-xs font-semibold text-slate-400">({{ answeredCount }} javob)</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-sm font-black tabular-nums"
            :class="timeLeft < 60
              ? 'bg-red-50 dark:bg-red-900/20 text-red-600 animate-pulse'
              : 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400'">
            <Clock class="w-4 h-4" /> {{ timerFormatted }}
          </div>
          <button
            class="px-3 py-1.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl text-xs font-bold hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors"
            @click="emit('finish')">
            Yakunlash
          </button>
        </div>
      </div>

      <!-- Progress bar -->
      <div class="h-1.5 bg-gray-100 dark:bg-slate-800 rounded-full overflow-hidden mb-3">
        <div
          class="h-full bg-blue-600 rounded-full transition-all duration-500"
          :style="`width: ${progressPct}%`"
        />
      </div>

      <!-- Question dots list (scrollable) -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-4 mb-3">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Savollar</p>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="(q, i) in questions"
            :key="q.id"
            class="relative w-9 h-9 rounded-xl text-xs font-black transition-all flex items-center justify-center border-2"
            :class="{
              // active
              'bg-blue-600 border-blue-600 text-white scale-110 shadow-md shadow-blue-200 dark:shadow-blue-900/30':
                qStatus(q, i) === 'active',
              // answered
              'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-400 dark:border-emerald-600 text-emerald-700 dark:text-emerald-400':
                qStatus(q, i) === 'answered',
              // unanswered
              'bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-gray-700 text-slate-500 hover:border-blue-300 hover:text-blue-600':
                qStatus(q, i) === 'unanswered',
            }"
            @click="emit('jump', i)"
          >
            {{ i + 1 }}
            <!-- answered checkmark badge -->
            <span
              v-if="qStatus(q, i) === 'answered'"
              class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-emerald-500 rounded-full flex items-center justify-center"
            >
              <svg class="w-2 h-2 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
              </svg>
            </span>
          </button>
        </div>
        <!-- Legend -->
        <div class="flex items-center gap-4 mt-3 pt-3 border-t border-gray-50 dark:border-gray-800">
          <span class="flex items-center gap-1.5 text-xs text-slate-400">
            <span class="w-3 h-3 rounded bg-blue-600 inline-block" /> Joriy
          </span>
          <span class="flex items-center gap-1.5 text-xs text-slate-400">
            <span class="w-3 h-3 rounded bg-emerald-100 dark:bg-emerald-900/30 border-2 border-emerald-400 inline-block" /> Javob berildi
          </span>
          <span class="flex items-center gap-1.5 text-xs text-slate-400">
            <span class="w-3 h-3 rounded bg-gray-100 dark:bg-slate-700 border-2 border-gray-300 dark:border-gray-600 inline-block" /> Javobsiz
          </span>
        </div>
      </div>

      <!-- Question card -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-6 mb-4">
        <div class="flex items-center gap-2 mb-5">
          <span class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center text-sm font-black text-white flex-shrink-0">
            {{ current + 1 }}
          </span>
          <span v-if="difficultyMap[currentQ.difficulty]"
            class="px-2.5 py-1 rounded-lg text-xs font-bold"
            :class="difficultyMap[currentQ.difficulty].cls">
            {{ difficultyMap[currentQ.difficulty].label }}
          </span>
        </div>
        <p class="text-base font-bold text-slate-800 dark:text-white leading-relaxed mb-6">
          {{ currentQ.question }}
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <button
            v-for="(answer, aIndex) in currentQ.answers"
            :key="answer.id"
            class="flex items-center gap-3 p-4 rounded-xl border-2 text-left transition-all font-semibold text-sm"
            :class="selected[currentQ.id] === answer.id
              ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300'
              : 'border-gray-100 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-800 text-slate-700 dark:text-slate-300 bg-gray-50 dark:bg-slate-800/50'"
            @click="emit('select', currentQ.id, answer.id)"
          >
            <span
              class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-black flex-shrink-0"
              :class="selected[currentQ.id] === answer.id
                ? 'bg-blue-600 text-white'
                : 'bg-white dark:bg-slate-800 text-slate-500 border border-gray-200 dark:border-gray-700'"
            >
              {{ ['A','B','C','D'][aIndex] }}
            </span>
            {{ answer.answer }}
          </button>
        </div>
      </div>

      <!-- Prev / Next navigation -->
      <div class="flex items-center justify-between gap-3">
        <button
          :disabled="current === 0"
          class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl font-bold text-sm border border-gray-200 dark:border-gray-700 text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-900 hover:border-blue-400 hover:text-blue-600 transition-all disabled:opacity-40 disabled:cursor-not-allowed"
          @click="emit('prev')"
        >
          <ChevronLeft class="w-4 h-4" /> Oldingi
        </button>

        <button
          v-if="current < questions.length - 1"
          class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl font-bold text-sm bg-blue-600 hover:bg-blue-700 text-white transition-all active:scale-95"
          @click="emit('next')"
        >
          Keyingi <ChevronRight class="w-4 h-4" />
        </button>
        <button
          v-else
          class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl font-bold text-sm bg-emerald-600 hover:bg-emerald-700 text-white transition-all active:scale-95"
          @click="emit('finish')"
        >
          <Check class="w-4 h-4" /> Yakunlash
        </button>
      </div>

    </template>
  </div>
</template>