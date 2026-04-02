<script setup lang="ts">
defineProps<{
  title: string
  value: string | number
  desc: string
  color: string
  icon: any
}>()
</script>

<template>
  <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5 flex items-center gap-4">
    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" :class="color">
      <component :is="icon" class="w-6 h-6" />
    </div>
    <div>
      <div class="text-2xl font-black text-slate-800 dark:text-white">{{ value }}</div>
      <div class="text-sm font-semibold text-slate-500 dark:text-slate-400">{{ title }}</div>
      <div class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">{{ desc }}</div>
    </div>
  </div>
</template>