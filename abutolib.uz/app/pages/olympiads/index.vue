<script setup lang="ts">
import { Trophy, Clock, Users, Lock, Globe } from 'lucide-vue-next'

const { $api } = useApi()

useSeoMeta({
  title: "Olimpiadalar",
  description: "Barcha ochiq va yaqinlashayotgan musobaqalar",
})

interface Olympiad {
  id: number
  name_uz: string
  description_uz: string | null
  type: 'public' | 'private'
  questions_count: number
  duration: number | null
  starts_at: string | null
  ends_at: string | null
  pass_score: number | null
  participants_count: number
  is_active: boolean
}

type Filter  = 'all' | 'active' | 'upcoming' | 'ended'
type Section = 'active' | 'upcoming' | 'ended'

// ─── State ────────────────────────────────────────────────────────
const all     = ref<Olympiad[]>([])
const loading = ref(true)
const now     = ref(new Date())
const filter  = ref<Filter>('all')
const perPage = ref(5)
const pages   = ref<Record<Section, number>>({ active: 1, upcoming: 1, ended: 1 })

const PER_PAGE_OPTIONS = [5, 10, 20]

let clockTick: ReturnType<typeof setInterval> | null = null

// ─── Fetch ────────────────────────────────────────────────────────
async function fetchOlympiads() {
  try {
    const res: any = await $api('/olympiads')
    all.value = Array.isArray(res) ? res : []
  } finally {
    loading.value = false
  }
}

// ─── Helpers ──────────────────────────────────────────────────────
function getStatus(o: Olympiad): Section {
  const n = now.value
  const s = o.starts_at ? new Date(o.starts_at) : null
  const e = o.ends_at   ? new Date(o.ends_at)   : null
  if (s && n < s) return 'upcoming'
  if (e && n > e) return 'ended'
  return 'active'
}

function diffMs(dateStr: string | null) {
  if (!dateStr) return -1
  return new Date(dateStr).getTime() - now.value.getTime()
}

// HH:MM:SS yoki Xk YYs ZZd formatida
function fmtCountdown(dateStr: string | null): string | null {
  const ms = diffMs(dateStr)
  if (ms <= 0) return null
  const d = Math.floor(ms / 86400000)
  const h = Math.floor((ms % 86400000) / 3600000)
  const m = Math.floor((ms % 3600000) / 60000)
  const s = Math.floor((ms % 60000) / 1000)
  const pad = (n: number) => String(n).padStart(2, '0')
  if (d > 0) return `${d}k ${pad(h)}s ${pad(m)}d`
  return `${pad(h)}:${pad(m)}:${pad(s)}`
}

function fmtDate(str: string | null) {
  if (!str) return '—'
  return new Date(str).toLocaleString('uz-UZ', {
    day: '2-digit', month: '2-digit', year: 'numeric',
    hour: '2-digit', minute: '2-digit',
  })
}

// ─── Grouped & sorted lists ───────────────────────────────────────
const activeList   = computed(() => all.value.filter(o => getStatus(o) === 'active'))
const upcomingList = computed(() =>
  all.value
    .filter(o => getStatus(o) === 'upcoming')
    .sort((a, b) => new Date(a.starts_at!).getTime() - new Date(b.starts_at!).getTime())
)
const endedList    = computed(() => all.value.filter(o => getStatus(o) === 'ended'))

const nextUpcoming = computed(() => upcomingList.value[0] ?? null)

const visibleSections = computed<Section[]>(() =>
  filter.value === 'all' ? ['active', 'upcoming', 'ended'] : [filter.value as Section]
)

function listOf(s: Section) {
  return s === 'active' ? activeList.value
       : s === 'upcoming' ? upcomingList.value
       : endedList.value
}

// ─── Pagination ───────────────────────────────────────────────────
function pageCount(s: Section) {
  return Math.max(1, Math.ceil(listOf(s).length / perPage.value))
}

function safePageOf(s: Section) {
  return Math.min(pages.value[s], pageCount(s))
}

function pagedList(s: Section) {
  const cur   = safePageOf(s)
  const start = (cur - 1) * perPage.value
  return listOf(s).slice(start, start + perPage.value)
}

function pageNumbers(s: Section): (number | '…')[] {
  const total = pageCount(s)
  const cur   = safePageOf(s)
  if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1)
  const result: (number | '…')[] = [1]
  const lo = Math.max(2, cur - 1), hi = Math.min(total - 1, cur + 1)
  if (lo > 2) result.push('…')
  for (let p = lo; p <= hi; p++) result.push(p)
  if (hi < total - 1) result.push('…')
  result.push(total)
  return result
}

function goPage(s: Section, p: number) {
  pages.value[s] = Math.max(1, Math.min(p, pageCount(s)))
}

function setFilter(f: Filter) {
  filter.value = f
  pages.value  = { active: 1, upcoming: 1, ended: 1 }
}

function setPerPage(n: number) {
  perPage.value = n
  pages.value   = { active: 1, upcoming: 1, ended: 1 }
}

function filterCount(f: Filter) {
  if (f === 'all') return all.value.length
  return listOf(f as Section).length
}

// ─── Section config ───────────────────────────────────────────────
const sectionConf: Record<Section, { dotCls: string; label: string; countCls: string }> = {
  active:   { dotCls: 'bg-green-500 animate-pulse', label: 'Hozir faol',     countCls: 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' },
  upcoming: { dotCls: 'bg-blue-500',                label: 'Yaqinlashmoqda', countCls: 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400'     },
  ended:    { dotCls: 'bg-slate-400',               label: 'Tugagan',        countCls: 'bg-gray-100 dark:bg-slate-800 text-slate-500'                         },
}

const filterLabels: Record<Filter, string> = {
  all: 'Barchasi', active: 'Faol', upcoming: 'Kutilmoqda', ended: 'Tugagan',
}

// ─── Lifecycle ────────────────────────────────────────────────────
onMounted(() => {
  fetchOlympiads()
  // now hар soniyада yangilanadi — barcha fmtCountdown() computed orqali reaktiv
  clockTick = setInterval(() => { now.value = new Date() }, 1000)
})
onBeforeUnmount(() => { if (clockTick) clearInterval(clockTick) })
</script>

<template>
  <div class="py-6 px-4 max-w-5xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-5">
      <div>
        <h1 class="text-xl font-black text-slate-800 dark:text-white">Olimpiadalar</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
          {{ loading ? 'Yuklanmoqda...' : `${all.length} ta olimpiada` }}
        </p>
      </div>
      <div class="w-9 h-9 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center">
        <Trophy class="w-5 h-5 text-yellow-500" />
      </div>
    </div>

    <!-- Skeleton -->
    <div v-if="loading" class="space-y-3 animate-pulse">
      <div class="h-9 w-72 bg-gray-100 dark:bg-slate-800 rounded-xl" />
      <div class="h-11 bg-gray-100 dark:bg-slate-800 rounded-xl" />
      <div v-for="i in 5" :key="i" class="h-14 bg-gray-100 dark:bg-slate-800 rounded-xl" />
    </div>

    <template v-else>

      <!-- Filters -->
      <div class="flex items-center gap-2 mb-5 flex-wrap">
        <button
          v-for="f in (['all','active','upcoming','ended'] as Filter[])"
          :key="f"
          class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all"
          :class="filter === f
            ? 'bg-blue-600 text-white shadow-sm'
            : 'bg-white dark:bg-slate-900 border border-gray-200 dark:border-gray-700 text-slate-500 dark:text-slate-400 hover:border-blue-300'"
          @click="setFilter(f)"
        >
          {{ filterLabels[f] }}
          <span class="ml-1 opacity-60">{{ filterCount(f) }}</span>
        </button>
      </div>

      <!-- Next upcoming banner with realtime countdown -->
      <Transition name="fade">
        <div
          v-if="(filter === 'all' || filter === 'upcoming') && nextUpcoming"
          class="mb-5 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/50 rounded-2xl px-5 py-4 flex items-center justify-between gap-4 flex-wrap"
        >
          <div>
            <div class="text-xs font-bold text-blue-500 dark:text-blue-400 uppercase tracking-wider mb-1">
              ⏳ Keyingi olimpiada
            </div>
            <div class="text-base font-black text-blue-800 dark:text-blue-200">{{ nextUpcoming.name_uz }}</div>
            <div class="text-xs text-blue-400 mt-0.5">{{ fmtDate(nextUpcoming.starts_at) }}</div>
          </div>
          <div class="text-right">
            <div class="text-xs text-blue-400 mb-0.5">Boshlanishiga</div>
            <div class="text-2xl font-black tabular-nums text-blue-700 dark:text-blue-300">
              {{ fmtCountdown(nextUpcoming.starts_at) || '—' }}
            </div>
          </div>
        </div>
      </Transition>

      <!-- Empty -->
      <div
        v-if="visibleSections.every(s => listOf(s).length === 0)"
        class="py-20 text-center"
      >
        <div class="w-14 h-14 bg-yellow-50 dark:bg-yellow-900/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
          <Trophy class="w-7 h-7 text-yellow-400" />
        </div>
        <p class="text-slate-500 font-semibold">Olimpiadalar yo'q</p>
      </div>

      <!-- Sections -->
      <div v-for="s in visibleSections" :key="s" class="mb-6">
        <template v-if="listOf(s).length">

          <!-- Section label -->
          <div class="flex items-center gap-2 mb-2">
            <span class="w-2 h-2 rounded-full" :class="sectionConf[s].dotCls" />
            <span class="text-xs font-black text-slate-600 dark:text-slate-300 uppercase tracking-wider">
              {{ sectionConf[s].label }}
            </span>
            <span class="px-2 py-0.5 rounded-lg text-xs font-bold" :class="sectionConf[s].countCls">
              {{ listOf(s).length }}
            </span>
          </div>

          <!-- Table card -->
          <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 overflow-hidden shadow-sm">
            <table class="w-full text-sm border-collapse">
              <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/70 dark:bg-slate-800/40">
                  <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Nomi</th>
                  <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider hidden sm:table-cell">Turi</th>
                  <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider hidden md:table-cell">Savollar</th>
                  <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider hidden md:table-cell">Vaqt</th>
                  <th class="text-left px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider hidden sm:table-cell">Ishtirokchi</th>
                  <th class="text-right px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">
                    {{ s === 'active' ? 'Tugashiga' : s === 'upcoming' ? 'Boshlanishiga' : 'Tugagan' }}
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50 dark:divide-gray-800/50">
                <tr
                  v-for="o in pagedList(s)"
                  :key="o.id"
                  class="hover:bg-gray-50 dark:hover:bg-slate-800/30 transition-colors cursor-pointer"
                  @click="$router.push(`/olympiads/${o.id}`)"
                >
                  <td class="px-4 py-3">
                    <div class="font-bold text-slate-800 dark:text-white text-sm leading-snug">{{ o.name_uz }}</div>
                    <div
                      v-if="o.description_uz"
                      class="text-xs text-slate-400 mt-0.5 line-clamp-1"
                      style="max-width: 200px"
                      v-html="o.description_uz"
                    />
                  </td>
                  <td class="px-4 py-3 hidden sm:table-cell">
                    <span
                      class="inline-flex items-center gap-1 text-xs font-bold px-2 py-1 rounded-lg"
                      :class="o.type === 'public'
                        ? 'bg-sky-50 dark:bg-sky-900/20 text-sky-600 dark:text-sky-400'
                        : 'bg-violet-50 dark:bg-violet-900/20 text-violet-600 dark:text-violet-400'"
                    >
                      <Globe v-if="o.type === 'public'" class="w-3 h-3" />
                      <Lock v-else class="w-3 h-3" />
                      {{ o.type === 'public' ? 'Ochiq' : 'Yopiq' }}
                    </span>
                  </td>
                  <td class="px-4 py-3 hidden md:table-cell">
                    <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold">{{ o.questions_count }} ta</span>
                  </td>
                  <td class="px-4 py-3 hidden md:table-cell">
                    <span class="inline-flex items-center gap-1 text-xs text-slate-500 dark:text-slate-400 font-semibold">
                      <Clock v-if="o.duration" class="w-3 h-3" />
                      {{ o.duration ? `${o.duration} daq` : '—' }}
                    </span>
                  </td>
                  <td class="px-4 py-3 hidden sm:table-cell">
                    <span class="inline-flex items-center gap-1 text-xs text-slate-500 dark:text-slate-400 font-semibold">
                      <Users class="w-3 h-3" />
                      {{ o.participants_count.toLocaleString() }}
                    </span>
                  </td>
                  <!-- ✅ Realtime timer — now reaktiv, har soniyada yangilanadi -->
                  <td class="px-4 py-3 text-right">
                    <span
                      v-if="s === 'active'"
                      class="text-xs font-black tabular-nums"
                      :class="fmtCountdown(o.ends_at) ? 'text-red-500 dark:text-red-400' : 'text-slate-400'"
                    >
                      {{ fmtCountdown(o.ends_at) || '—' }}
                    </span>
                    <span
                      v-else-if="s === 'upcoming'"
                      class="text-xs font-black tabular-nums text-blue-600 dark:text-blue-400"
                    >
                      {{ fmtCountdown(o.starts_at) || '—' }}
                    </span>
                    <span v-else class="text-xs text-slate-400 tabular-nums">
                      {{ fmtDate(o.ends_at) }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- ─── Pagination footer ────────────────────────────── -->
            <div class="border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-slate-800/20 px-4 py-2.5 flex items-center justify-between gap-3 flex-wrap">
              <!-- Info + per-page -->
              <div class="flex items-center gap-4 flex-wrap">
                <span class="text-xs text-slate-400">
                  {{ (safePageOf(s) - 1) * perPage + 1 }}–{{ Math.min(safePageOf(s) * perPage, listOf(s).length) }}
                  / {{ listOf(s).length }} ta
                </span>
                <div class="flex items-center gap-1.5">
                  <span class="text-xs text-slate-400">Ko'rsatish:</span>
                  <select
                    :value="perPage"
                    class="text-xs border border-gray-200 dark:border-gray-700 rounded-lg px-2 py-1 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 cursor-pointer"
                    @change="setPerPage(+($event.target as HTMLSelectElement).value)"
                  >
                    <option v-for="n in PER_PAGE_OPTIONS" :key="n" :value="n">{{ n }}</option>
                  </select>
                </div>
              </div>

              <!-- Page buttons -->
              <div v-if="pageCount(s) > 1" class="flex items-center gap-1">
                <button
                  class="w-7 h-7 rounded-lg border border-gray-200 dark:border-gray-700 text-xs text-slate-500 hover:bg-gray-100 dark:hover:bg-slate-800 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
                  :disabled="safePageOf(s) <= 1"
                  @click="goPage(s, safePageOf(s) - 1)"
                >‹</button>

                <template v-for="(p, i) in pageNumbers(s)" :key="i">
                  <span v-if="p === '…'" class="w-7 text-center text-xs text-slate-400 select-none">…</span>
                  <button
                    v-else
                    class="w-7 h-7 rounded-lg border text-xs font-bold transition-all"
                    :class="p === safePageOf(s)
                      ? 'bg-blue-600 text-white border-blue-600 shadow-sm'
                      : 'border-gray-200 dark:border-gray-700 text-slate-500 hover:bg-gray-100 dark:hover:bg-slate-800'"
                    @click="goPage(s, p as number)"
                  >{{ p }}</button>
                </template>

                <button
                  class="w-7 h-7 rounded-lg border border-gray-200 dark:border-gray-700 text-xs text-slate-500 hover:bg-gray-100 dark:hover:bg-slate-800 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
                  :disabled="safePageOf(s) >= pageCount(s)"
                  @click="goPage(s, safePageOf(s) + 1)"
                >›</button>
              </div>
            </div>

          </div>
        </template>
      </div>

    </template>
  </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>