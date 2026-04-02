<script setup lang="ts">
interface Answer { id: number; answer: string }
interface Question { id: number; answers: Answer[] }

const props = defineProps<{
  question: Question
  selected?: number
  finished: boolean
  color?: string
}>()

const emit = defineEmits<{ select: [answerId: number] }>()

const letters = ['A', 'B', 'C', 'D', 'E', 'F']
</script>

<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
    <button
      v-for="(answer, aIndex) in question.answers"
      :key="answer.id"
      class="flex items-center gap-3 p-4 rounded-xl border-2 text-left transition-all duration-200 font-semibold text-sm group"
      :class="selected === answer.id
        ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 shadow-sm shadow-blue-200 dark:shadow-blue-900/20'
        : 'border-gray-100 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-700 text-slate-700 dark:text-slate-300 bg-gray-50 dark:bg-slate-800/50 hover:bg-blue-50/50 dark:hover:bg-blue-900/10'"
      @click="!finished && emit('select', answer.id)">
      <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-black flex-shrink-0 transition-all"
        :class="selected === answer.id
          ? 'bg-blue-600 text-white shadow-sm'
          : 'bg-white dark:bg-slate-800 text-slate-500 border border-gray-200 dark:border-gray-700 group-hover:border-blue-300'">
        {{ letters[aIndex] }}
      </span>
      <span class="leading-snug">{{ answer.answer }}</span>
    </button>
  </div>
</template>