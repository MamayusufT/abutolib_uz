<script setup lang="ts">
const config = useRuntimeConfig()
const route = useRoute()

interface SubjectFile {
  id: number
  file_path: string
  file_name: string
  file_type: string
  file_size: number
}

interface Topic {
  id: number
  name: string
  description: string | null
  questions_count: number
}

interface Subject {
  id: number
  name: string
  slug: string
  image: string | null
  description: string | null
  color: string
  topics_count: number
  files: SubjectFile[]
}

const subject = ref<Subject | null>(null)
const topics = ref<Topic[]>([])
const loading = ref(true)
const downloadingId = ref<number | null>(null)

useSeoMeta({
  title: () => subject.value ? `${subject.value.name} - Testlar` : 'Yuklanmoqda...',
  description: () => subject.value?.description?.replace(/<[^>]*>?/gm, '') || 'Online testlar',
})

async function fetchData() {
  try {
    const res: any = await $fetch(`${config.public.apiBase}/subjects/${route.params.slug}`)
    subject.value = res.data
    topics.value = res.data.topics
  } catch {
    await navigateTo('/subjects')
  } finally {
    loading.value = false
  }
}

const downloadFile = async (file: SubjectFile) => {
  downloadingId.value = file.id
  try {
    const response = await fetch(`${config.public.apiBase}/subjects/file-download/${file.id}`)
    const blob = await response.blob()
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', file.file_name)
    document.body.appendChild(link)
    link.click()
    link.parentNode?.removeChild(link)
    window.URL.revokeObjectURL(url)
  } catch (error) {
    console.error(error)
  } finally {
    downloadingId.value = null
  }
}

const formatSize = (bytes: number) => {
  if (bytes === 0) return '0 B'
  const i = Math.floor(Math.log(bytes) / Math.log(1024))
  return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + ['B', 'KB', 'MB', 'GB'][i]
}

onMounted(fetchData)
</script>

<template>
  <div class="max-w-7xl mx-auto py-8 px-4">
    <NuxtLink to="/subjects" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 mb-6">
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor font-bold"><path d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
      Orqaga
    </NuxtLink>

    <div v-if="loading" class="max-w-3xl mx-auto space-y-6 animate-pulse">
      <div class="h-40 bg-gray-100 dark:bg-slate-800 rounded-2xl" />
      <div v-for="i in 4" :key="i" class="h-20 bg-gray-100 dark:bg-slate-800 rounded-2xl" />
    </div>

    <div v-else-if="subject" class="max-w-3xl mx-auto">
      <div class="relative rounded-2xl overflow-hidden mb-8 shadow-sm">
        <div class="h-40 w-full" :style="`background: linear-gradient(135deg, ${subject.color}cc, ${subject.color})`">
          <img v-if="subject.image" :src="subject.image" class="w-full h-full object-cover mix-blend-overlay opacity-30" />
        </div>
        <div class="absolute inset-0 flex items-center px-8 text-white">
          <div>
            <h1 class="text-3xl font-black">{{ subject.name }}</h1>
            <p v-if="subject.description" class="text-white/90 text-sm mt-2 line-clamp-2" v-html="subject.description" />
            <span class="mt-4 inline-block bg-white/20 px-3 py-1 rounded-full text-xs font-bold">{{ subject.topics_count }} mavzu</span>
          </div>
        </div>
      </div>

      <section v-if="subject.files?.length" class="mb-10">
        <h2 class="text-lg font-black mb-4 flex items-center gap-2">Resurslar</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div v-for="file in subject.files" :key="file.id" class="flex items-center justify-between p-4 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl">
            <div class="flex items-center gap-3 min-w-0">
              <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 font-bold text-[10px]">{{ file.file_type.toUpperCase() }}</div>
              <div class="min-w-0">
                <div class="text-sm font-bold truncate pr-2">{{ file.file_name }}</div>
                <div class="text-[10px] text-slate-400">{{ formatSize(file.file_size) }}</div>
              </div>
            </div>
            <button @click="downloadFile(file)" :disabled="downloadingId === file.id" class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center">
              <svg v-if="downloadingId !== file.id" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
              <svg v-else class="animate-spin h-5 w-5" viewBox="0 0 24 24 text-blue-600"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" class="opacity-25"/><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" class="opacity-75"/></svg>
            </button>
          </div>
        </div>
      </section>

      <section class="space-y-4">
        <h2 class="text-lg font-black mb-4">Mavzular</h2>
        <NuxtLink v-for="(topic, index) in topics" :key="topic.id" :to="`/subjects/${route.params.slug}/test/${topic.id}`" class="group flex items-center gap-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-5 hover:shadow-md transition-all">
          <div class="w-11 h-11 rounded-xl flex items-center justify-center font-black text-sm" :style="`background: ${subject.color}15; color: ${subject.color}`">{{ index + 1 }}</div>
          <div class="flex-1 min-w-0">
            <div class="font-black group-hover:text-blue-600 transition-colors">{{ topic.name }}</div>
            <div v-if="topic.description" v-html="topic.description" class="text-xs text-slate-500 mt-1 truncate" />
          </div>
          <div class="flex items-center gap-4">
            <span class="hidden sm:block text-xs font-bold text-slate-400">{{ topic.questions_count }} savol</span>
            <div class="w-9 h-9 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </div>
          </div>
        </NuxtLink>
      </section>
    </div>
  </div>
</template>