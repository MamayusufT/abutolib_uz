<script setup lang="ts">
interface Answer { id: number; answer: string }
interface Question { id: number; answers: Answer[] }

const props = defineProps<{
  question: Question
  selected: number[]
  finished: boolean
}>()

const emit = defineEmits<{ select: [answerId: number] }>()

const letters = ['A', 'B', 'C', 'D', 'E', 'F']
const isSelected = (id: number) => props.selected.includes(id)
</script>

<template>
  <div>
    <p class="flex items-center gap-1.5 text-xs text-violet-600 dark:text-violet-400 font-semibold mb-3 bg-violet-50 dark:bg-violet-900/20 px-3 py-2 rounded-lg border border-violet-100 dark:border-violet-800 w-fit">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
      </svg>
      Bir nechta to'g'ri javob tanlang
    </p>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
      <button
        v-for="(answer, aIndex) in question.answers"
        :key="answer.id"
        class="flex items-center gap-3 p-4 rounded-xl border-2 text-left transition-all duration-200 font-semibold text-sm group"
        :class="isSelected(answer.id)
          ? 'border-violet-500 bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300 shadow-sm shadow-violet-200 dark:shadow-violet-900/20'
          : 'border-gray-100 dark:border-slate-700 hover:border-violet-300 dark:hover:border-violet-700 text-slate-700 dark:text-slate-300 bg-gray-50 dark:bg-slate-800/50 hover:bg-violet-50/50 dark:hover:bg-violet-900/10'"
        @click="!finished && emit('select', answer.id)">
        <span class="w-7 h-7 rounded-md flex items-center justify-center flex-shrink-0 border-2 transition-all"
          :class="isSelected(answer.id)
            ? 'bg-violet-600 border-violet-600 text-white shadow-sm'
            : 'bg-white dark:bg-slate-800 border-gray-300 dark:border-gray-600 text-slate-500 group-hover:border-violet-300'">
          <svg v-if="isSelected(answer.id)" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
          </svg>
          <span v-else class="text-xs font-black">{{ letters[aIndex] }}</span>
        </span>
        <span class="leading-snug">{{ answer.answer }}</span>
      </button>
    </div>
  </div>
</template>