<script setup lang="ts">
import { Trophy, Clock, Download, Medal } from 'lucide-vue-next'

interface LeaderboardRow {
  rank: number; user_id: number; name: string
  score_percent: number; correct_answers: number; wrong_answers: number
  skipped_questions: number; time_spent: number; finished_at: string
}

const PER_PAGE = 10

const props = withDefaults(defineProps<{
  leaderboard?: LeaderboardRow[]
  scoreColor:   (v: number) => string
  formatTime:   (s: number) => string
}>(), {
  leaderboard: () => [],
})

const page = ref(1)
const rows = computed(() => props.leaderboard ?? [])

const paginated = computed(() => {
  const start = (page.value - 1) * PER_PAGE
  return rows.value.slice(start, start + PER_PAGE)
})

watch(() => rows.value.length, () => { page.value = 1 })

// ─── Excel export ───────────────────────────────────────────────────
function exportToExcel() {
  const headers = [
    "O'rin", "Ism", "Ball (%)", "To'g'ri", "Noto'g'ri",
    "O'tkazildi", "Vaqt (s)", "Yakunlangan vaqt"
  ]

  const csvRows = [
    headers.join(','),
    ...rows.value.map(r =>
      [
        r.rank,
        `"${r.name}"`,
        r.score_percent,
        r.correct_answers,
        r.wrong_answers,
        r.skipped_questions,
        r.time_spent,
        `"${r.finished_at}"`
      ].join(',')
    )
  ]

  // UTF-8 BOM so Excel reads Uzbek/Cyrillic correctly
  const bom  = '\uFEFF'
  const blob = new Blob([bom + csvRows.join('\n')], { type: 'text/csv;charset=utf-8;' })
  const url  = URL.createObjectURL(blob)
  const a    = document.createElement('a')
  a.href     = url
  a.download = `leaderboard_${new Date().toISOString().slice(0,10)}.csv`
  a.click()
  URL.revokeObjectURL(url)
}
</script>

<template>
  <div class="w-full bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">

    <!-- ── Header ──────────────────────────────────────────────── -->
    <div class="px-4 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-800
                flex flex-wrap items-center justify-between gap-3">
      <div>
        <h2 class="text-base font-black text-slate-800 dark:text-white">
          Natijalar jadvali
        </h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
          {{ rows.length }} ishtirokchi
        </p>
      </div>

      <div class="flex items-center gap-2">
        <!-- Live badge -->
        <span class="flex items-center gap-1.5 px-3 py-1.5
                     bg-emerald-50 dark:bg-emerald-900/20 rounded-xl
                     text-xs font-bold text-emerald-600 dark:text-emerald-400">
          <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse" />
          Live
        </span>

        <!-- Export button -->
        <button
          v-if="rows.length > 0"
          @click="exportToExcel"
          class="flex items-center gap-1.5 px-3 py-1.5
                 bg-blue-50 dark:bg-blue-900/20 rounded-xl
                 text-xs font-bold text-blue-600 dark:text-blue-400
                 hover:bg-blue-100 dark:hover:bg-blue-900/40
                 active:scale-95 transition-all duration-150 cursor-pointer"
          title="Excel (CSV) formatida yuklab olish"
        >
          <Download class="w-3.5 h-3.5" />
          <span class="hidden sm:inline">Excel</span>
        </button>
      </div>
    </div>

    <!-- ── Empty state ─────────────────────────────────────────── -->
    <div v-if="rows.length === 0" class="py-20 text-center">
      <Trophy class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3" />
      <p class="text-slate-400 font-semibold">Hali natijalar yo'q</p>
      <p class="text-xs text-slate-400 mt-1">Birinchi bo'lib yakunlang!</p>
    </div>

    <template v-else>

      <!-- ── Podium (1-sahifada, ≥3 ishtirokchi) ─────────────── -->
      <div
        v-if="rows.length >= 3 && page === 1"
        class="grid grid-cols-3 border-b border-gray-100 dark:border-gray-800"
      >
        <!-- 🥈 2-o'rin -->
        <div class="p-3 sm:p-5 text-center border-r border-gray-100 dark:border-gray-800">
          <div class="text-2xl sm:text-3xl mb-1 sm:mb-2">🥈</div>
          <div class="text-xs sm:text-sm font-black text-slate-700 dark:text-slate-200 truncate">
            {{ rows[1].name }}
          </div>
          <div class="text-lg sm:text-xl font-black mt-1" :class="scoreColor(rows[1].score_percent)">
            {{ rows[1].score_percent }}%
          </div>
          <div class="text-xs text-slate-400 mt-1 flex items-center justify-center gap-1">
            <Clock class="w-3 h-3" />
            <span class="hidden sm:inline">{{ formatTime(rows[1].time_spent) }}</span>
            <span class="sm:hidden">{{ formatTime(rows[1].time_spent) }}</span>
          </div>
        </div>

        <!-- 🥇 1-o'rin -->
        <div class="p-3 sm:p-5 text-center bg-gradient-to-b from-yellow-50 to-white dark:from-yellow-900/10 dark:to-slate-900">
          <div class="text-3xl sm:text-4xl mb-1 sm:mb-2">🥇</div>
          <div class="text-xs sm:text-sm font-black text-slate-800 dark:text-white truncate">
            {{ rows[0].name }}
          </div>
          <div class="text-xl sm:text-2xl font-black mt-1" :class="scoreColor(rows[0].score_percent)">
            {{ rows[0].score_percent }}%
          </div>
          <div class="text-xs text-slate-400 mt-1 flex items-center justify-center gap-1">
            <Clock class="w-3 h-3" /> {{ formatTime(rows[0].time_spent) }}
          </div>
        </div>

        <!-- 🥉 3-o'rin -->
        <div class="p-3 sm:p-5 text-center border-l border-gray-100 dark:border-gray-800">
          <div class="text-2xl sm:text-3xl mb-1 sm:mb-2">🥉</div>
          <div class="text-xs sm:text-sm font-black text-slate-700 dark:text-slate-200 truncate">
            {{ rows[2].name }}
          </div>
          <div class="text-lg sm:text-xl font-black mt-1" :class="scoreColor(rows[2].score_percent)">
            {{ rows[2].score_percent }}%
          </div>
          <div class="text-xs text-slate-400 mt-1 flex items-center justify-center gap-1">
            <Clock class="w-3 h-3" /> {{ formatTime(rows[2].time_spent) }}
          </div>
        </div>
      </div>

      <!-- ── List ────────────────────────────────────────────────── -->
      <div class="divide-y divide-gray-50 dark:divide-gray-800/50">
        <div
          v-for="row in paginated"
          :key="row.user_id"
          class="flex items-center gap-3 sm:gap-4 px-4 sm:px-5 py-3 sm:py-3.5
                 hover:bg-gray-50 dark:hover:bg-slate-800/30 transition-colors"
        >
          <!-- Rank -->
          <div class="w-7 sm:w-8 text-center flex-shrink-0">
            <span v-if="row.rank === 1" class="text-lg sm:text-xl">🥇</span>
            <span v-else-if="row.rank === 2" class="text-lg sm:text-xl">🥈</span>
            <span v-else-if="row.rank === 3" class="text-lg sm:text-xl">🥉</span>
            <span v-else class="text-xs sm:text-sm font-black text-slate-400">#{{ row.rank }}</span>
          </div>

          <!-- Info -->
          <div class="flex-1 min-w-0">
            <div class="text-sm font-black text-slate-800 dark:text-white truncate">
              {{ row.name }}
            </div>

            <!-- Mobile: compact row -->
            <div class="flex items-center gap-2 mt-0.5 flex-wrap">
              <span class="text-xs text-slate-400 flex items-center gap-1">
                <Clock class="w-3 h-3" /> {{ formatTime(row.time_spent) }}
              </span>
              <span class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold">
                {{ row.correct_answers }}✓
              </span>
              <span class="text-xs text-red-400 font-semibold">
                {{ row.wrong_answers }}✗
              </span>
              <!-- Skip count — hidden on very small screens -->
              <span class="hidden xs:inline text-xs text-slate-400 font-semibold">
                {{ row.skipped_questions }} o'tkazildi
              </span>
            </div>
          </div>

          <!-- Score -->
          <div class="text-base sm:text-lg font-black flex-shrink-0" :class="scoreColor(row.score_percent)">
            {{ row.score_percent }}%
          </div>
        </div>
      </div>

      <!-- ── Pagination ───────────────────────────────────────── -->
      <OlympiadPagination
        :current="page"
        :total="rows.length"
        :per-page="PER_PAGE"
        @go="(p) => page = p"
      />

    </template>
  </div>
</template>