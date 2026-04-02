<script setup lang="ts">
import { BarChart3, Trophy, BookOpen, Clock, TrendingUp, CheckCircle, XCircle, SkipForward } from 'lucide-vue-next'

definePageMeta({ middleware: 'auth' })

const { $api } = useApi()

interface RecentResult {
  id: number
  score_percent: number
  correct_answers: number
  wrong_answers: number
  skipped_questions: number
  total_questions: number
  time_spent: number
  finished_at: string
  topic: {
    name: string
    subject: { name: string; color: string; slug: string }
  }
}

interface Stats {
  total_tests: number
  avg_score: number
  best_score: number
  recent: RecentResult[]
}

interface ResultsPage {
  data: RecentResult[]
  last_page: number
  current_page: number
  total: number
}

const stats        = ref<Stats | null>(null)
const results      = ref<RecentResult[]>([])
const statsLoading = ref(true)
const listLoading  = ref(true)
const page         = ref(1)
const lastPage     = ref(1)
const totalResults = ref(0)

async function fetchStats() {
  try {
    const res: any = await $api('/test/stats')
    stats.value = res
  } catch {} finally {
    statsLoading.value = false
  }
}

async function fetchResults() {
  listLoading.value = true
  try {
    const res: any = await $api('/test/results', { params: { page: page.value } })
    results.value      = res.data
    lastPage.value     = res.last_page
    totalResults.value = res.total
  } catch {} finally {
    listLoading.value = false
  }
}

function goToPage(p: number) {
  if (p < 1 || p > lastPage.value || p === page.value) return
  page.value = p
  fetchResults()
}

function loadMore() {
  page.value++
  fetchResults()
}

function formatTime(seconds: number) {
  const m = Math.floor(seconds / 60).toString().padStart(2, '0')
  const s = (seconds % 60).toString().padStart(2, '0')
  return `${m}:${s}`
}

function formatDate(str: string) {
  return new Date(str).toLocaleDateString('uz-UZ', {
    day: 'numeric', month: 'short', year: 'numeric',
    hour: '2-digit', minute: '2-digit',
  })
}

function scoreColor(score: number) {
  if (score >= 80) return 'text-green-600 dark:text-green-400'
  if (score >= 60) return 'text-yellow-600 dark:text-yellow-400'
  return 'text-red-600 dark:text-red-400'
}

function scoreBg(score: number) {
  if (score >= 80) return 'bg-green-50 dark:bg-green-900/20 border-green-100 dark:border-green-900/30'
  if (score >= 60) return 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-100 dark:border-yellow-900/30'
  return 'bg-red-50 dark:bg-red-900/20 border-red-100 dark:border-red-900/30'
}

function scoreLabel(score: number) {
  if (score >= 80) return "A'lo"
  if (score >= 60) return "Yaxshi"
  if (score >= 40) return "Qoniqarli"
  return "Yaxshilash kerak"
}

onMounted(() => {
  fetchStats()
  fetchResults()
})
</script>

<template>
  <div class="py-8 px-4 max-w-7xl mx-auto space-y-6">

    <!-- Stats cards skeleton -->
    <div v-if="statsLoading" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-for="i in 4" :key="i" class="animate-pulse bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-5 h-28" />
    </div>

    <!-- Stats cards -->
    <div v-else-if="stats" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
        <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center mb-3">
          <BookOpen class="w-5 h-5 text-blue-600 dark:text-blue-400" />
        </div>
        <div class="text-2xl font-black text-slate-800 dark:text-white">{{ stats.total_tests }}</div>
        <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-0.5">Ishlangan testlar</div>
      </div>

      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
        <div class="w-10 h-10 bg-purple-50 dark:bg-purple-900/20 rounded-xl flex items-center justify-center mb-3">
          <TrendingUp class="w-5 h-5 text-purple-600 dark:text-purple-400" />
        </div>
        <div class="text-2xl font-black" :class="scoreColor(stats.avg_score)">{{ stats.avg_score }}%</div>
        <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-0.5">O'rtacha natija</div>
      </div>

      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
        <div class="w-10 h-10 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center mb-3">
          <Trophy class="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
        </div>
        <div class="text-2xl font-black text-yellow-600 dark:text-yellow-400">{{ stats.best_score }}%</div>
        <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-0.5">Eng yuqori natija</div>
      </div>

      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
        <div class="w-10 h-10 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center mb-3">
          <CheckCircle class="w-5 h-5 text-green-600 dark:text-green-400" />
        </div>
        <div class="text-2xl font-black text-slate-800 dark:text-white">{{ totalResults }}</div>
        <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-0.5">Jami testlar</div>
      </div>
    </div>

    <!-- Progress bar vizual -->
    <div v-if="stats && !statsLoading" class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-6">
      <h2 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider mb-5">Umumiy ko'rsatkichlar</h2>
      <div class="space-y-4">

        <div>
          <div class="flex justify-between text-sm font-semibold mb-2">
            <span class="text-slate-600 dark:text-slate-300 flex items-center gap-1.5">
              <TrendingUp class="w-3.5 h-3.5 text-purple-500" /> O'rtacha ball
            </span>
            <span :class="scoreColor(stats.avg_score)">{{ stats.avg_score }}%</span>
          </div>
          <div class="h-2.5 bg-gray-100 dark:bg-slate-800 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all duration-1000 bg-purple-500"
              :style="`width: ${stats.avg_score}%`" />
          </div>
        </div>

        <div>
          <div class="flex justify-between text-sm font-semibold mb-2">
            <span class="text-slate-600 dark:text-slate-300 flex items-center gap-1.5">
              <Trophy class="w-3.5 h-3.5 text-yellow-500" /> Eng yuqori natija
            </span>
            <span class="text-yellow-600 dark:text-yellow-400">{{ stats.best_score }}%</span>
          </div>
          <div class="h-2.5 bg-gray-100 dark:bg-slate-800 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all duration-1000 bg-yellow-500"
              :style="`width: ${stats.best_score}%`" />
          </div>
        </div>

        <div>
          <div class="flex justify-between text-sm font-semibold mb-2">
            <span class="text-slate-600 dark:text-slate-300 flex items-center gap-1.5">
              <BookOpen class="w-3.5 h-3.5 text-blue-500" /> Maqsad (80%)
            </span>
            <span :class="stats.avg_score >= 80 ? 'text-green-600 dark:text-green-400' : 'text-slate-400'">
              {{ stats.avg_score >= 80 ? '✓ Bajarildi' : `${Math.max(0, 80 - stats.avg_score).toFixed(1)}% qoldi` }}
            </span>
          </div>
          <div class="h-2.5 bg-gray-100 dark:bg-slate-800 rounded-full overflow-hidden relative">
            <div class="h-full rounded-full transition-all duration-1000"
              :class="stats.avg_score >= 80 ? 'bg-green-500' : 'bg-blue-500'"
              :style="`width: ${Math.min(stats.avg_score, 100)}%`" />
            <div class="absolute top-0 bottom-0 w-0.5 bg-slate-400 dark:bg-slate-500" style="left: 80%" />
          </div>
        </div>

      </div>
    </div>

    <!-- Results list -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <div>
          <h2 class="text-base font-black text-slate-800 dark:text-white">Test natijalari</h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Barcha tugallangan testlar</p>
        </div>
        <span class="px-3 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-xl text-xs font-bold">
          {{ totalResults }} ta
        </span>
      </div>

      <!-- Skeleton -->
      <div v-if="listLoading && results.length === 0" class="divide-y divide-gray-50 dark:divide-gray-800/50">
        <div v-for="i in 5" :key="i" class="p-5 flex items-center gap-4 animate-pulse">
          <div class="w-12 h-12 bg-gray-100 dark:bg-slate-800 rounded-2xl flex-shrink-0" />
          <div class="flex-1 space-y-2">
            <div class="h-3.5 bg-gray-100 dark:bg-slate-800 rounded w-1/3" />
            <div class="h-3 bg-gray-100 dark:bg-slate-800 rounded w-1/2" />
          </div>
          <div class="w-16 h-10 bg-gray-100 dark:bg-slate-800 rounded-xl" />
        </div>
      </div>

      <!-- Empty -->
      <div v-else-if="results.length === 0 && !listLoading" class="py-16 text-center">
        <div class="w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
          <BarChart3 class="w-8 h-8 text-slate-400" />
        </div>
        <p class="text-slate-500 dark:text-slate-400 font-semibold">Hali test ishlanmagan</p>
        <p class="text-slate-400 dark:text-slate-500 text-sm mt-1">Birinchi testingizni boshlang!</p>
        <NuxtLink to="/subjects"
          class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm transition-all active:scale-95">
          <BookOpen class="w-4 h-4" />
          Fanlarni ko'rish
        </NuxtLink>
      </div>

      <!-- List -->
      <TransitionGroup name="list" tag="div" class="divide-y divide-gray-50 dark:divide-gray-800/50">
        <div v-for="result in results" :key="result.id"
          class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 dark:hover:bg-slate-800/30 transition-colors">

          <!-- Score circle -->
          <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 border"
            :class="scoreBg(result.score_percent)">
            <span class="text-sm font-black" :class="scoreColor(result.score_percent)">
              {{ Math.round(result.score_percent) }}%
            </span>
          </div>

          <!-- Info -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap mb-1">
              <span class="text-sm font-black text-slate-800 dark:text-white truncate">
                {{ result.topic?.name }}
              </span>
              <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold"
                :class="scoreColor(result.score_percent)"
                :style="`background: ${result.topic?.subject?.color}15`">
                {{ scoreLabel(result.score_percent) }}
              </span>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
              <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold flex items-center gap-1"
                :style="`color: ${result.topic?.subject?.color}`">
                {{ result.topic?.subject?.name }}
              </span>
              <span class="text-xs text-slate-400 flex items-center gap-1">
                <Clock class="w-3 h-3" />
                {{ formatTime(result.time_spent) }}
              </span>
              <span class="text-xs text-slate-400">{{ formatDate(result.finished_at) }}</span>
            </div>
          </div>

          <!-- Answer stats -->
          <div class="hidden sm:flex items-center gap-2 flex-shrink-0">
            <span class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded-xl text-xs font-bold">
              <CheckCircle class="w-3 h-3" />
              {{ result.correct_answers }}
            </span>
            <span class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-red-50 dark:bg-red-900/20 text-red-500 dark:text-red-400 rounded-xl text-xs font-bold">
              <XCircle class="w-3 h-3" />
              {{ result.wrong_answers }}
            </span>
            <span class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-gray-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded-xl text-xs font-bold">
              <SkipForward class="w-3 h-3" />
              {{ result.skipped_questions }}
            </span>
          </div>

          <!-- Mobile: only total -->
          <div class="sm:hidden flex-shrink-0 text-xs text-slate-400 font-semibold">
            {{ result.correct_answers }}/{{ result.total_questions }}
          </div>

        </div>
      </TransitionGroup>

      <div v-if="lastPage > 1" class="p-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-center gap-1.5 flex-wrap">
        <!-- Prev -->
        <button
          :disabled="page === 1"
          class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-700 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:border-blue-400 hover:text-blue-600 transition-all disabled:opacity-40 disabled:cursor-not-allowed"
          @click="goToPage(page - 1)"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
          </svg>
        </button>

        <!-- Page numbers -->
        <template v-for="p in lastPage" :key="p">
          <!-- Gap -->
          <span
            v-if="(p === 2 && page > 3) || (p === lastPage - 1 && page < lastPage - 2)"
            class="w-9 h-9 flex items-center justify-center text-slate-400 text-sm"
          >…</span>

          <button
            v-else-if="p === 1 || p === lastPage || Math.abs(p - page) <= 1"
            class="w-9 h-9 rounded-xl border text-sm font-bold transition-all"
            :class="p === page
              ? 'bg-blue-600 border-blue-600 text-white'
              : 'border-gray-200 dark:border-gray-700 text-slate-600 dark:text-slate-300 hover:border-blue-400 hover:text-blue-600'"
            @click="goToPage(p)"
          >
            {{ p }}
          </button>
        </template>

        <!-- Next -->
        <button
          :disabled="page === lastPage"
          class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-700 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:border-blue-400 hover:text-blue-600 transition-all disabled:opacity-40 disabled:cursor-not-allowed"
          @click="goToPage(page + 1)"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
          </svg>
        </button>
      </div>

    </div>

  </div>
</template>

<style scoped>
.list-leave-active { transition: all 0.3s ease; }
.list-leave-to     { opacity: 0; transform: translateX(20px); }
</style>