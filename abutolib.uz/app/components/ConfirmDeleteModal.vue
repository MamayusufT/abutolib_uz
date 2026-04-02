<template>
  <UModal :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)" :ui="{ container: 'items-center' }">
    <UCard :ui="{ ring: '', divide: 'divide-y divide-gray-100 dark:divide-gray-800' }">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
            <UIcon name="i-heroicons-exclamation-triangle" class="w-5 h-5 text-red-600 dark:text-red-400" />
          </div>
          <div>
            <h3 class="font-semibold text-gray-900 dark:text-white text-base">{{ title }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Bu amalni ortga qaytarib bo'lmaydi</p>
          </div>
        </div>
      </template>

      <div class="py-3">
        <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">
          <span v-if="itemName" class="font-bold text-gray-900 dark:text-white">« {{ itemName }} »</span>
          {{ description }}
        </p>
        <div
          v-if="warning"
          class="mt-3 flex items-start gap-2 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800"
        >
          <UIcon name="i-heroicons-information-circle" class="w-4 h-4 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" />
          <p class="text-xs text-amber-700 dark:text-amber-300">{{ warning }}</p>
        </div>
      </div>

      <template #footer>
        <div class="flex justify-end gap-3">
          <UButton
            color="gray"
            variant="outline"
            @click="$emit('update:modelValue', false)"
            :disabled="loading"
          >
            Bekor qilish
          </UButton>
          <UButton
            color="red"
            :loading="loading"
            icon="i-heroicons-trash"
            @click="$emit('confirm')"
          >
            Ha, o'chirish
          </UButton>
        </div>
      </template>
    </UCard>
  </UModal>
</template>

<script setup lang="ts">
defineProps<{
  modelValue: boolean
  title?: string
  description?: string
  itemName?: string
  warning?: string
  loading?: boolean
}>()

defineEmits<{
  'update:modelValue': [value: boolean]
  confirm: []
}>()
</script>