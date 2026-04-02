<script setup lang="ts">
import { ArrowRight, BookOpen, BarChart3, Users, Trophy } from 'lucide-vue-next'
const config = useRuntimeConfig()

useSeoMeta({
  title: 'Onlayn testlar va fanlar bo\'yicha imtihonlar',
  ogTitle: 'Test Platformasi — Bilimingizni biz bilan sinang',
  description: 'Matematika, ingliz tili va boshqa fanlardan eng so‘nggi testlar to‘plami. O‘z bilimingizni onlayn tekshiring va natijalarni yaxshilang.',
  ogDescription: 'Barcha fanlardan mavzulashtirilgan onlayn testlar bazasi.',
  ogImage: `${config.public.siteUrl}/og-home.jpg`,
  ogType: 'website',
})

interface NewsItem {
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

interface TeamMember {
  id: number
  name: string
  role: string
  avatar: string | null
  email: string | null
  telegram: string | null
  instagram: string | null
  bio: string | null
}

const news        = ref<NewsItem[]>([])
const team        = ref<TeamMember[]>([])
const newsLoading = ref(true)
const teamLoading = ref(true)

async function fetchNews() {
  try {
    const res: any = await $fetch(`${config.public.apiBase}/news`, {
      params: { per_page: 4, page: 1 },
    })
    news.value = res.data
  } catch {} finally {
    newsLoading.value = false
  }
}

async function fetchTeam() {
  try {
    const res: any = await $fetch(`${config.public.apiBase}/team`)
    team.value = res.data
  } catch {} finally {
    teamLoading.value = false
  }
}

onMounted(() => {
  fetchNews()
  fetchTeam()
})

function formatDate(str: string) {
  return new Date(str).toLocaleDateString('uz-UZ', {
    day: 'numeric', month: 'short', year: 'numeric',
  })
}

const stats = [
  { value: '10K+', label: 'Foydalanuvchi', icon: Users,    color: 'text-blue-600 bg-blue-50 dark:bg-blue-900/20 dark:text-blue-400' },
  { value: '500+', label: 'Testlar',        icon: BookOpen, color: 'text-green-600 bg-green-50 dark:bg-green-900/20 dark:text-green-400' },
  { value: '20+',  label: 'Fanlar',         icon: BarChart3,color: 'text-purple-600 bg-purple-50 dark:bg-purple-900/20 dark:text-purple-400' },
  { value: '98%',  label: 'Mamnunlik',      icon: Trophy,   color: 'text-yellow-600 bg-yellow-50 dark:bg-yellow-900/20 dark:text-yellow-400' },
]
</script>

<template>
  <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">

    <!-- ═══ HERO ═══ -->
    <section class="relative overflow-hidden bg-slate-50 dark:bg-slate-950 py-24 px-4 rounded-[3rem] mb-10">
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-400/10 rounded-full blur-[100px] pointer-events-none" />
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-400/10 rounded-full blur-[100px] pointer-events-none" />
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-gradient-to-b from-transparent via-white/50 dark:via-transparent to-transparent pointer-events-none" />

    <div class="relative max-w-4xl mx-auto text-center">
      <div class="inline-flex items-center gap-2 px-5 py-2 bg-blue-50 dark:bg-blue-900/20 rounded-full text-blue-600 dark:text-blue-400 text-[11px] font-black uppercase tracking-[0.2em] mb-8 shadow-sm">
        <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse" />
        Intellektual Test Platformasi
      </div>

      <h1 class="text-4xl sm:text-6xl lg:text-7xl font-[900] text-slate-900 dark:text-white leading-[1.1] mb-6 tracking-tight">
        Bilimingizni 
        <span class="relative inline-block text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-500 dark:from-blue-400 dark:to-cyan-300">
          sinab ko'ring
          <svg class="absolute -bottom-3 left-0 w-full h-3 opacity-40" viewBox="0 0 300 12" fill="none">
            <path d="M2 10 Q75 2 150 8 Q225 14 298 6" stroke="currentColor" stroke-width="4" stroke-linecap="round" class="text-blue-400"/>
          </svg>
        </span>
      </h1>

      <p class="text-slate-500 dark:text-slate-400 text-lg sm:text-xl max-w-2xl mx-auto mb-10 leading-relaxed font-medium">
        Zamonaviy platformada barcha fanlar bo'yicha interaktiv testlar. 
        Natijalaringizni tahlil qiling va cho'qqilarni zabt eting.
      </p>

      <div class="flex flex-wrap justify-center gap-4 mb-16">
        <NuxtLink to="/register"
          class="inline-flex items-center gap-3 px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold text-base shadow-xl shadow-blue-600/25 transition-all hover:-translate-y-1 active:scale-95">
          Bepul boshlash
          <ArrowRight class="w-5 h-5" />
        </NuxtLink>
        <NuxtLink to="/subjects"
          class="inline-flex items-center gap-3 px-8 py-4 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 rounded-2xl font-bold text-base shadow-lg shadow-slate-200/50 dark:shadow-none transition-all hover:-translate-y-1 active:scale-95">
          <BookOpen class="w-5 h-5 text-blue-500" />
          Testlarni ko'rish
        </NuxtLink>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 max-w-3xl mx-auto">
        <div v-for="s in stats" :key="s.label"
          class="group bg-white/60 dark:bg-slate-900/40 rounded-[2rem] p-6 text-center backdrop-blur-md shadow-sm hover:shadow-xl hover:bg-white dark:hover:bg-slate-900 transition-all duration-500">
          <div class="w-12 h-12 rounded-2xl mx-auto mb-3 flex items-center justify-center transition-transform group-hover:scale-110 shadow-sm" :class="s.color">
            <component :is="s.icon" class="w-5 h-5" />
          </div>
          <div class="text-2xl font-black text-slate-900 dark:text-white">{{ s.value }}</div>
          <div class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-widest mt-1">{{ s.label }}</div>
        </div>
      </div>
    </div>
  </section>

    <!-- ═══ NEWS ═══ -->
    <section class="mb-10">
      <div class="flex items-center justify-between mb-5">
        <div>
          <h2 class="text-xl font-black text-slate-800 dark:text-white">So'nggi yangiliklar</h2>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Platforma haqidagi eng so'nggi xabarlar</p>
        </div>
        <NuxtLink to="/news"
          class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
          Barchasi
          <ArrowRight class="w-4 h-4" />
        </NuxtLink>
      </div>

      <!-- Skeleton -->
      <div v-if="newsLoading" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="animate-pulse bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 overflow-hidden">
          <div class="aspect-[4/3] bg-gray-100 dark:bg-slate-800" />
          <div class="p-5 space-y-3">
            <div class="h-3 bg-gray-100 dark:bg-slate-800 rounded w-1/4" />
            <div class="h-5 bg-gray-100 dark:bg-slate-800 rounded w-full" />
            <div class="h-4 bg-gray-100 dark:bg-slate-800 rounded w-3/4" />
          </div>
        </div>
        <div class="space-y-3">
          <div v-for="i in 3" :key="i" class="animate-pulse flex gap-3 bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-4">
            <div class="w-20 h-20 bg-gray-100 dark:bg-slate-800 rounded-xl flex-shrink-0" />
            <div class="flex-1 space-y-2">
              <div class="h-2.5 bg-gray-100 dark:bg-slate-800 rounded w-1/3" />
              <div class="h-4 bg-gray-100 dark:bg-slate-800 rounded w-full" />
              <div class="h-3 bg-gray-100 dark:bg-slate-800 rounded w-2/3" />
            </div>
          </div>
        </div>
      </div>

      <!-- News grid: 1 left big + 3 right small -->
      <div v-else-if="news.length > 0" class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        <!-- Left: featured news -->
        <NuxtLink
          v-if="news[0]"
          :to="`/news/${news[0].slug}`"
          class="group bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all overflow-hidden"
        >
          <div class="aspect-[4/3] bg-gray-100 dark:bg-slate-800 overflow-hidden">
            <img v-if="news[0].image" :src="news[0].image" :alt="news[0].title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
            <div v-else class="w-full h-full flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z"/>
              </svg>
            </div>
          </div>
          <div class="p-5">
            <div class="flex items-center gap-2 mb-3">
              <span v-if="news[0].category"
                class="px-2.5 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg text-[10px] font-bold uppercase tracking-wide">
                {{ news[0].category }}
              </span>
              <span class="text-xs text-slate-400">{{ formatDate(news[0].published_at) }}</span>
            </div>
            <h3 class="text-lg font-black text-slate-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors leading-snug line-clamp-2 mb-2">
              {{ news[0].title }}
            </h3>
            <p v-if="news[0].excerpt" class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
              {{ news[0].excerpt }}
            </p>
            <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100 dark:border-gray-800">
              <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ news[0].author?.name }}</span>
              <span class="flex items-center gap-1 text-xs text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ news[0].views }}
              </span>
            </div>
          </div>
        </NuxtLink>

        <!-- Right: 3 small news -->
        <div class="flex flex-col gap-3">
          <NuxtLink
            v-for="item in news.slice(1, 4)"
            :key="item.id"
            :to="`/news/${item.slug}`"
            class="group flex gap-4 bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all p-4"
          >
            <div class="w-20 h-20 rounded-xl overflow-hidden bg-gray-100 dark:bg-slate-800 flex-shrink-0">
              <img v-if="item.image" :src="item.image" :alt="item.title"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
              <div v-else class="w-full h-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z"/>
                </svg>
              </div>
            </div>
            <div class="flex-1 min-w-0 flex flex-col justify-center">
              <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                <span v-if="item.category"
                  class="px-2 py-0.5 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg text-[10px] font-bold uppercase">
                  {{ item.category }}
                </span>
                <span class="text-xs text-slate-400">{{ formatDate(item.published_at) }}</span>
              </div>
              <h3 class="text-sm font-black text-slate-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2 leading-snug">
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
        </div>

      </div>

      <div v-else class="text-center py-16 text-slate-400">Yangiliklar topilmadi</div>
    </section>

    <!-- ═══ TEAM ═══ -->
    <TeamSlider v-if="!teamLoading && team.length > 0" :team="team" />

  </div>
</template>