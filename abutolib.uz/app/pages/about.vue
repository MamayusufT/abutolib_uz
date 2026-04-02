<template>
  <div class="max-w-7xl mx-auto py-12 px-4">

    <!-- Header -->
    <div class="text-center mb-12">
      <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-600 rounded-2xl shadow-lg shadow-blue-200 dark:shadow-none mb-4">
        <span class="text-white text-2xl font-black">+</span>
      </div>
      <h1 class="text-3xl font-black text-slate-800 dark:text-white uppercase tracking-tight">Abutolib</h1>
      <p class="text-slate-500 dark:text-slate-400 mt-2">Test tizimi — bilimingizni sinab ko'ring</p>
    </div>

    <!-- About text -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-8 mb-6">
      <h2 class="text-lg font-black text-slate-800 dark:text-white mb-3">Biz haqimizda</h2>
      <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
        <strong class="text-slate-800 dark:text-white">Abutolib</strong> — o'quvchilar va talabalar uchun mo'ljallangan zamonaviy online test platformasi.
        Turli fanlar bo'yicha testlar yechish, natijalarni kuzatish va bilim darajangizni oshirish imkonini beradi.
        Tizim qulay interfeysi va tezkorligi bilan ajralib turadi.
      </p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
      <div v-for="stat in stats" :key="stat.label" class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-3 text-center">
        <div class="text-2xl font-black text-blue-600 dark:text-blue-400">{{ stat.value }}</div>
        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-semibold">{{ stat.label }}</div>
      </div>
    </div>

    <!-- Features -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
      <div v-for="feature in features" :key="feature.title" class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-3">
        <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center mb-3">
          <component :is="feature.icon" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
        </div>
        <div class="text-sm font-black text-slate-800 dark:text-white">{{ feature.title }}</div>
        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ feature.desc }}</div>
      </div>
    </div>

    <!-- CTA -->
    <div class="bg-blue-600 rounded-2xl p-8 text-center">
      <h3 class="text-xl font-black text-white mb-2">Boshlashga tayyormisiz?</h3>
      <p class="text-blue-100 text-sm mb-5">Ro'yxatdan o'ting va testlarni bepul ishlang</p>
      <div class="flex justify-center gap-3 flex-wrap">
        <NuxtLink to="/register" class="px-6 py-2.5 bg-white text-blue-600 rounded-xl font-bold text-sm hover:bg-blue-50 transition-all active:scale-95">
          Ro'yxatdan o'tish
        </NuxtLink>
        <NuxtLink to="/contact" class="px-6 py-2.5 bg-blue-500 text-white rounded-xl font-bold text-sm hover:bg-blue-400 transition-all active:scale-95">
          Bog'lanish
        </NuxtLink>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { BookOpen, BarChart3, Shield } from 'lucide-vue-next'

const stats = [
  { value: '10K+', label: 'Foydalanuvchi' },
  { value: '500+', label: 'Testlar' },
  { value: '20+',  label: 'Fanlar' },
  { value: '98%',  label: 'Mamnunlik' },
]

const features = [
  { icon: BookOpen,  title: 'Ko\'p fanlar',    desc: 'Matematika, fizika, tarix va boshqa fanlar' },
  { icon: BarChart3, title: 'Natijalar',       desc: 'Har bir test natijasi saqlanadi va tahlil qilinadi' },
  { icon: Shield,    title: 'Xavfsizlik',      desc: 'Ma\'lumotlaringiz to\'liq himoyalangan' },
]

useSeoMeta({
  title: 'Abutolib - Biz haqimizda',
  description: 'Abutolib - o\'quvchilar va talabalar uchun mo\'ljallangan zamonaviy online test platformasi.',
})

useHead({
  link: [
    { rel: 'canonical', href: 'https://abutolib.uz/about' },
  ],
})
</script>