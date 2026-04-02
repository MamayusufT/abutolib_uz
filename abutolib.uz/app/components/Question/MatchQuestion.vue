<script setup lang="ts">
interface MatchPair { id: number; left: string; right: string; order: number }
interface Question { id: number; match_pairs?: MatchPair[] }

const props = defineProps<{
  question: Question
  value: Record<number, number>
  finished: boolean
}>()

const emit = defineEmits<{
  pair:  [payload: { leftId: number; rightId: number }]
  clear: [leftId: number]
}>()

const selectedLeft = ref<number | null>(null)

const sortedPairs = computed(() =>
  [...(props.question.match_pairs || [])].sort((a, b) => a.order - b.order)
)

const shuffledRight = computed(() => {
  const pairs = [...sortedPairs.value]
  for (let i = pairs.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [pairs[i], pairs[j]] = [pairs[j], pairs[i]]
  }
  return pairs
})

const stableRight = ref<MatchPair[]>([])
watch(() => props.question.id, () => {
  stableRight.value = [...(props.question.match_pairs || [])].sort(() => Math.random() - 0.5)
}, { immediate: true })

function getMatchedRight(leftId: number): MatchPair | undefined {
  const rightId = props.value[leftId]
  return stableRight.value.find(p => p.id === rightId)
}

function isRightUsed(rightId: number): boolean {
  return Object.values(props.value).includes(rightId)
}

function handleLeftClick(leftId: number) {
  if (props.finished) return
  selectedLeft.value = selectedLeft.value === leftId ? null : leftId
}

function handleRightClick(rightId: number) {
  if (props.finished || selectedLeft.value === null) return
  emit('pair', { leftId: selectedLeft.value, rightId })
  selectedLeft.value = null
}

function handleClear(leftId: number) {
  if (props.finished) return
  emit('clear', leftId)
  if (selectedLeft.value === leftId) selectedLeft.value = null
}

const matchedCount = computed(() => Object.keys(props.value).length)
const totalCount   = computed(() => sortedPairs.value.length)
</script>

<template>
  <div>
    <div class="flex items-center justify-between mb-4">
      <p class="flex items-center gap-1.5 text-xs text-teal-600 dark:text-teal-400 font-semibold bg-teal-50 dark:bg-teal-900/20 px-3 py-2 rounded-lg border border-teal-100 dark:border-teal-800 w-fit">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
        </svg>
        Chap va o'ng tomonni moslang
      </p>
      <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400">
        {{ matchedCount }} / {{ totalCount }}
      </span>
    </div>

    <div v-if="selectedLeft !== null" class="mb-3 px-3 py-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl text-xs font-semibold text-amber-700 dark:text-amber-400 flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672L13.684 16.6m0 0l-2.51 2.225.569-9.47 5.227 7.917-3.286-.672zm-7.518-.267A8.25 8.25 0 1120.25 10.5M8.288 14.212A5.25 5.25 0 1117.25 10.5"/>
      </svg>
      Endi o'ng tomondagi mos variantni tanlang
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <div class="space-y-2">
        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 px-1">Chap tomon</div>
        <div
          v-for="pair in sortedPairs"
          :key="`left-${pair.id}`"
          class="relative flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all duration-200 select-none text-sm font-semibold group"
          :class="selectedLeft === pair.id
            ? 'border-teal-500 bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300 shadow-md shadow-teal-200/50 dark:shadow-teal-900/20 scale-[1.01]'
            : value[pair.id] !== undefined
              ? 'border-teal-300 dark:border-teal-700 bg-teal-50/60 dark:bg-teal-900/10 text-slate-700 dark:text-slate-300'
              : 'border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300 hover:border-teal-300'"
          @click="handleLeftClick(pair.id)">
          <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-black flex-shrink-0 transition-all"
            :class="selectedLeft === pair.id
              ? 'bg-teal-600 text-white'
              : value[pair.id] !== undefined
                ? 'bg-teal-100 dark:bg-teal-900/40 text-teal-600 dark:text-teal-400'
                : 'bg-white dark:bg-slate-800 text-slate-400 border border-gray-200 dark:border-gray-700'">
            {{ ['A','B','C','D','E','F','G','H'][sortedPairs.indexOf(pair)] }}
          </span>
          <span class="flex-1 leading-snug">{{ pair.left }}</span>
          <button
            v-if="value[pair.id] !== undefined && !props.finished"
            class="w-5 h-5 rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-500 flex items-center justify-center hover:bg-rose-200 transition-colors flex-shrink-0 opacity-0 group-hover:opacity-100"
            @click.stop="handleClear(pair.id)">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <div class="space-y-2">
        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 px-1">O'ng tomon</div>
        <div
          v-for="pair in stableRight"
          :key="`right-${pair.id}`"
          class="flex items-center gap-3 p-3.5 rounded-xl border-2 transition-all duration-200 select-none text-sm font-semibold"
          :class="isRightUsed(pair.id)
            ? 'border-teal-300 dark:border-teal-700 bg-teal-50/60 dark:bg-teal-900/10 text-slate-500 dark:text-slate-400 cursor-default'
            : selectedLeft !== null
              ? 'border-teal-200 dark:border-teal-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 cursor-pointer hover:border-teal-500 hover:bg-teal-50 dark:hover:bg-teal-900/20 hover:scale-[1.01] hover:shadow-md hover:shadow-teal-200/50 dark:hover:shadow-teal-900/20'
              : 'border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300 cursor-pointer'">
          <span class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 transition-all"
            :class="isRightUsed(pair.id)
              ? 'bg-teal-100 dark:bg-teal-900/40'
              : 'bg-white dark:bg-slate-800 border border-gray-200 dark:border-gray-700'">
            <svg v-if="isRightUsed(pair.id)" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
            </svg>
            <svg v-else-if="selectedLeft !== null" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-teal-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
            </svg>
          </span>
          <span class="leading-snug">{{ pair.right }}</span>
        </div>
      </div>
    </div>

    <div v-if="Object.keys(props.value).length > 0" class="mt-5 pt-4 border-t border-gray-100 dark:border-slate-800">
      <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Moslashtirilganlar</div>
      <div class="space-y-2">
        <div
          v-for="pair in sortedPairs.filter(p => props.value[p.id] !== undefined)"
          :key="`matched-${pair.id}`"
          class="flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-400 bg-teal-50 dark:bg-teal-900/10 border border-teal-100 dark:border-teal-900 rounded-lg px-3 py-2">
          <span class="text-teal-700 dark:text-teal-400 flex-1">{{ pair.left }}</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-teal-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
          </svg>
          <span class="text-teal-700 dark:text-teal-400 flex-1 text-right">
            {{ stableRight.find(r => r.id === props.value[pair.id])?.right }}
          </span>
          <button v-if="!props.finished" class="ml-1 text-rose-400 hover:text-rose-600 transition-colors" @click="handleClear(pair.id)">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>