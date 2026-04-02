<template>
  <div class="min-h-screen text-slate-900 bg-slate-50 dark:bg-slate-950 dark:text-slate-100 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
            Kutubxona
          </h1>
          <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm font-medium">
            <span class="text-blue-600 dark:text-blue-400">{{ total }}</span> ta kitob topildi
          </p>
        </div>
        
        <NuxtLink
          v-if="isAdmin"
          to="/books/create"
          class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-lg shadow-blue-200 dark:shadow-none transition-all active:scale-95"
        >
          <Plus :size="18" />
          Kitob qo'shish
        </NuxtLink>
      </div>

      <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 mb-8 space-y-4 md:space-y-0">
        <div class="flex flex-wrap gap-3">
          <div class="relative flex-1 min-w-[280px]">
            <Search :size="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
            <input
              v-model="search"
              type="text"
              placeholder="Kitob nomini yoki muallifni qidiring..."
              class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all"
              @input="debouncedFetch"
            />
          </div>

          <div class="flex gap-2 w-full md:w-auto">
            <select
              v-model="filters.language"
              class="flex-1 md:w-40 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-blue-500 cursor-pointer"
              @change="fetchBooks"
            >
              <option value="">Barcha tillar</option>
              <option value="uz">O'zbekcha</option>
              <option value="ru">Ruscha</option>
              <option value="en">Inglizcha</option>
            </select>

            <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-800 rounded-xl p-1 border border-slate-200 dark:border-slate-700">
              <button
                :class="['p-2 rounded-lg transition-all', viewMode === 'grid' ? 'bg-white dark:bg-slate-700 shadow-sm text-blue-600 dark:text-blue-400' : 'text-slate-500 hover:text-slate-700']"
                @click="viewMode = 'grid'"
              >
                <LayoutGrid :size="18" />
              </button>
              <button
                :class="['p-2 rounded-lg transition-all', viewMode === 'list' ? 'bg-white dark:bg-slate-700 shadow-sm text-blue-600 dark:text-blue-400' : 'text-slate-500 hover:text-slate-700']"
                @click="viewMode = 'list'"
              >
                <List :size="18" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="flex overflow-x-auto no-scrollbar gap-2 mb-8 pb-2">
        <button
          v-for="tab in tabs"
          :key="tab.key"
          :class="['px-6 py-2.5 rounded-full text-sm font-semibold whitespace-nowrap transition-all border', 
            activeTab === tab.key 
              ? 'bg-blue-600 border-blue-600 text-white shadow-md shadow-blue-100 dark:shadow-none' 
              : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:border-blue-300']"
          @click="switchTab(tab.key)"
        >
          {{ tab.label }}
        </button>
      </div>

      <div v-if="loading" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
        <div v-for="i in 10" :key="i" class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden border border-slate-100 dark:border-slate-800 animate-pulse">
          <div class="aspect-[3/4] bg-slate-200 dark:bg-slate-800" />
          <div class="p-4 space-y-3">
            <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-5/6" />
            <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-2/3" />
          </div>
        </div>
      </div>

      <div v-else-if="books.length > 0">
        <div v-if="viewMode === 'grid'" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
          <BookCard
            v-for="book in books"
            :key="book.id"
            :book="book"
            class="hover:-translate-y-1 transition-transform duration-300"
            @wishlist-toggle="toggleWishlist"
          />
        </div>
        <div v-else class="space-y-4">
          <BookListItem
            v-for="book in books"
            :key="book.id"
            :book="book"
            @wishlist-toggle="toggleWishlist"
          />
        </div>
      </div>

      <div v-else class="flex flex-col items-center justify-center py-32 text-center bg-white dark:bg-slate-900 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700">
        <div class="w-20 h-20 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center mb-6">
          <BookOpen :size="40" class="text-blue-500" />
        </div>
        <h3 class="text-xl font-bold text-slate-800 dark:text-white">Kitoblar topilmadi</h3>
        <p class="text-slate-500 dark:text-slate-400 mt-2 max-w-xs mx-auto">
          Qidiruv shartlarini o'zgartiring yoki boshqa bo'limni ko'ring.
        </p>
      </div>

      <div v-if="lastPage > 1" class="flex items-center justify-center gap-3 mt-16">
        <button
          :disabled="currentPage === 1"
          class="p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-blue-50 disabled:opacity-30 transition-all"
          @click="changePage(currentPage - 1)"
        >
          <ChevronLeft :size="20" />
        </button>

        <div class="hidden sm:flex items-center gap-2">
           <button
            v-for="page in visiblePages"
            :key="page"
            :class="['w-10 h-10 rounded-xl text-sm font-bold transition-all', 
              page === currentPage 
                ? 'bg-blue-600 text-white shadow-lg shadow-blue-200 dark:shadow-none' 
                : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:border-blue-500']"
            @click="changePage(page)"
          >
            {{ page }}
          </button>
        </div>

        <button
          :disabled="currentPage === lastPage"
          class="p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-blue-50 disabled:opacity-30 transition-all"
          @click="changePage(currentPage + 1)"
        >
          <ChevronRight :size="20" />
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { 
  Search, Plus, LayoutGrid, List, BookOpen, 
  ChevronLeft, ChevronRight 
} from 'lucide-vue-next'

useSeoMeta({
  title: 'Kutubxona - Abutolib platformasi',
  ogTitle: 'Kutubxona - Barcha turdagi kitoblar to\'plami',
  description: 'Abutolib platformasida eng sara o\'quv qo\'llanmalari va badiiy adabiyotlarni onlayn mutolaa qiling.',
  ogDescription: 'Abutolib platformasida eng sara o\'quv qo\'llanmalari va test to\'plamlari.',
  ogImage: 'https://abutolib.uz/logo.png',
  twitterCard: 'summary_large_image',
})

const { $api } = useApi()
const authStore = useAuthStore()

const isAdmin = computed(() => authStore.user?.role === 'admin')
const books = ref([])
const total = ref(0)
const currentPage = ref(1)
const lastPage = ref(1)
const loading = ref(false)
const search = ref('')
const viewMode = ref('grid')
const activeTab = ref('all')

const filters = reactive({
  language: '',
  status: '',
})

const tabs = [
  { key: 'all', label: 'Barcha kitoblar' },
  { key: 'trending', label: 'Trenddagilar' },
  { key: 'top-rated', label: 'Eng yuqori baholangan' },
]

const visiblePages = computed(() => {
  const pages = []
  const start = Math.max(1, currentPage.value - 2)
  const end = Math.min(lastPage.value, start + 4)
  for (let i = start; i <= end; i++) pages.push(i)
  return pages
})

async function fetchBooks() {
  loading.value = true
  try {
    let url = '/books'
    if (activeTab.value === 'trending') url = '/books/trending/all'
    else if (activeTab.value === 'top-rated') url = '/books/top-rated/all'

    const params = {
      page: currentPage.value,
      ...(search.value && { search: search.value }),
      ...(filters.language && { language: filters.language }),
      ...(filters.status && { status: filters.status }),
    }

    const data = await $api(url, { params })
    books.value = data.data || data
    total.value = data.total || (Array.isArray(data) ? data.length : 0)
    lastPage.value = data.last_page || 1
  } catch (e) {
  } finally {
    loading.value = false
  }
}

function switchTab(tab) {
  activeTab.value = tab
  currentPage.value = 1
  fetchBooks()
}

function changePage(page) {
  if(page < 1 || page > lastPage.value) return
  currentPage.value = page
  fetchBooks()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

async function toggleWishlist(book) {
  try {
    const check = await $api(`/wishlists/${book.id}/check`)
    if (check.in_wishlist) {
      await $api(`/wishlists/${book.id}`, { method: 'DELETE' })
    } else {
      await $api('/wishlists', { method: 'POST', body: { book_id: book.id } })
    }
    fetchBooks()
  } catch(err) {
  }
}

let debounceTimer
function debouncedFetch() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    currentPage.value = 1
    fetchBooks()
  }, 400)
}

onMounted(fetchBooks)
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>