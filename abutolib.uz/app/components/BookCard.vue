<template>
  <NuxtLink
    :to="`/books/${book.id}`"
    class="group relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden hover:border-blue-500/50 dark:hover:border-blue-400/40 transition-all duration-300 hover:-translate-y-1.5 hover:shadow-[0_20px_25px_-5px_rgba(59,130,246,0.1),0_10px_10px_-5px_rgba(59,130,246,0.04)] flex flex-col"
  >
    <div class="relative aspect-[3/4] bg-slate-100 dark:bg-slate-800 overflow-hidden">
      <img
        v-if="book.cover_image"
        :src="book.cover_image"
        :alt="book.title"
        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
      />
      <div v-else class="w-full h-full flex items-center justify-center">
        <BookOpen :size="40" class="text-slate-300 dark:text-slate-600" />
      </div>

      <div class="absolute top-3 left-3 flex flex-col gap-2 z-10">
        <div
          v-if="book.status === 'unavailable'"
          class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md text-slate-500 dark:text-slate-400 text-[10px] font-black uppercase tracking-wider px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-700"
        >
          Nofaol
        </div>

        <div
          v-if="!book.price || book.price == 0"
          class="bg-blue-600 dark:bg-blue-500 text-white text-[10px] font-black uppercase tracking-wider px-2 py-1 rounded-lg shadow-sm"
        >
          Bepul
        </div>
      </div>

      <button
        class="absolute top-3 right-3 p-2 rounded-xl bg-white/90 dark:bg-slate-900/80 backdrop-blur-md border border-slate-200 dark:border-slate-700 text-slate-400 hover:text-rose-500 dark:hover:text-rose-400 transition-all duration-300 z-20 shadow-sm"
        :class="{ 'text-rose-500 dark:text-rose-400 border-rose-100 dark:border-rose-900/30 bg-rose-50 dark:bg-rose-900/20': inWishlist }"
        @click.prevent="$emit('wishlist-toggle', book)"
      >
        <Heart :size="16" :fill="inWishlist ? 'currentColor' : 'none'" />
      </button>

      <div class="absolute inset-0 bg-gradient-to-t from-slate-900/20 to-transparent opacity-0 dark:opacity-40" />
    </div>

    <div class="p-4 flex flex-col flex-1">
      <h3 class="text-slate-800 dark:text-slate-100 text-sm font-bold leading-tight line-clamp-2 mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
        {{ book.title }}
      </h3>
      <p class="text-slate-500 dark:text-slate-400 text-[11px] font-medium mb-3">
        {{ book.author }}
      </p>

      <div class="flex items-center gap-2 mb-4">
        <div class="flex items-center gap-0.5 text-amber-400">
          <Star :size="12" fill="currentColor" />
          <span class="text-slate-700 dark:text-slate-200 text-xs font-bold ml-1">{{ book.average_rating || '0.0' }}</span>
        </div>
        <div class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-700" />
        <span class="text-slate-400 dark:text-slate-500 text-[11px]">{{ book.review_count || 0 }} fikr</span>
      </div>

      <div class="flex items-center justify-between mt-auto pt-3 border-t border-slate-100 dark:border-slate-800/60">
        <div class="text-blue-600 dark:text-blue-400 text-sm font-black tracking-tight">
          <span v-if="book.price > 0">{{ formatPrice(book.price) }}</span>
          <span v-else class="text-emerald-600 dark:text-emerald-400 uppercase text-xs">Bepul</span>
        </div>

        <div class="flex items-center gap-3 text-slate-400 dark:text-slate-500">
          <div class="flex items-center gap-1">
            <Eye :size="12" />
            <span class="text-[10px] font-bold">{{ formatNum(book.view_count) }}</span>
          </div>
          <div class="flex items-center gap-1">
            <Download :size="12" />
            <span class="text-[10px] font-bold">{{ formatNum(book.download_count) }}</span>
          </div>
        </div>
      </div>
    </div>
  </NuxtLink>
</template>

<script setup>
import { BookOpen, Heart, Star, Eye, Download } from 'lucide-vue-next'

const props = defineProps({
  book: { type: Object, required: true },
  inWishlist: { type: Boolean, default: false },
})

defineEmits(['wishlist-toggle'])

const formatNum = (n) => {
  if (!n) return '0'
  return n >= 1000 ? (n / 1000).toFixed(1) + 'k' : n
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('uz-UZ').format(price) + ' so\'m'
}
</script>