<template>
  <div class="min-h-screen bg-slate-50 dark:bg-[#0f172a] py-24 px-4 transition-colors duration-500 relative overflow-hidden">
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-400/10 dark:bg-blue-600/5 blur-[100px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-indigo-400/10 dark:bg-indigo-600/5 blur-[100px] rounded-full pointer-events-none"></div>

    <Head>
      <Title>Tarif Rejalari | AI Platform</Title>
    </Head>

    <div class="relative z-10 max-w-6xl mx-auto">
      <div class="text-center mb-16 space-y-4">
        <h2 class="text-blue-600 dark:text-blue-400 text-sm font-bold tracking-[0.2em] uppercase">
          Narxlar va imkoniyatlar
        </h2>
        <h1 class="text-4xl md:text-6xl font-extrabold text-slate-900 dark:text-white tracking-tight">
          O'zingizga mos <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-500">tarifni</span> tanlang
        </h1>
        <p class="text-slate-500 dark:text-slate-400 max-w-2xl mx-auto text-lg">
          Barcha ehtiyojlar uchun moslashuvchan rejalar. Istalgan vaqtda tarifni o'zgartirishingiz mumkin.
        </p>
      </div>

      <div v-if="store.loading" class="flex justify-center items-center py-20">
        <div class="w-12 h-12 rounded-full border-4 border-blue-100 dark:border-slate-800 border-t-blue-600 animate-spin"></div>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div
          v-for="(plan, i) in store.plans"
          :key="plan.id"
          :class="[
            'relative flex flex-col rounded-[2rem] p-8 transition-all duration-300',
            'bg-white dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-slate-800',
            'hover:shadow-2xl hover:shadow-blue-500/10 hover:-translate-y-2',
            planConfig[i]?.popular ? 'ring-2 ring-blue-500 dark:ring-blue-600' : ''
          ]"
        >
          <div
            v-if="store.activeSubscription?.plan_id === plan.id"
            class="absolute -top-4 left-1/2 -translate-x-1/2 bg-emerald-500 text-white text-[10px] font-bold uppercase tracking-widest px-5 py-2 rounded-full shadow-lg shadow-emerald-500/20"
          >
            Faol tarif
          </div>

          <div
            v-else-if="planConfig[i]?.popular"
            class="absolute -top-4 left-1/2 -translate-x-1/2 bg-blue-600 text-white text-[10px] font-bold uppercase tracking-widest px-5 py-2 rounded-full shadow-lg shadow-blue-600/20"
          >
            Eng mashhur
          </div>

          <div class="mb-8">
            <div :class="['w-14 h-14 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-sm', planConfig[i]?.iconBg]">
              {{ planConfig[i]?.icon }}
            </div>
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ plan.name }}</h3>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-2 leading-relaxed">
              {{ plan.description }}
            </p>
          </div>

          <div class="mb-8">
            <div class="flex items-baseline gap-1">
              <span class="text-4xl font-black text-slate-900 dark:text-white">
                {{ plan.price_uzs === 0 ? 'Bepul' : plan.price_uzs.toLocaleString() }}
              </span>
              <span v-if="plan.price_uzs > 0" class="text-slate-500 font-bold uppercase text-xs">UZS</span>
            </div>
            <p class="text-slate-400 dark:text-slate-500 text-xs mt-1 italic">
              {{ plan.price_uzs === 0 ? 'Sinov muddatisiz' : `$${plan.price_usd} USD oylik hisobda` }}
            </p>
          </div>

          <div class="flex flex-wrap gap-2 mb-8">
            <span
              v-for="model in JSON.parse(plan.included_models)"
              :key="model"
              class="text-[9px] font-bold px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 uppercase"
            >
              {{ model }}
            </span>
          </div>

          <div class="h-px w-full bg-slate-100 dark:bg-slate-800 mb-8"></div>

          <ul class="space-y-4 mb-10 flex-1">
            <li class="flex items-center gap-3 text-sm">
              <div class="w-5 h-5 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path d="M5 13l4 4L19 7" /></svg>
              </div>
              <span class="text-slate-600 dark:text-slate-300"><b class="text-slate-900 dark:text-white">{{ (plan.monthly_token_limit / 1000) }}K</b> Token / oy</span>
            </li>
            
            <li class="flex items-center gap-3 text-sm">
              <div class="w-5 h-5 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path d="M5 13l4 4L19 7" /></svg>
              </div>
              <span class="text-slate-600 dark:text-slate-300">Max <b class="text-slate-900 dark:text-white">{{ plan.max_pages_per_ocr }}</b> sahifa OCR</span>
            </li>

            <li :class="['flex items-center gap-3 text-sm', plan.has_teacher_panel ? '' : 'opacity-40 grayscale']">
              <div :class="['w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0', plan.has_teacher_panel ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-400']">
                <svg v-if="plan.has_teacher_panel" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path d="M5 13l4 4L19 7" /></svg>
                <span v-else class="text-[10px]">✕</span>
              </div>
              <span class="text-slate-600 dark:text-slate-300">Teacher Panel</span>
            </li>

            <li :class="['flex items-center gap-3 text-sm', plan.has_admin_panel ? '' : 'opacity-40 grayscale']">
              <div :class="['w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0', plan.has_admin_panel ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-400']">
                <svg v-if="plan.has_admin_panel" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path d="M5 13l4 4L19 7" /></svg>
                <span v-else class="text-[10px]">✕</span>
              </div>
              <span class="text-slate-600 dark:text-slate-300">Admin Panel boshqaruvi</span>
            </li>

            <li v-if="plan.priority_support" class="flex items-center gap-3 text-sm font-semibold text-blue-600 dark:text-blue-400">
              <div class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center flex-shrink-0 animate-pulse shadow-lg shadow-blue-500/40">
                <span class="text-[8px]">★</span>
              </div>
              <span>Priority Support 24/7</span>
            </li>
          </ul>

          <div class="mt-auto space-y-3">
            <template v-if="plan.price_uzs === 0">
              <button
                @click="store.activateFree(plan.id)"
                class="w-full py-4 rounded-2xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-sm transition-all hover:bg-blue-600 dark:hover:bg-blue-500 hover:text-white active:scale-95"
              >
                Hoziroq boshlash
              </button>
            </template>
            <template v-else>
              <button
                @click="store.checkout(plan.id, 'click')"
                class="w-full py-4 rounded-2xl bg-blue-600 text-white font-bold text-sm transition-all hover:bg-blue-700 hover:shadow-xl hover:shadow-blue-500/20 active:scale-95"
              >
                Click bilan to'lash
              </button>
              <button
                @click="store.checkout(plan.id, 'payme')"
                class="w-full py-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-bold text-sm transition-all hover:border-blue-400 active:scale-95 flex items-center justify-center gap-2"
              >
                Payme orqali
              </button>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const store = useSubscriptionStore()

const planConfig = [
  {
    icon: '🚀',
    popular: false,
    iconBg: 'bg-blue-50 dark:bg-blue-900/20 text-blue-600',
  },
  {
    icon: '⚡',
    popular: true,
    iconBg: 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600',
  },
  {
    icon: '👑',
    popular: false,
    iconBg: 'bg-amber-50 dark:bg-amber-900/20 text-amber-600',
  },
]

onMounted(async () => {
  await Promise.all([
    store.fetchPlans(),
    store.fetchCurrentSubscription()
  ])
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
* { font-family: 'Inter', sans-serif; }
</style>