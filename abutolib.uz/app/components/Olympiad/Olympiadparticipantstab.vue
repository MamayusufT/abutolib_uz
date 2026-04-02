<script setup lang="ts">
import { Users, UserCheck, Calendar } from 'lucide-vue-next'

interface Participant {
  id: number; name: string; registered_at: string
}

const PER_PAGE = 10

const props = withDefaults(defineProps<{
  participants?: Participant[]
  formatDate:    (s: string) => string
}>(), {
  participants: () => [],
})

const page = ref(1)
const rows = computed(() => props.participants ?? [])

const paginated = computed(() => {
  const start = (page.value - 1) * PER_PAGE
  return rows.value.slice(start, start + PER_PAGE)
})

watch(() => rows.value.length, () => { page.value = 1 })
</script>

<template>
  <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">

    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
      <div>
        <h2 class="text-base font-black text-slate-800 dark:text-white">Qatnashuvchilar</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Ro'yhatdan o'tganlar</p>
      </div>
      <span class="px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-xl text-xs font-bold">
        {{ rows.length }} kishi
      </span>
    </div>

    <!-- Empty -->
    <div v-if="rows.length === 0" class="py-20 text-center">
      <Users class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3" />
      <p class="text-slate-400 font-semibold">Hali hech kim ro'yhatdan o'tmagan</p>
    </div>

    <template v-else>

      <!-- List -->
      <div class="divide-y divide-gray-50 dark:divide-gray-800/50">
        <div
          v-for="(p, index) in paginated"
          :key="p.id"
          class="flex items-center gap-4 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-slate-800/30 transition-colors"
        >
          <div class="w-8 text-center flex-shrink-0">
            <span class="text-sm font-black text-slate-400">
              {{ (page - 1) * PER_PAGE + index + 1 }}
            </span>
          </div>
          <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center flex-shrink-0">
            <span class="text-sm font-black text-white">
              {{ (p.name || '?').charAt(0).toUpperCase() }}
            </span>
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-sm font-black text-slate-800 dark:text-white truncate">{{ p.name }}</div>
            <div v-if="p.registered_at" class="flex items-center gap-1 mt-0.5 text-xs text-slate-400">
              <Calendar class="w-3 h-3" /> {{ formatDate(p.registered_at) }}
            </div>
          </div>
          <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-lg text-xs font-bold flex-shrink-0">
            <UserCheck class="w-3 h-3" /> Ro'yhatdan o'tgan
          </span>
        </div>
      </div>

      <!-- Pagination -->
      <OlympiadPagination
        :current="page"
        :total="rows.length"
        :per-page="PER_PAGE"
        @go="(p) => page = p"
      />

    </template>
  </div>
</template>