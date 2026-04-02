<script setup lang="ts">
import {
  BookOpen, Users, BarChart3, Trophy,
  ArrowRight, Newspaper
} from 'lucide-vue-next'
import DashboardStatsCard from '~/components/Dashboard/StatsCard.vue'
import DashboardNewsCard  from '~/components/Dashboard/NewsCard.vue'
import DashboardTeamCard  from '~/components/Dashboard/TeamCard.vue'

definePageMeta({ middleware: 'auth' })

const config = useRuntimeConfig()
const auth   = useAuthStore()

// Stats
const stats = [
  { title: 'Testlar',       value: '124',   desc: 'Jami ishlangan',  color: 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400',   icon: BookOpen  },
  { title: 'Ball',          value: '87%',   desc: "O'rtacha natija", color: 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400', icon: BarChart3  },
  { title: 'Reyting',       value: '#12',   desc: 'Umumiy reyting',  color: 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400', icon: Trophy },
  { title: 'Fanlar',        value: '8',     desc: "O'rganilgan",     color: 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400', icon: Users },
]

// News
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
const news        = ref<NewsItem[]>([])
const newsLoading = ref(true)

async function fetchNews() {
  try {
    const res: any = await $fetch(`${config.public.apiBase}/news`, {
      params: { per_page: 5, page: 1 },
    })
    news.value = res.data
  } catch {} finally {
    newsLoading.value = false
  }
}

// Team
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
const team        = ref<TeamMember[]>([])
const teamLoading = ref(true)

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

const greeting = computed(() => {
  const hour = new Date().getHours()

  if (hour >= 5 && hour < 12) {
    return 'Xayrli tong'
  } else if (hour >= 12 && hour < 17) {
    return 'Xayrli kun'
  } else if (hour >= 17 && hour < 22) {
    return 'Xayrli oqshom'
  } else {
    return 'Xayrli tun'
  }
})

useSeoMeta({
  title: 'Dashboard',
  description: 'Shaxsiy kabinetingizga xush kelibsiz! Bu yerda test natijalaringizni ko\'rishingiz, so\'nggi yangiliklardan xabardor bo\'lishingiz va bizning jamoamiz bilan tanishishingiz mumkin.',
})
</script>

<template>
  <div class="max-w-7xl mx-auto p-5 space-y-6">

    <!-- Welcome banner -->
    <div class="relative bg-blue-600 rounded-2xl overflow-hidden">
      <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full -translate-y-1/2 translate-x-1/3" />
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white rounded-full translate-y-1/2 -translate-x-1/4" />
      </div>
      <div class="relative px-6 py-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <p class="text-blue-200 text-sm font-semibold mb-1">{{ greeting }},</p>
          <h1 class="text-2xl sm:text-3xl font-black text-white">{{ auth.user?.name }} 👋</h1>
          <p class="text-blue-100 text-sm mt-1">{{ auth.user?.email }}</p>
        </div>
        <div class="flex items-center gap-3">
          <NuxtLink to="/subjects"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-blue-600 rounded-xl font-bold text-sm hover:bg-blue-50 transition-all active:scale-95">
            <BookOpen class="w-4 h-4" />
            Test boshlash
          </NuxtLink>
        </div>
      </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <DashboardStatsCard
        v-for="stat in stats"
        :key="stat.title"
        v-bind="stat"
      />
    </div>

    <!-- News & Team -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

      <!-- Latest news -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
          <div class="flex items-center gap-2">
            <Newspaper class="w-4 h-4 text-blue-600 dark:text-blue-400" />
            <h2 class="font-black text-slate-800 dark:text-white text-sm">So'nggi yangiliklar</h2>
          </div>
          <NuxtLink to="/news" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
            Barchasi
            <ArrowRight class="w-3 h-3" />
          </NuxtLink>
        </div>

        <!-- Loading skeleton -->
        <div v-if="newsLoading" class="divide-y divide-gray-50 dark:divide-gray-800">
          <div v-for="i in 4" :key="i" class="flex gap-4 p-4 animate-pulse">
            <div class="w-16 h-16 bg-gray-100 dark:bg-slate-800 rounded-xl flex-shrink-0" />
            <div class="flex-1 space-y-2">
              <div class="h-2.5 bg-gray-100 dark:bg-slate-800 rounded w-1/4" />
              <div class="h-3 bg-gray-100 dark:bg-slate-800 rounded w-full" />
              <div class="h-3 bg-gray-100 dark:bg-slate-800 rounded w-3/4" />
            </div>
          </div>
        </div>

        <!-- News list -->
        <div v-else-if="news.length > 0" class="divide-y divide-gray-50 dark:divide-gray-800/50">
          <DashboardNewsCard v-for="item in news" :key="item.id" :item="item" />
        </div>

        <div v-else class="py-12 text-center text-slate-400 text-sm">Yangilik topilmadi</div>
      </div>

      <!-- Team -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
          <div class="flex items-center gap-2">
            <Users class="w-4 h-4 text-blue-600 dark:text-blue-400" />
            <h2 class="font-black text-slate-800 dark:text-white text-sm">Bizning jamoa</h2>
          </div>
          <NuxtLink to="/about" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
            Batafsil
            <ArrowRight class="w-3 h-3" />
          </NuxtLink>
        </div>

        <!-- Loading skeleton -->
        <div v-if="teamLoading" class="p-4 grid grid-cols-2 sm:grid-cols-3 gap-3">
          <div v-for="i in 6" :key="i" class="animate-pulse bg-gray-50 dark:bg-slate-800 rounded-2xl p-4 flex flex-col items-center gap-2">
            <div class="w-14 h-14 bg-gray-200 dark:bg-slate-700 rounded-2xl" />
            <div class="h-3 bg-gray-200 dark:bg-slate-700 rounded w-3/4" />
            <div class="h-2.5 bg-gray-100 dark:bg-slate-700/50 rounded w-1/2" />
          </div>
        </div>

        <!-- Team grid -->
        <div v-else-if="team.length > 0" class="p-4 grid grid-cols-2 sm:grid-cols-3 gap-3">
          <DashboardTeamCard
            v-for="member in team"
            :key="member.id"
            :member="member"
          />
        </div>

        <div v-else class="py-12 text-center text-slate-400 text-sm">Ma'lumot topilmadi</div>
      </div>

    </div>

    <!-- Quick links -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <NuxtLink
        v-for="link in [
          { title: 'Testlar', desc: 'Test ishlash', href: '/subjects', color: 'from-blue-500 to-blue-600' },
          { title: 'Statistika', desc: 'Natijalarim', href: '/stats', color: 'from-green-500 to-green-600' },
          { title: 'Yangiliklar', desc: 'So\'nggi xabar', href: '/news', color: 'from-purple-500 to-purple-600' },
          { title: 'Profil', desc: 'Sozlamalar', href: '/profile', color: 'from-orange-500 to-orange-600' },
        ]"
        :key="link.href"
        :to="link.href"
        class="relative overflow-hidden rounded-2xl p-5 text-white transition-all hover:-translate-y-0.5 hover:shadow-lg active:scale-95"
        :style="`background: linear-gradient(135deg, var(--tw-gradient-from), var(--tw-gradient-to))`"
        :class="`bg-gradient-to-br ${link.color}`"
      >
        <div class="font-black text-base">{{ link.title }}</div>
        <div class="text-white/70 text-xs mt-0.5">{{ link.desc }}</div>
        <ArrowRight class="absolute bottom-4 right-4 w-4 h-4 text-white/50" />
      </NuxtLink>
    </div>

  </div>
</template>
