<template>
  <footer class="bg-white dark:bg-slate-900 border-t border-gray-100 dark:border-slate-800 mt-auto">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-y-10 gap-x-8">
        
        <div class="space-y-6">
          <NuxtLink to="/" class="flex items-center gap-3 group w-fit">
            <div class="w-10 h-10 sm:w-11 sm:h-11 bg-slate-50 dark:bg-slate-800 rounded-xl flex items-center justify-center shadow-md shadow-slate-200 dark:shadow-none transition-all duration-300 group-hover:rotate-6 group-hover:scale-110 p-1.5">
              <img 
                src="/logo.png" 
                alt="Abutolib Logo" 
                class="w-full h-full object-contain"
              />
            </div>

            <div class="leading-none flex flex-col justify-center">
              <div class="text-lg sm:text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight flex items-baseline">
                Abutolib
                <span class="text-sm font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500 dark:from-blue-400 dark:to-cyan-300 ml-0.5">
                  .uz
                </span>
              </div>
              
              <div class="text-[9px] sm:text-[10px] font-bold text-blue-600 dark:text-blue-400 tracking-[0.2em] uppercase mt-1">
                Test tizimi
              </div>
            </div>
          </NuxtLink>
          <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed max-w-xs">
            O'zbekistonning zamonaviy online test platformasi. Bilimingizni biz bilan oson va qiziqarli sinab ko'ring.
          </p>
        </div>

        <div>
          <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest mb-6 border-l-2 border-blue-600 pl-3">
            Navigatsiya
          </h3>
          <ul class="space-y-3">
            <li v-for="link in navLinks" :key="link.href">
              <NuxtLink :to="link.href" class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-all group text-nowrap">
                <span class="w-1.5 h-1.5 bg-slate-200 dark:bg-slate-700 group-hover:bg-blue-500 group-hover:w-3 rounded-full transition-all duration-300" />
                {{ link.name }}
              </NuxtLink>
            </li>
          </ul>
        </div>

        <div>
          <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest mb-6 border-l-2 border-blue-600 pl-3">
            Fanlar
          </h3>
          
          <div v-if="pending" class="space-y-3">
            <div v-for="i in 5" :key="i" class="h-4 bg-gray-100 dark:bg-slate-800 rounded w-full animate-pulse"></div>
          </div>

          <template v-else>
            <ul class="space-y-3">
              <li v-for="subject in subjects" :key="subject.id">
                <NuxtLink :to="`/subjects/${subject.slug}`" class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-all group">
                  <span class="w-2 h-2 rounded-sm transition-transform group-hover:scale-125" :style="{ backgroundColor: subject.color || '#3b82f6' }" />
                  {{ subject.name }}
                </NuxtLink>
              </li>
            </ul>

            <div v-if="totalPages > 1" class="flex items-center gap-2 mt-6">
              <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1" class="p-1.5 rounded-lg border border-gray-200 dark:border-slate-700 disabled:opacity-30 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
              </button>
              <span class="text-[10px] font-bold text-slate-500 uppercase">{{ currentPage }} / {{ totalPages }}</span>
              <button @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages" class="p-1.5 rounded-lg border border-gray-200 dark:border-slate-700 disabled:opacity-30 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
              </button>
            </div>
          </template>
        </div>

        <div>
          <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest mb-6 border-l-2 border-blue-600 pl-3">
            Aloqa
          </h3>
          <ul class="space-y-4">
            <li class="flex items-center gap-3">
              <div class="w-8 h-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center text-blue-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              </div>
              <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">info@abutolib.uz</span>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <div class="border-t border-gray-100 dark:border-slate-800 py-6">
      <div class="max-w-full mx-auto px-4 flex flex-col md:flex-row items-center justify-between gap-4 text-[11px] text-slate-500 font-medium">
        <div class="flex items-center gap-3">
          <div class="flex items-center gap-2">
            <p>© {{ new Date().getFullYear() }} Abutolib Test Tizimi.</p>
            <span class="text-slate-300 dark:text-slate-700">|</span>
            <span>Barcha huquqlar himoyalangan</span>
          </div>
          <div v-if="generationTime > 0" class="hidden sm:flex items-center gap-1.5 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 px-2 py-0.5 rounded-md border border-emerald-100 dark:border-emerald-800/30">
            <span class="relative flex h-1.5 w-1.5">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
            </span>
            <span class="font-bold tracking-tight">Generated in {{ generationTime }}ms</span>
          </div>
        </div>
        <div class="flex items-center gap-4">
          <div class="flex items-center gap-3 bg-slate-50 dark:bg-slate-800/50 px-3 py-1.5 rounded-full border border-slate-100 dark:border-slate-800">
            <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Stack:</span>
            <div class="flex items-center gap-1">
              <svg class="w-3.5 h-3.5 text-[#FF2D20]" viewBox="0 0 24 24" fill="currentColor"><path d="M6.657 18.235L3.107 16.184V7.816L6.657 5.765V18.235ZM10.207 20.286L6.657 18.235V5.765L10.207 3.714V20.286ZM13.758 18.235L10.207 20.286V3.714L13.758 1.663V18.235ZM17.308 16.184L13.758 18.235V1.663L17.308 3.714V16.184ZM20.858 14.133L17.308 16.184V3.714L20.858 5.765V14.133Z"/></svg>
              <span class="text-slate-600 dark:text-slate-400">Laravel</span>
            </div>
            <span class="w-1 h-1 bg-slate-300 dark:bg-slate-700 rounded-full"></span>
            <div class="flex items-center gap-1">
              <svg class="w-3.5 h-3.5 text-[#00DC82]" viewBox="0 0 24 24" fill="currentColor"><path d="M11.53 4.29a1 1 0 011.83 0l8.77 15.65a1 1 0 01-.89 1.45H2.76a1 1 0 01-.89-1.45L11.53 4.29zM12 7.7L5.78 18.79h12.44L12 7.7z"/></svg>
              <span class="text-slate-600 dark:text-slate-400">Nuxt 3</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
interface Subject {
  id: number
  name: string
  slug: string
  color?: string
}

const { $api } = useApi()
const config = useRuntimeConfig()

const navLinks = [
  { name: 'Bosh sahifa', href: '/' },
  { name: 'Biz haqimizda', href: '/about' },
  { name: 'Statistika', href: '/stats' },
  { name: "Bog'lanish", href: '/contact' },
]

const subjects = ref<Subject[]>([])
const currentPage = ref(1)
const totalPages = ref(1)
const pending = ref(true)
const generationTime = ref(0)

const fetchSubjects = async (page = 1) => {
  pending.value = true
  try {
    const response: any = await $api(`${config.public.apiBase}/subjects`, {
      params: { page }
    })
    subjects.value = response.data
    currentPage.value = response.current_page
    totalPages.value = response.last_page
  } catch (e) {
    console.error(e)
  } finally {
    pending.value = false
  }
}

const changePage = (newPage: number) => {
  if (newPage >= 1 && newPage <= totalPages.value) {
    fetchSubjects(newPage)
  }
}

onMounted(() => {
  setTimeout(() => {
    const [entry] = performance.getEntriesByType('navigation') as any;
    if (entry) {
      generationTime.value = Math.round(entry.domContentLoadedEventEnd - entry.responseStart);
    }
  }, 500);
  fetchSubjects()
})
</script>