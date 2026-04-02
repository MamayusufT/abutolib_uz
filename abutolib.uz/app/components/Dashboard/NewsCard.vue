<script setup lang="ts">
defineProps<{
  item: {
    id: number
    title: string
    slug: string
    excerpt: string | null
    image: string | null
    category: string | null
    views: number
    published_at: string
    author: { name: string }
  }
}>()

function formatDate(str: string) {
  return new Date(str).toLocaleDateString('uz-UZ', {
    day: 'numeric', month: 'short', year: 'numeric',
  })
}
</script>

<template>
  <NuxtLink
    :to="`/news/${item.slug}`"
    class="group flex gap-4 p-4 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors"
  >
    <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-100 dark:bg-slate-800 shrink-0">
      <img v-if="item.image" :src="item.image" :alt="item.title" class="w-full h-1/2 object-cover group-hover:scale-105 transition-transform duration-300" />
      <div v-else class="w-full h-full flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
        </svg>
      </div>
    </div>
    <div class="flex-1 min-w-0">
      <div class="flex items-center gap-2 mb-1 flex-wrap">
        <span v-if="item.category" class="px-2 py-0.5 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg text-[10px] font-bold uppercase">
          {{ item.category }}
        </span>
        <span class="text-xs text-slate-400">{{ formatDate(item.published_at) }}</span>
      </div>
      <h3 class="text-sm font-bold text-slate-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2 leading-snug">
        {{ item.title }}
      </h3>
      <div class="flex items-center gap-1 mt-1.5 text-xs text-slate-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        {{ item.views }}
      </div>
    </div>
  </NuxtLink>
</template>