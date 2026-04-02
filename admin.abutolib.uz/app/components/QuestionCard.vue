<template>
  <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
    <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
      <span class="w-6 h-6 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-xs font-semibold text-primary-600 dark:text-primary-400">
        {{ index + 1 }}
      </span>
      <span class="flex-1 text-xs font-medium text-gray-500 dark:text-gray-400">Question {{ index + 1 }}</span>

      <select
        v-model="q.type"
        class="text-xs px-2 py-1 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-600 dark:text-gray-400"
        @change="$emit('type-change', index)"
      >
        <option value="single">Single choice</option>
        <option value="multiple">Multiple choice</option>
        <option value="open">Open ended</option>
        <option value="match">Matching</option>
      </select>

      <select
        v-model="q.difficulty"
        class="text-xs px-2 py-1 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-600 dark:text-gray-400"
      >
        <option value="easy">Easy</option>
        <option value="medium">Medium</option>
        <option value="hard">Hard</option>
      </select>

      <UButton icon="i-heroicons-trash" color="error" variant="ghost" size="xs" @click="$emit('remove', index)" />
    </div>

    <div class="p-4 space-y-4">
      <div class="space-y-2">
        <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 px-1">Question Text</p>
        <RichEditor v-model="q.question" />
      </div>

      <template v-if="q.type === 'single' || q.type === 'multiple'">
        <div class="space-y-2">
          <div class="flex items-center justify-between">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">
              Answers
              <span v-if="q.type === 'multiple'" class="normal-case font-normal ml-1">(multiple correct)</span>
            </p>
            <UButton icon="i-heroicons-plus" size="xs" color="neutral" variant="ghost" label="Add answer" @click="$emit('add-answer', index)" />
          </div>

          <div v-for="(a, ai) in q.answers" :key="ai" class="flex items-center gap-2">
            <button
              type="button"
              class="shrink-0 flex items-center justify-center transition-colors"
              :class="[
                q.type === 'single' ? 'w-5 h-5 rounded-full border-2' : 'w-5 h-5 rounded border-2',
                a.is_correct
                  ? 'border-primary-500 bg-primary-500'
                  : 'border-gray-300 dark:border-gray-600 hover:border-primary-400'
              ]"
              @click="$emit('set-correct', index, ai)"
            >
              <span v-if="a.is_correct && q.type === 'single'" class="w-2 h-2 rounded-full bg-white" />
              <UIcon v-if="a.is_correct && q.type === 'multiple'" name="i-heroicons-check" class="w-3 h-3 text-white" />
            </button>

            <UInput v-model="a.answer" :placeholder="`Answer ${ai + 1}`" size="sm" class="flex-1" />

            <UButton
              v-if="q.answers.length > 2"
              icon="i-heroicons-x-mark"
              color="neutral"
              variant="ghost"
              size="xs"
              @click="$emit('remove-answer', index, ai)"
            />
          </div>

          <p class="text-xs text-gray-400 pl-7">
            <UIcon name="i-heroicons-information-circle" class="w-3 h-3 inline mr-1" />
            {{ q.type === 'single' ? 'Click circle to mark correct answer' : 'Check all correct answers' }}
          </p>
        </div>
      </template>

      <template v-else-if="q.type === 'open'">
        <div class="rounded-lg border border-dashed border-gray-300 dark:border-gray-600 p-4 text-center">
          <UIcon name="i-heroicons-pencil-square" class="w-6 h-6 mx-auto mb-1 text-gray-400" />
          <p class="text-xs text-gray-400">Students will type their answer. Manual review required.</p>
        </div>
      </template>

      <template v-else-if="q.type === 'match'">
        <div class="space-y-2">
          <div class="flex items-center justify-between">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Match Pairs</p>
            <UButton icon="i-heroicons-plus" size="xs" color="neutral" variant="ghost" label="Add pair" @click="$emit('add-pair', index)" />
          </div>

          <div class="grid grid-cols-2 gap-2 px-1">
            <p class="text-xs text-center font-medium text-gray-500">Left Side</p>
            <p class="text-xs text-center font-medium text-gray-500">Right Side</p>
          </div>

          <div v-for="(pair, pi) in q.match_pairs" :key="pi" class="flex items-center gap-2">
            <div class="grid grid-cols-2 gap-2 flex-1">
              <UInput v-model="pair.left" placeholder="e.g. Paris" size="sm" />
              <UInput v-model="pair.right" placeholder="e.g. France" size="sm" />
            </div>
            <UButton
              v-if="q.match_pairs.length > 2"
              icon="i-heroicons-x-mark"
              color="neutral"
              variant="ghost"
              size="xs"
              @click="$emit('remove-pair', index, pi)"
            />
          </div>

          <p class="text-xs text-gray-400">
            <UIcon name="i-heroicons-information-circle" class="w-3 h-3 inline mr-1" />
            Students will drag right items to match with left items
          </p>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { QuestionForm } from '~/types/question'

defineProps<{
  q: QuestionForm
  index: number
}>()

defineEmits<{
  remove: [index: number]
  'type-change': [index: number]
  'add-answer': [qIndex: number]
  'remove-answer': [qIndex: number, aIndex: number]
  'set-correct': [qIndex: number, aIndex: number]
  'add-pair': [qIndex: number]
  'remove-pair': [qIndex: number, pIndex: number]
}>()
</script>