<script setup lang="ts">
const config = useRuntimeConfig()

interface NewsItem {
  id: number
  title: string
  slug: string
  excerpt: string
  image: string | null
  category: string | null
  views: number
  published_at: string
  author: { id: number; name: string }
}

const search         = ref('')
const activeCategory = ref('')
const page           = ref(1)
const loading        = ref(false)
const news           = ref<NewsItem[]>([])
const lastPage       = ref(1)
const categories     = ref<string[]>([])

useSeoMeta({
  title: () => activeCategory.value ? `${activeCategory.value} - Yangiliklar` : 'Soʻnggi yangiliklar va maqolalar',
  ogTitle: () => activeCategory.value ? `${activeCategory.value} - Yangiliklar` : 'Soʻnggi yangiliklar va maqolalar',
  description: 'Platformamizdagi barcha soʻnggi yangiliklar, foydali maqolalar va e’lonlar bilan tanishing.',
  ogDescription: 'Platformamizdagi barcha soʻnggi yangiliklar, foydali maqolalar va e’lonlar bilan tanishing.',
  ogImage: () => `${config.public.siteUrl}/og-news.jpg`,
  ogType: 'website',
  twitterCard: 'summary_large_image'
})

useHead({
  link: [
    { 
      rel: 'canonical', 
      href: () => activeCategory.value 
        ? `${config.public.siteUrl}/news?category=${activeCategory.value}` 
        : `${config.public.siteUrl}/news` 
    }
  ]
})

async function fetchCategories() {
  try {
    const res: any = await $fetch(`${config.public.apiBase}/news/categories`)
    categories.value = res
  } catch {}
}

async function fetchNews() {
  loading.value = true
  try {
    const params: any = { page: page.value, per_page: 9 }
    if (activeCategory.value) params.category = activeCategory.value
    if (search.value)         params.search   = search.value
    const res: any = await $fetch(`${config.public.apiBase}/news`, { params })
    news.value     = res.data
    lastPage.value = res.last_page
  } catch {} finally {
    loading.value = false
  }
}

function goPage(p: number) {
  if (p < 1 || p > lastPage.value || p === page.value) return
  page.value = p
  fetchNews()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

function setCategory(cat: string) {
  activeCategory.value = cat
  page.value = 1
  fetchNews()
}

let searchTimer: any
watch(search, () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    page.value = 1
    fetchNews()
  }, 400)
})

const paginationPages = computed(() => {
  const cur  = page.value
  const last = lastPage.value
  const set  = new Set<number>()
  set.add(1)
  set.add(last)
  for (let i = cur - 1; i <= cur + 1; i++) {
    if (i >= 1 && i <= last) set.add(i)
  }
  const sorted = Array.from(set).sort((a, b) => a - b)
  const result: (number | '...')[] = []
  sorted.forEach((p, i) => {
    if (i > 0 && p - sorted[i - 1] > 1) result.push('...')
    result.push(p)
  })
  return result
})

function formatDate(str: string) {
  return new Date(str).toLocaleDateString('uz-UZ', {
    year: 'numeric', month: 'long', day: 'numeric',
  })
}

onMounted(() => {
  fetchCategories()
  fetchNews()
})
</script>

<template>
  <div class="py-8 px-4">

    <!-- Header -->
    <div class="max-w-full mx-auto text-center mb-10">
      <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-600 rounded-2xl shadow-lg shadow-blue-200 dark:shadow-none mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
        </svg>
      </div>
      <h1 class="text-3xl font-black text-slate-800 dark:text-white">Yangiliklar</h1>
      <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">So'nggi yangiliklar va e'lonlar</p>
    </div>

    <div class="max-w-7xl mx-auto">

      <!-- Search & Filter -->
      <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <div class="relative flex-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
          </svg>
          <input
            v-model="search"
            type="text"
            placeholder="Yangilik qidirish..."
            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
          />
        </div>
        <div class="flex items-center gap-2 flex-wrap">
          <button
            class="px-3 py-2 rounded-xl text-xs font-bold transition-all"
            :class="activeCategory === '' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-slate-900 border border-gray-200 dark:border-gray-700 text-slate-600 dark:text-slate-300 hover:border-blue-400'"
            @click="setCategory('')"
          >Barchasi</button>
          <button
            v-for="cat in categories" :key="cat.category"
            class="px-3 py-2 rounded-xl text-xs font-bold transition-all"
            :class="activeCategory === cat.category ? 'bg-blue-600 text-white' : 'bg-white dark:bg-slate-900 border border-gray-200 dark:border-gray-700 text-slate-600 dark:text-slate-300 hover:border-blue-400'"
            @click="setCategory(cat.category)"
          >{{ cat.category }}</button>
        </div>
      </div>

      <!-- Skeleton -->
      <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div v-for="i in 9" :key="i" class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 overflow-hidden animate-pulse">
          <div class="aspect-video bg-gray-100 dark:bg-slate-800" />
          <div class="p-5 space-y-3">
            <div class="h-3 bg-gray-100 dark:bg-slate-800 rounded w-1/3" />
            <div class="h-4 bg-gray-100 dark:bg-slate-800 rounded w-full" />
            <div class="h-4 bg-gray-100 dark:bg-slate-800 rounded w-3/4" />
            <div class="h-3 bg-gray-100 dark:bg-slate-800 rounded w-full" />
          </div>
        </div>
      </div>

      <!-- News grid -->
      <div v-else-if="news.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <NuxtLink
          v-for="item in news" :key="item.id"
          :to="`/news/${item.slug}`"
          class="group bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 overflow-hidden"
        >
          <div class="aspect-video bg-gray-100 dark:bg-slate-800 overflow-hidden">
            <img v-if="item.image" :src="item.image" :alt="item.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
            <div v-else class="w-full h-full flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
              </svg>
            </div>
          </div>
          <div class="p-5">
            <div class="flex items-center gap-2 mb-3 flex-wrap">
              <span v-if="item.category" class="px-2.5 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg text-[10px] font-bold uppercase tracking-wide">
                {{ item.category }}
              </span>
              <span class="text-xs text-slate-400 dark:text-slate-500">{{ formatDate(item.published_at) }}</span>
            </div>
            <h3 class="text-sm font-black text-slate-800 dark:text-white leading-snug group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2 mb-2">
              {{ item.title }}
            </h3>
            <p v-if="item.excerpt" class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
              {{ item.excerpt }}
            </p>
            <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100 dark:border-gray-800">
              <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold">{{ item.author?.name }}</span>
              <span class="flex items-center gap-1 text-xs text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ item.views }}
              </span>
            </div>
          </div>
        </NuxtLink>
      </div>

      <!-- Empty -->
      <div v-else class="text-center py-20">
        <div class="w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z"/>
          </svg>
        </div>
        <p class="text-slate-500 dark:text-slate-400 font-semibold">Yangilik topilmadi</p>
        <p class="text-slate-400 dark:text-slate-500 text-sm mt-1">Boshqa kalit so'z bilan qidiring</p>
      </div>

      <!-- Pagination -->
      <div v-if="lastPage > 1 && !loading" class="flex items-center justify-center gap-1.5 mt-10">

        <!-- Prev -->
        <button
          :disabled="page === 1"
          class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-700 flex items-center justify-center text-slate-500 hover:border-blue-400 hover:text-blue-600 transition-all disabled:opacity-40 disabled:cursor-not-allowed"
          @click="goPage(page - 1)"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
          </svg>
        </button>

        <!-- Pages -->
        <template v-for="(p, i) in paginationPages" :key="i">
          <span v-if="p === '...'" class="w-9 h-9 flex items-center justify-center text-slate-400 text-sm">…</span>
          <button
            v-else
            class="w-9 h-9 rounded-xl border text-sm font-bold transition-all"
            :class="p === page
              ? 'bg-blue-600 border-blue-600 text-white'
              : 'border-gray-200 dark:border-gray-700 text-slate-600 dark:text-slate-300 hover:border-blue-400 hover:text-blue-600'"
            @click="goPage(p as number)"
          >{{ p }}</button>
        </template>

        <!-- Next -->
        <button
          :disabled="page === lastPage"
          class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-700 flex items-center justify-center text-slate-500 hover:border-blue-400 hover:text-blue-600 transition-all disabled:opacity-40 disabled:cursor-not-allowed"
          @click="goPage(page + 1)"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
          </svg>
        </button>

      </div>

      <!-- Page info -->
      <p v-if="lastPage > 1 && !loading" class="text-center text-xs text-slate-400 mt-3">
        {{ page }}-sahifa / {{ lastPage }} ta
      </p>

    </div>
  </div>
</template>