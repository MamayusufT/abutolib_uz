<template>
  <div class="min-h-screen bg-gray-950 text-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

      <!-- Header -->
      <div class="flex items-center gap-3 mb-8">
        <div class="p-2.5 bg-orange-900/40 border border-orange-800 rounded-xl">
          <TrendingUp :size="22" class="text-orange-400" />
        </div>
        <div>
          <h1 class="text-2xl font-bold text-white">Trendda</h1>
          <p class="text-gray-500 text-sm mt-0.5">Eng ko'p ko'rilgan kitoblar</p>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <div v-for="i in 10" :key="i" class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden animate-pulse">
          <div class="aspect-[3/4] bg-gray-800" />
          <div class="p-3 space-y-2">
            <div class="h-4 bg-gray-800 rounded w-3/4" />
            <div class="h-3 bg-gray-800 rounded w-1/2" />
          </div>
        </div>
      </div>

      <!-- Grid -->
      <div v-else-if="books.length" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <div v-for="(book, index) in books" :key="book.id" class="relative">
          <!-- Rank badge -->
          <div
            :class="[
              'absolute -top-2 -left-2 z-10 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shadow-lg',
              index === 0 ? 'bg-amber-400 text-amber-900' :
              index === 1 ? 'bg-gray-300 text-gray-800' :
              index === 2 ? 'bg-orange-700 text-orange-100' :
              'bg-gray-800 text-gray-400 border border-gray-700'
            ]"
          >
            {{ index + 1 }}
          </div>
          <BookCard :book="book" @wishlist-toggle="toggleWishlist" />
        </div>
      </div>

      <!-- Empty -->
      <div v-else class="flex flex-col items-center justify-center py-24">
        <TrendingUp :size="48" class="text-gray-700 mb-4" />
        <p class="text-gray-400 text-lg font-medium">Trend kitoblar topilmadi</p>
      </div>

    </div>
  </div>
</template>

<script setup>
import { TrendingUp } from 'lucide-vue-next'

const { $api } = useApi()

const books = ref([])
const loading = ref(true)

async function fetchTrending() {
  loading.value = true
  try {
    const data = await $api('/books/trending/all')
    books.value = data.data || data
  } catch {}
  loading.value = false
}

async function toggleWishlist(book) {
  try {
    const check = await $api(`/wishlists/${book.id}/check`)
    if (check.in_wishlist) {
      await $api(`/wishlists/${book.id}`, { method: 'DELETE' })
    } else {
      await $api('/wishlists', { method: 'POST', body: { book_id: book.id } })
    }
  } catch {}
}

onMounted(fetchTrending)
</script>