<script setup lang="ts">
import { ref, watch } from 'vue'

interface Question { id: number }

const props = defineProps<{
  question: Question
  value: string
  finished: boolean
  maxLength?: number
}>()

const emit = defineEmits<{ input: [text: string] }>()

const valueLocal = ref(props.value)

watch(valueLocal, (newVal) => {
  emit('input', newVal)
})

watch(() => props.value, (newVal) => {
  valueLocal.value = newVal
})

const maxLength = props.maxLength ?? 500
const warningThreshold = 50
</script>

<template>
  <div>
    <p class="flex items-center gap-1.5 text-xs text-orange-600 dark:text-orange-400 font-semibold mb-3 bg-orange-50 dark:bg-orange-900/20 px-3 py-2 rounded-lg border border-orange-100 dark:border-orange-800 w-fit">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
      </svg>
      Javobingizni yozing
    </p>

    <textarea
      v-model="valueLocal"
      :disabled="finished"
      :maxlength="maxLength"
      rows="5"
      placeholder="Javobingizni shu yerga yozing..."
      class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-slate-800 dark:text-white text-sm font-medium resize-none focus:outline-none focus:border-orange-400 dark:focus:border-orange-500 transition-colors placeholder:text-slate-300 dark:placeholder:text-slate-600"
    />

    <div class="flex justify-end mt-1.5">
      <span class="text-xs font-semibold"
            :class="valueLocal.length > maxLength - warningThreshold 
                     ? 'text-red-500' 
                     : valueLocal.length > 0 
                     ? 'text-orange-500' 
                     : 'text-slate-300'">
        {{ valueLocal.length }} / {{ maxLength }} ta belgi
      </span>
    </div>
  </div>
</template>