<template>
  <section class="py-12 dark:bg-slate-950 overflow-hidden">
    <div class="max-w-360 mx-auto px-4">
      
      <div class="flex items-end justify-between mb-8 px-2">
        <div class="space-y-2">
          <span class="text-blue-600 dark:text-blue-400 font-bold tracking-tighter text-sm uppercase">
            // Bizning jamoa
          </span>
          <h2 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white leading-none">
            Mutaxassislarimiz
          </h2>
        </div>
        
        <div class="flex gap-1.5">
          <button class="team-prev w-10 h-10 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all active:scale-90">
            <ChevronLeft class="w-5 h-5" />
          </button>
          <button class="team-next w-10 h-10 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all active:scale-90">
            <ChevronRight class="w-5 h-5" />
          </button>
        </div>
      </div>

      <Swiper
        :modules="[SwiperAutoplay, SwiperNavigation]"
        :slides-per-view="1.3"
        :space-between="16"
        :loop="true"
        :speed="800"
        :autoplay="{
          delay: 3000,
          disableOnInteraction: false,
          pauseOnMouseEnter: true
        }"
        :navigation="{ prevEl: '.team-prev', nextEl: '.team-next' }"
        :breakpoints="{
          '640': { slidesPerView: 2.2, spaceBetween: 16 },
          '1024': { slidesPerView: 4, spaceBetween: 20 },
          '1536': { slidesPerView: 5, spaceBetween: 20 }
        }"
        class="!overflow-visible"
      >
        <SwiperSlide v-for="member in team" :key="member.id">
          <div class="group relative bg-white dark:bg-slate-900 rounded-3xl p-3 border border-slate-100 dark:border-slate-800 transition-all duration-500 hover:shadow-xl hover:shadow-blue-500/5 hover:-translate-y-1">
            
            <div class="relative aspect-square rounded-[20px] overflow-hidden mb-4 bg-slate-100 dark:bg-slate-800">
              <img 
                v-if="member.avatar" 
                :src="member.avatar"
                :alt="member.name" 
                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
              />
              <div v-else class="w-full h-full flex items-center justify-center bg-blue-600/10 text-blue-600">
                <span class="text-3xl font-black">{{ member.name.charAt(0) }}</span>
              </div>

              <div class="absolute inset-0 bg-gradient-to-t from-blue-900/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-end justify-center pb-4 gap-2">
                <a :href="`https://t.me/${member.telegram}`" target="_blank" class="p-2 bg-white/20 backdrop-blur-md rounded-lg text-white hover:bg-white hover:text-blue-600 transition-all">
                  <Send class="w-4 h-4" />
                </a>
                <a :href="`mailto:${member.email}`" class="p-2 bg-white/20 backdrop-blur-md rounded-lg text-white hover:bg-white hover:text-blue-600 transition-all">
                  <Mail class="w-4 h-4" />
                </a>
              </div>
            </div>

            <div class="text-center">
              <h3 class="text-lg font-bold text-slate-800 dark:text-white leading-tight mb-1 group-hover:text-blue-600 transition-colors">
                {{ member.name }}
              </h3>
              <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                {{ member.role }}
              </p>
            </div>
          </div>
        </SwiperSlide>
      </Swiper>
    </div>
  </section>
</template>

<script setup>
import { ChevronLeft, ChevronRight, Send, Mail } from 'lucide-vue-next'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Autoplay, Navigation } from 'swiper/modules'
const config = useRuntimeConfig()

// Swiper CSS
import 'swiper/css'

const SwiperAutoplay = Autoplay
const SwiperNavigation = Navigation

defineProps({
  team: { type: Array, required: true }
})
</script>

<style scoped>
.swiper-slide {
  height: auto !important;
}

/* Slayder chekkalari kesilib qolmasligi uchun */
:deep(.swiper) {
  overflow: visible !important;
}
</style>