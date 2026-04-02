<script setup lang="ts">
const config = useRuntimeConfig()
const route = useRoute()

const { data: news, pending, error } = await useAsyncData(`news-${route.params.slug}`, () =>
  $fetch(`${config.public.apiBase}/news/${route.params.slug}`).then(res => res.data)
)

useSeoMeta({
  title: () => news.value?.title,
  ogTitle: () => news.value?.title,
  description: () => news.value?.excerpt,
  ogDescription: () => news.value?.excerpt,
  ogImage: () => news.value?.image,
  ogType: 'article',
  articlePublishedTime: () => news.value?.published_at,
  articleModifiedTime: () => news.value?.updated_at,
  twitterCard: 'summary_large_image',
  twitterTitle: () => news.value?.title,
  twitterDescription: () => news.value?.excerpt,
  twitterImage: () => news.value?.image,
})

useHead({
  link: [
    { 
      rel: 'canonical', 
      href: () => `${config.public.siteUrl}/news/${route.params.slug}` 
    }
  ],
  script: [
    {
      type: 'application/ld+json',
      children: JSON.stringify({
        "@context": "https://schema.org",
        "@type": "Yangilik haqida",
        "headline": news.value?.title,
        "image": [news.value?.image],
        "datePublished": news.value?.published_at,
        "dateModified": news.value?.updated_at,
        "description": news.value?.excerpt,
        "mainEntityOfPage": {
          "@type": "WebPage",
          "@id": `${config.public.siteUrl}/news/${route.params.slug}`
        },
        "author": {
          "@type": "Organization",
          "name": config.public.siteName || "Platforma"
        }
      })
    }
  ]
})

function formatDate(str: string) {
  if (!str) return ''
  return new Date(str).toLocaleDateString('uz-UZ', {
    year: 'numeric', month: 'long', day: 'numeric',
  })
}

</script>

<template>
  <div class="max-w-7xl mx-auto py-8 px-4">

    <!-- Back -->
    <NuxtLink to="/news" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors mb-6">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
      </svg>
      Yangiliklarga qaytish
    </NuxtLink>

    <!-- Loading -->
    <div v-if="pending" class="space-y-4 animate-pulse">
      <div class="h-8 bg-gray-100 dark:bg-slate-800 rounded-xl w-3/4" />
      <div class="h-4 bg-gray-100 dark:bg-slate-800 rounded w-1/3" />
      <div class="aspect-video bg-gray-100 dark:bg-slate-800 rounded-2xl" />
      <div class="space-y-3">
        <div class="h-4 bg-gray-100 dark:bg-slate-800 rounded w-full" />
        <div class="h-4 bg-gray-100 dark:bg-slate-800 rounded w-5/6" />
        <div class="h-4 bg-gray-100 dark:bg-slate-800 rounded w-4/5" />
      </div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="text-center py-20">
      <p class="text-slate-500 font-semibold">Yangilik topilmadi</p>
      <NuxtLink to="/news" class="text-blue-600 text-sm mt-2 inline-block hover:underline">Orqaga qaytish</NuxtLink>
    </div>

    <!-- Article -->
    <article v-else-if="news">

      <!-- Category & date -->
      <div class="flex items-center gap-3 flex-wrap mb-4">
        <span v-if="(news as any).category" class="px-3 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-xl text-xs font-bold uppercase tracking-wide">
          {{ (news as any).category }}
        </span>
        <span class="text-sm text-slate-400 dark:text-slate-500">{{ formatDate((news as any).published_at) }}</span>
        <span class="flex items-center gap-1 text-sm text-slate-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          {{ (news as any).views }}
        </span>
      </div>

      <!-- Title -->
      <h1 class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-white leading-tight mb-4">
        {{ (news as any).title }}
      </h1>

      <!-- Author -->
      <div class="flex items-center gap-3 mb-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
          <span class="text-white text-sm font-bold">
            {{ (news as any).author?.name?.charAt(0).toUpperCase() }}
          </span>
        </div>
        <div>
          <div class="text-sm font-bold text-slate-800 dark:text-white">{{ (news as any).author?.name }}</div>
          <div class="text-xs text-slate-400">Muallif</div>
        </div>
      </div>

      <!-- Image -->
      <div v-if="(news as any).image" class="aspect-video rounded-2xl overflow-hidden mb-8 bg-gray-100 dark:bg-slate-800">
        <img :src="(news as any).image" :alt="(news as any).title" class="w-full h-full object-cover" />
      </div>

      <!-- Excerpt -->
      <p v-if="(news as any).excerpt" class="text-base text-slate-600 dark:text-slate-300 font-semibold leading-relaxed mb-6 p-4 bg-blue-50 dark:bg-blue-900/10 border-l-4 border-blue-600 rounded-r-xl">
        {{ (news as any).excerpt }}
      </p>
      <!-- Body -->
      <div 
        class="prose prose-sm dark:prose-invert max-w-none text-slate-700 dark:text-slate-300 leading-relaxed"
        v-html="news.body"
      >
      </div>

    </article>

  </div>
</template>
