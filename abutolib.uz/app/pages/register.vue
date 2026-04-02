<script setup lang="ts">
import { useAuthStore } from '~/stores/auth'

definePageMeta({ middleware: 'guest', layout: 'auth' })

const config = useRuntimeConfig()

useSeoMeta({
  title: 'Ro\'yxatdan o\'tish',
  description: 'Yangi hisob yarating va bizning xizmatlarimizdan foydalaning.',
  ogTitle: 'Ro\'yxatdan o\'tish - ' + config.public.appName,
  ogDescription: 'Yangi hisob yarating va bizning xizmatlarimizdan foydalaning.',
  ogUrl: config.public.appUrl + '/register',
  ogType: 'website',
});

const auth  = useAuthStore()
const name                 = ref('')
const email                = ref('')
const password             = ref('')
const password_confirmation = ref('')

async function submit() {
  await auth.register(name.value, email.value, password.value, password_confirmation.value)
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 to-blue-50 dark:from-slate-950 dark:to-slate-900 px-4 py-8">
    <div class="w-full max-w-md">
      <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-800 p-8">

        <div class="flex flex-col items-center mb-8">
          <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200 dark:shadow-none mb-4">
            <span class="text-white text-3xl font-black">+</span>
          </div>
          <h1 class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-tight">Ro'yxatdan o'tish</h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Yangi hisob yarating</p>
        </div>

        <div v-if="auth.error" class="mb-5 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-600 dark:text-red-400 flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
            <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd"/>
          </svg>
          {{ auth.error }}
        </div>

        <form class="space-y-4" @submit.prevent="submit">
          <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Ism Familiya</label>
            <input v-model="name" type="text" required placeholder="Abdulloh Karimov"
              class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800 text-slate-800 dark:text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
            <input v-model="email" type="email" required placeholder="example@mail.com"
              class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800 text-slate-800 dark:text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Parol</label>
            <input v-model="password" type="password" required placeholder="Min. 8 ta belgi"
              class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800 text-slate-800 dark:text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Parolni tasdiqlash</label>
            <input v-model="password_confirmation" type="password" required placeholder="Parolni qayta kiriting"
              class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800 text-slate-800 dark:text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
          </div>

          <button type="submit" :disabled="auth.loading"
            class="w-full py-3 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed text-white rounded-xl font-bold text-sm shadow-md shadow-blue-200 dark:shadow-none transition-all active:scale-95 mt-2">
            <span v-if="auth.loading" class="flex items-center justify-center gap-2">
              <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              Ro'yxatdan o'tilmoqda...
            </span>
            <span v-else>Ro'yxatdan o'tish</span>
          </button>
        </form>

        <p class="text-center text-sm text-slate-500 dark:text-slate-400 mt-6">
          Hisob bor?
          <NuxtLink to="/login" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline">Kirish</NuxtLink>
        </p>
      </div>
    </div>
  </div>
</template>