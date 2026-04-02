<script setup lang="ts">
import AppFooter from './components/Layouts/AppFooter.vue'
import AppNavbar from './components/Layouts/AppNavbar.vue'
import Olympiadbanner from './components/Olympiad/Olympiadbanner.vue'

const config = useRuntimeConfig()

useHead({
  htmlAttrs: { lang: 'uz' },
  link: [{ rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }],
})

const { data: olympiads } = await useFetch<any[]>(
  `${config.public.apiBase}/olympiads/today`,
  { default: () => [] }
)
</script>

<template>
  <UApp>
    <UNotifications />
    <Olympiadbanner :olympiads="olympiads" />
    <div class="min-h-screen flex flex-col bg-white dark:bg-slate-950">
      <AppNavbar />
      <main class="flex-1 w-full pb-24 lg:pb-0">
        <div class="mx-auto">
          <NuxtPage />
        </div>
      </main>
      <AppFooter class="mb-24 lg:mb-0" />
    </div>
  </UApp>
</template>