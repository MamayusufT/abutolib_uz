<script setup lang="ts">
interface Olympiad {
  id: number
  subject: string
  title: string
  time: string
  color: string
  icon: string
}

const props = defineProps<{
  olympiads?: Olympiad[]
}>()

const defaultOlympiads: Olympiad[] = [
  { id: 1, subject: 'Matematika', title: 'Matematika Olimpiadasi 2026', time: '13:48', color: '#3b82f6', icon: '📐' },
  { id: 2, subject: 'Fizika', title: 'Viloyat fizika musobaqasi', time: '14:00', color: '#8b5cf6', icon: '⚛️' },
  { id: 3, subject: 'Informatika', title: 'IT olimpiada — 2026', time: '16:30', color: '#f97316', icon: '💻' },
]

const olympiads = computed(() => props.olympiads ?? defaultOlympiads)
const current = ref(0)
const visible = ref(true)
const paused = ref(false)
let interval: any = null

function startRotation() {
  interval = setInterval(() => {
    if (!paused.value) {
      current.value = (current.value + 1) % olympiads.value.length
    }
  }, 4500)
}

function close() {
  visible.value = false
  clearInterval(interval)
}

onMounted(() => {
  if (olympiads.value.length > 1) startRotation()
})

onBeforeUnmount(() => clearInterval(interval))

const current_olympiad = computed(() => olympiads.value[current.value])
</script>

<template>
  <Transition
    enter-active-class="transition-all duration-700 ease-in-out"
    enter-from-class="-translate-y-full opacity-0"
    enter-to-class="translate-y-0 opacity-100"
    leave-active-class="transition-all duration-500 ease-in-out"
    leave-from-class="translate-y-0 opacity-100"
    leave-to-class="-translate-y-full opacity-0"
  >
    <div
      v-if="visible && olympiads.length > 0"
      class="relative w-full overflow-hidden backdrop-blur-md bg-white/70 dark:bg-slate-950/70 border-b border-slate-200/50 dark:border-slate-800/50 z-50"
      @mouseenter="paused = true"
      @mouseleave="paused = false"
    >
      <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div
          class="absolute inset-y-0 w-64 opacity-[0.08] blur-[80px] transition-all duration-1000 ease-in-out"
          :style="{
            background: current_olympiad.color,
            left: '20%',
            transform: `translateX(${current * 100}%)`
          }"
        />
      </div>

      <div class="relative flex items-center gap-4 px-4 py-2.5 max-w-7xl mx-auto">
        <div class="flex items-center gap-2.5 flex-shrink-0">
          <div class="relative flex h-2 w-2">
            <span
              class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-40"
              :style="`background: ${current_olympiad.color}`"
            />
            <span
              class="relative inline-flex rounded-full h-2 w-2"
              :style="`background: ${current_olympiad.color}`"
            />
          </div>
          <span 
            class="text-[10px] font-black tracking-[0.15em] uppercase hidden md:block"
            :style="`color: ${current_olympiad.color}`"
          >
            BUGUN
          </span>
        </div>

        <div class="w-px h-4 bg-slate-200 dark:bg-slate-800 flex-shrink-0 hidden sm:block" />

        <div class="flex-1 overflow-hidden">
          <Transition
            enter-active-class="transition-all duration-500 ease-out"
            enter-from-class="translate-y-4 opacity-0 scale-95"
            enter-to-class="translate-y-0 opacity-100 scale-100"
            leave-active-class="transition-all duration-300 ease-in"
            leave-from-class="translate-y-0 opacity-100 scale-100"
            leave-to-class="-translate-y-4 opacity-0 scale-95"
            mode="out-in"
          >
            <div :key="current_olympiad.id" class="flex items-center gap-3 min-w-0">
              <!-- <div 
                class="w-7 h-7 rounded-lg flex items-center justify-center text-sm shadow-sm bg-white dark:bg-slate-900 flex-shrink-0 border border-slate-100 dark:border-slate-800"
              >
                {{ current_olympiad.icon }}
              </div> -->
              <div class="flex items-center gap-2 min-w-0">
                <span class="text-[13px] text-slate-800 dark:text-slate-100 font-bold truncate">
                  {{ current_olympiad.title }}
                </span>
                <span
                  class="flex-shrink-0 text-[10px] font-black px-1.5 py-0.5 rounded-md"
                  :style="`background: ${current_olympiad.color}15; color: ${current_olympiad.color}`"
                >
                  {{ current_olympiad.time }}
                </span>
              </div>
            </div>
          </Transition>
        </div>

        <div class="flex items-center gap-4 flex-shrink-0">
          <NuxtLink 
            :to="`/olympiads/${current_olympiad.id}`"
            class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-900 hover:bg-white dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-bold transition-all active:scale-95 border border-transparent hover:border-slate-200 dark:hover:border-slate-700"
          >
            Qatnashish
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
          </NuxtLink>

          <button
            class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors"
            @click="close"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <div class="absolute bottom-0 left-0 right-0 h-[2px] bg-slate-100/50 dark:bg-slate-800/50">
        <div
          class="h-full transition-all ease-linear"
          :style="{
            width: !paused ? '100%' : '0%',
            background: current_olympiad.color,
            transitionDuration: !paused ? '4.5s' : '0s'
          }"
          :key="current"
        />
      </div>
    </div>
  </Transition>
</template>