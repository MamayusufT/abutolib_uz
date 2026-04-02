<script setup lang="ts">
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'

const props = defineProps<{
  current:  number
  total:    number
  perPage?: number
}>()

const emit = defineEmits<{ (e: 'go', page: number): void }>()

const perPage  = computed(() => props.perPage ?? 10)
const lastPage = computed(() => Math.max(1, Math.ceil(props.total / perPage.value)))

const pages = computed(() => {
  const cur  = props.current
  const last = lastPage.value
  const set  = new Set<number>()
  set.add(1)
  set.add(last)
  for (let i = cur - 1; i <= cur + 1; i++) {
    if (i >= 1 && i <= last) set.add(i)
  }
  const sorted = Array.from(set).sort((a, b) => a - b)
  const result: (number | '...')[] = []
  sorted.forEach((p, i) => {
    if (i > 0 && p - sorted[i - 1] > 1) result.push('...')
    result.push(p)
  })
  return result
})

function go(p: number) {
  if (p < 1 || p > lastPage.value || p === props.current) return
  emit('go', p)
}
</script>

<template>
  <div v-if="lastPage > 1" class="flex items-center justify-center gap-1.5 p-4 border-t border-gray-100 dark:border-gray-800">
    <button
      :disabled="current === 1"
      class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-700 flex items-center justify-center text-slate-500 hover:border-blue-400 hover:text-blue-600 transition-all disabled:opacity-40 disabled:cursor-not-allowed"
      @click="go(current - 1)"
    >
      <ChevronLeft class="w-4 h-4" />
    </button>

    <template v-for="(p, i) in pages" :key="i">
      <span v-if="p === '...'" class="w-9 h-9 flex items-center justify-center text-slate-400 text-sm">…</span>
      <button
        v-else
        class="w-9 h-9 rounded-xl border text-sm font-bold transition-all"
        :class="p === current
          ? 'bg-blue-600 border-blue-600 text-white'
          : 'border-gray-200 dark:border-gray-700 text-slate-600 dark:text-slate-300 hover:border-blue-400 hover:text-blue-600'"
        @click="go(p as number)"
      >
        {{ p }}
      </button>
    </template>

    <button
      :disabled="current === lastPage"
      class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-700 flex items-center justify-center text-slate-500 hover:border-blue-400 hover:text-blue-600 transition-all disabled:opacity-40 disabled:cursor-not-allowed"
      @click="go(current + 1)"
    >
      <ChevronRight class="w-4 h-4" />
    </button>
  </div>
</template>