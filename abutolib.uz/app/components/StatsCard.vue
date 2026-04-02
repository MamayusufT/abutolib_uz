<template>
  <UCard class="relative overflow-hidden group hover:shadow-md transition-all duration-200">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ label }}</p>
        <p class="text-3xl font-bold mt-1.5 text-gray-900 dark:text-white tabular-nums">
          <slot name="value">{{ value }}</slot>
        </p>
        <p v-if="sub" class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ sub }}</p>
      </div>
      <div
        class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0"
        :class="iconBg"
      >
        <UIcon :name="icon" class="w-6 h-6" :class="iconColor" />
      </div>
    </div>
    <div
      class="absolute bottom-0 left-0 h-0.5 w-0 group-hover:w-full transition-all duration-500 ease-out"
      :class="barColor"
    />
  </UCard>
</template>

<script setup lang="ts">
defineProps<{
  label: string
  value?: number | string
  sub?: string
  icon: string
  iconBg?: string
  iconColor?: string
  barColor?: string
}>()
</script>