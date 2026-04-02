<script setup lang="ts">
const config = useRuntimeConfig()

interface SubjectFile {
  id: number
  file_path: string
  file_name: string
  file_type: string
  file_size: number
}

interface Subject {
  id: number
  name: string
  slug: string
  image: string | null
  description: string | null
  color: string
  topics_count: number
  questions_count: number
  files: SubjectFile[]
}

const PER_PAGE = 6

const subjects    = ref<Subject[]>([])
const loading     = ref(true)
const search      = ref('')
const currentPage = ref(1)
const lastPage    = ref(1)
const total       = ref(0)

function getImageUrl(path: string | null): string | null {
  if (!path) return null
  if (path.startsWith('http://') || path.startsWith('https://')) return path
  const base = (config.public.storageBase as string) || (config.public.apiBase as string).replace('/api', '')
  return `${base}/storage/${path.replace(/^\//, '')}`
}

useSeoMeta({
  title: () => search.value ? `"${search.value}" bo'yicha fanlar` : 'Barcha fanlar',
  ogTitle: () => search.value ? `"${search.value}" bo'yicha fanlar` : 'Barcha fanlar',
  description: "Matematika, ingliz tili, fizika va boshqa barcha fanlar bo'yicha onlayn testlar hamda mavzulashtirilgan savollar to'plami.",
  ogDescription: "Bilimingizni sinash uchun mavjud barcha fanlar va mavzular ro'yxati.",
  ogImage: `${config.public.siteUrl}/og-subjects.jpg`,
  ogType: 'website',
  twitterCard: 'summary_large_image',
})

useHead({
  link: [{ rel: 'canonical', href: () => `${config.public.siteUrl}/subjects` }],
})

async function fetchSubjects(page = 1) {
  loading.value = true
  try {
    const data: any = await $fetch(`${config.public.apiBase}/subjects`, {
      params: { page, search: search.value, per_page: PER_PAGE },
    })
    subjects.value    = data.data
    currentPage.value = data.current_page
    lastPage.value    = data.last_page
    total.value       = data.total
  } catch {}
  finally {
    loading.value = false
  }
}

function goToPage(page: number) {
  if (page < 1 || page > lastPage.value || page === currentPage.value) return
  fetchSubjects(page)
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const visiblePages = computed(() => {
  const pages: (number | '...')[] = []
  const last = lastPage.value
  const cur  = currentPage.value
  if (last <= 7) {
    for (let i = 1; i <= last; i++) pages.push(i)
    return pages
  }
  pages.push(1)
  if (cur > 3) pages.push('...')
  for (let i = Math.max(2, cur - 1); i <= Math.min(last - 1, cur + 1); i++) pages.push(i)
  if (cur < last - 2) pages.push('...')
  pages.push(last)
  return pages
})

let timeout: any = null
watch(search, () => {
  clearTimeout(timeout)
  timeout = setTimeout(() => {
    currentPage.value = 1
    fetchSubjects(1)
  }, 500)
})

onMounted(() => fetchSubjects(1))
</script>

<template>
  <div class="min-h-screen bg-[#f4f5f9] dark:bg-[#0d0e14]">

    <section class="relative overflow-hidden bg-white dark:bg-[#111318] border-b border-black/[0.06] dark:border-white/[0.05]">
      <div class="absolute -top-24 -right-24 w-[480px] h-[480px] rounded-full bg-blue-500 opacity-[0.06] dark:opacity-[0.1] blur-[90px] pointer-events-none" />
      <div class="absolute -bottom-20 -left-20 w-80 h-80 rounded-full bg-violet-500 opacity-[0.04] dark:opacity-[0.08] blur-[70px] pointer-events-none" />

      <div class="relative max-w-3xl mx-auto px-6 py-20 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-blue-200 dark:border-blue-500/25 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 text-[11px] font-bold tracking-widest uppercase mb-7">
          <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse" />
          Barcha fanlar
        </div>

        <h1 class="text-4xl sm:text-5xl font-black tracking-tight leading-[1.12] text-slate-900 dark:text-slate-100 mb-4">
          Qaysi fanni<br>
          <span class="bg-gradient-to-br from-blue-500 to-indigo-500 bg-clip-text text-transparent">
            o'rganmoqchisiz?
          </span>
        </h1>

        <p class="text-[15px] leading-relaxed text-slate-500 dark:text-slate-400 mb-10">
          Mavzular bo'yicha savollar, testlar va bilimlarni<br class="hidden sm:block">
          mustahkamlash uchun fan tanlang
        </p>

        <div class="relative max-w-md mx-auto">
          <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-slate-400 pointer-events-none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
          <input
            v-model="search"
            type="text"
            placeholder="Fan nomini qidiring..."
            class="w-full pl-11 pr-10 py-3.5 rounded-[14px] border-[1.5px] border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/[0.04] text-slate-900 dark:text-slate-100 text-sm placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-blue-500 dark:focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 focus:bg-white dark:focus:bg-white/[0.06] transition-all duration-200"
          />
          <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 scale-75"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-75"
          >
            <button
              v-if="search"
              class="absolute right-3 top-1/2 -translate-y-1/2 w-7 h-7 flex items-center justify-center rounded-lg bg-slate-200 dark:bg-white/10 text-slate-500 dark:text-slate-400 hover:bg-slate-300 dark:hover:bg-white/20 transition-colors"
              @click="search = ''"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </Transition>
        </div>
      </div>
    </section>

    <div class="max-w-[1200px] mx-auto px-6 py-10 pb-20">

      <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div
          v-for="i in PER_PAGE"
          :key="i"
          class="rounded-2xl bg-white dark:bg-[#16171f] border border-black/[0.06] dark:border-white/[0.06] overflow-hidden animate-pulse"
        >
          <div class="h-[180px] bg-slate-100 dark:bg-white/[0.05]" />
          <div class="p-5 space-y-3">
            <div class="h-4 bg-slate-100 dark:bg-white/[0.08] rounded-lg w-2/3" />
            <div class="h-3 bg-slate-100 dark:bg-white/[0.05] rounded-lg w-1/2" />
            <div class="flex gap-2 pt-1">
              <div class="h-7 w-24 bg-slate-100 dark:bg-white/[0.05] rounded-[8px]" />
              <div class="h-7 w-24 bg-slate-100 dark:bg-white/[0.05] rounded-[8px]" />
            </div>
          </div>
        </div>
      </div>

      <template v-else-if="subjects.length > 0">
        <div class="flex items-center justify-between mb-6">
          <p class="text-[13px] text-slate-500 dark:text-slate-400">
            Jami <span class="font-bold text-slate-900 dark:text-slate-100">{{ total }}</span> ta fan
          </p>
          <p class="text-[13px] text-slate-400 dark:text-slate-500">
            {{ currentPage }} / {{ lastPage }} sahifa
          </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
          <NuxtLink
            v-for="subject in subjects"
            :key="subject.id"
            :to="`/subjects/${subject.slug}`"
            class="group flex flex-col rounded-2xl bg-white dark:bg-[#16171f] border border-black/[0.07] dark:border-white/[0.07] shadow-[0_2px_16px_-4px_rgba(0,0,0,0.07)] dark:shadow-[0_2px_20px_-4px_rgba(0,0,0,0.35)] hover:shadow-[0_20px_56px_-12px_rgba(0,0,0,0.16)] dark:hover:shadow-[0_24px_64px_-12px_rgba(0,0,0,0.6)] hover:border-black/[0.11] dark:hover:border-white/[0.13] hover:-translate-y-1.5 overflow-hidden transition-all duration-300 ease-[cubic-bezier(.22,1,.36,1)] no-underline text-inherit"
          >
            <div
              class="relative w-full h-45 flex items-center justify-center overflow-hidden shrink-0"
              :style="`background: linear-gradient(145deg, ${subject.color}20 0%, ${subject.color}0d 100%)`"
            >
              <img
                  v-if="subject.image"
                  :src="getImageUrl(subject.image)!"
                  :alt="subject.name"
                  class="w-full object-contain rounded-[24px]"
                  style="filter: drop-shadow(0 4px 12px rgba(0,0,0,0.15))"
                  loading="lazy"
                />
            </div>

            <div class="p-5 flex flex-col flex-1">
              <h3 class="text-[15px] font-bold leading-snug text-slate-900 dark:text-slate-100 truncate mb-1.5 transition-colors duration-200 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                {{ subject.name }}
              </h3>
              <p
                v-if="subject.description"
                class="text-xs text-slate-400 dark:text-slate-500 leading-relaxed line-clamp-2 mb-4"
                v-html="subject.description"
              />
              <div v-else class="mb-4" />

              <div class="flex items-center gap-2 mt-auto">
                <div
                  class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-[8px] text-[11px] font-semibold"
                  :style="`background: ${subject.color}15; color: ${subject.color}`"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                  </svg>
                  {{ subject.topics_count }} mavzu
                </div>

                <div class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-[8px] bg-slate-100 dark:bg-white/[0.07] text-[11px] font-semibold text-slate-500 dark:text-slate-400">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                  </svg>
                  {{ subject.questions_count }} savol
                </div>

                <div
                  class="ml-auto w-8 h-8 rounded-[8px] flex items-center justify-center shrink-0 opacity-0 translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-200"
                  :style="`background: ${subject.color}18`"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" :style="`color: ${subject.color}`" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                  </svg>
                </div>
              </div>
            </div>
          </NuxtLink>
        </div>

        <nav v-if="lastPage > 1" class="mt-12 flex items-center justify-center gap-1.5 flex-wrap">
          <button
            :disabled="currentPage === 1"
            class="w-9 h-9 flex items-center justify-center rounded-[10px] border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.04] text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/[0.09] disabled:opacity-30 disabled:cursor-not-allowed transition-all"
            @click="goToPage(currentPage - 1)"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
          </button>

          <template v-for="(page, i) in visiblePages" :key="i">
            <span
              v-if="page === '...'"
              class="w-9 h-9 flex items-center justify-center text-slate-400 dark:text-slate-500 text-sm select-none"
            >···</span>
            <button
              v-else
              class="w-9 h-9 flex items-center justify-center rounded-[10px] text-[13px] font-semibold transition-all"
              :class="page === currentPage
                ? 'bg-blue-600 text-white border border-blue-600 shadow-[0_4px_14px_-2px_rgba(59,130,246,0.45)]'
                : 'border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.04] text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/[0.09]'"
              @click="goToPage(page as number)"
            >{{ page }}</button>
          </template>

          <button
            :disabled="currentPage === lastPage"
            class="w-9 h-9 flex items-center justify-center rounded-[10px] border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.04] text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/[0.09] disabled:opacity-30 disabled:cursor-not-allowed transition-all"
            @click="goToPage(currentPage + 1)"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
            </svg>
          </button>
        </nav>
      </template>

      <div v-else class="flex flex-col items-center justify-center py-24 text-center">
        <div class="w-16 h-16 rounded-[18px] bg-slate-100 dark:bg-white/[0.06] flex items-center justify-center mb-5">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
        </div>
        <h3 class="text-lg font-extrabold text-slate-900 dark:text-slate-100 mb-2">Hech narsa topilmadi</h3>
        <p class="text-sm text-slate-400 dark:text-slate-500 mb-6">"{{ search }}" bo'yicha fan mavjud emas</p>
        <button
          class="px-5 py-2.5 rounded-[12px] bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-sm font-semibold transition-all duration-150"
          @click="search = ''"
        >
          Qidiruvni tozalash
        </button>
      </div>

    </div>
  </div>
</template>