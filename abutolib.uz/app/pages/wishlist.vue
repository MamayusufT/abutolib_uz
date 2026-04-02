<template>
  <div class="min-h-screen bg-gray-950 text-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

      <!-- Header -->
      <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-3">
          <div class="p-2.5 bg-rose-900/40 border border-rose-800 rounded-xl">
            <Heart :size="22" class="text-rose-400" />
          </div>
          <div>
            <h1 class="text-2xl font-bold text-white">Istaklar ro'yxati</h1>
            <p class="text-gray-500 text-sm mt-0.5">
              <span v-if="!loading">{{ books.length }} ta kitob saqlangan</span>
              <span v-else>Yuklanmoqda...</span>
            </p>
          </div>
        </div>

        <button
          v-if="books.length"
          class="flex items-center gap-2 text-sm text-gray-500 hover:text-rose-400 transition-colors"
          @click="clearAll"
        >
          <Trash2 :size="15" />
          Barchasini o'chirish
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <div v-for="i in 8" :key="i" class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden animate-pulse">
          <div class="aspect-[3/4] bg-gray-800" />
          <div class="p-3 space-y-2">
            <div class="h-4 bg-gray-800 rounded w-3/4" />
            <div class="h-3 bg-gray-800 rounded w-1/2" />
          </div>
        </div>
      </div>

      <!-- Grid -->
      <div v-else-if="books.length" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <div v-for="item in wishlists" :key="item.id" class="relative group/item">
          <BookCard :book="item.book" :in-wishlist="true" @wishlist-toggle="removeFromWishlist(item)" />
          <!-- Remove overlay button -->
          <button
            class="absolute top-2 right-2 p-1.5 rounded-lg bg-rose-900/80 backdrop-blur-sm text-rose-300 border border-rose-700 opacity-0 group-hover/item:opacity-100 transition-opacity z-10"
            @click.prevent="removeFromWishlist(item)"
          >
            <X :size="13" />
          </button>
        </div>
      </div>

      <!-- Empty -->
      <div v-else class="flex flex-col items-center justify-center py-24 text-center">
        <div class="relative mb-6">
          <Heart :size="56" class="text-gray-800" />
          <div class="absolute inset-0 flex items-center justify-center">
            <Plus :size="20" class="text-gray-600" />
          </div>
        </div>
        <p class="text-gray-400 text-lg font-medium">Istaklar ro'yxati bo'sh</p>
        <p class="text-gray-600 text-sm mt-1 mb-6">Yoqtirgan kitoblaringizni shu yerda saqlang</p>
        <NuxtLink
          to="/books"
          class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors"
        >
          <BookOpen :size="16" />
          Kitoblarni ko'rish
        </NuxtLink>
      </div>

    </div>
  </div>
</template>

<script setup>
import { Heart, Trash2, X, Plus, BookOpen } from 'lucide-vue-next'

definePageMeta({ middleware: 'auth' })

const { $api } = useApi()

const wishlists = ref([])
const loading = ref(true)

const books = computed(() => wishlists.value.map(w => w.book).filter(Boolean))

async function fetchWishlists() {
  loading.value = true
  try {
    const data = await $api('/wishlists')
    wishlists.value = data.data || data
  } catch {}
  loading.value = false
}

async function removeFromWishlist(item) {
  try {
    await $api(`/wishlists/${item.id}`, { method: 'DELETE' })
    wishlists.value = wishlists.value.filter(w => w.id !== item.id)
  } catch {}
}

async function clearAll() {
  if (!confirm('Barcha kitoblarni istaklar ro\'yxatidan o\'chirmoqchimisiz?')) return
  try {
    await Promise.all(wishlists.value.map(w => $api(`/wishlists/${w.id}`, { method: 'DELETE' })))
    wishlists.value = []
  } catch {}
}

onMounted(fetchWishlists)
</script>