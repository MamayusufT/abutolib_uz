<script setup lang="ts">
const { $api } = useApi()

interface Session {
  id: number
  token_id: string
  device_name: string
  device_type: string
  browser: string
  platform: string
  ip_address: string
  last_active_at: string
  is_current: boolean
}

const sessions    = ref<Session[]>([])
const loading     = ref(false)
const revokingId  = ref<number | null>(null)
const revokingAll = ref(false)

async function fetchSessions() {
  loading.value = true
  try {
    const res: any = await $api('/auth/sessions')
    sessions.value = res
  } catch {
    //
  } finally {
    loading.value = false
  }
}

async function revokeSession(id: number) {
  revokingId.value = id
  try {
    await $api(`/auth/sessions/${id}`, { method: 'DELETE' })
    sessions.value = sessions.value.filter(s => s.id !== id)
  } catch {
    //
  } finally {
    revokingId.value = null
  }
}

async function revokeAllSessions() {
  revokingAll.value = true
  try {
    await $api('/auth/sessions', { method: 'DELETE' })
    await fetchSessions()
  } catch {
    //
  } finally {
    revokingAll.value = false
  }
}

function timeAgo(dateStr: string) {
  const diff  = Date.now() - new Date(dateStr).getTime()
  const mins  = Math.floor(diff / 60000)
  const hours = Math.floor(diff / 3600000)
  const days  = Math.floor(diff / 86400000)
  if (mins < 1)   return 'Hozirgina'
  if (mins < 60)  return `${mins} daqiqa oldin`
  if (hours < 24) return `${hours} soat oldin`
  return `${days} kun oldin`
}

function deviceIcon(type: string) {
  if (type === 'mobile') return 'mobile'
  if (type === 'tablet') return 'tablet'
  return 'desktop'
}

const nonCurrentSessions = computed(() => sessions.value.filter(s => !s.is_current))

onMounted(() => fetchSessions())
</script>

<template>
  <div class="space-y-1">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="text-base font-black text-gray-900 dark:text-white">Faol seanslar</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Barcha kirgan qurilmalar ro'yxati</p>
      </div>
      <button
        v-if="nonCurrentSessions.length > 0"
        :disabled="revokingAll"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-red-500 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors disabled:opacity-50"
        @click="revokeAllSessions"
      >
        <svg v-if="revokingAll" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
        </svg>
        Barchasini tugatish
      </button>
    </div>

    <div v-if="loading" class="space-y-3 py-2">
      <div v-for="i in 2" :key="i" class="flex items-center gap-4 animate-pulse p-3">
        <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-xl flex-shrink-0" />
        <div class="flex-1 space-y-2">
          <div class="h-3 bg-gray-100 dark:bg-gray-800 rounded w-1/3" />
          <div class="h-2.5 bg-gray-100 dark:bg-gray-800 rounded w-1/2" />
        </div>
      </div>
    </div>

    <div v-else-if="sessions.length === 0" class="py-12 text-center text-gray-400 dark:text-gray-500 text-sm">
      Hech qanday seans topilmadi
    </div>

    <TransitionGroup name="session" tag="div" class="space-y-1">
      <div
        v-for="session in sessions"
        :key="session.id"
        class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors"
        :class="session.is_current ? 'bg-blue-50/50 dark:bg-blue-900/10 ring-1 ring-blue-100 dark:ring-blue-900/30' : ''"
      >
        <div
          class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
          :class="session.is_current ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-gray-100 dark:bg-gray-800'"
        >
          <svg v-if="deviceIcon(session.device_type) === 'desktop'" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" :class="session.is_current ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25A2.25 2.25 0 015.25 3h13.5A2.25 2.25 0 0121 5.25z" />
          </svg>
          <svg v-else-if="deviceIcon(session.device_type) === 'mobile'" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" :class="session.is_current ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" :class="session.is_current ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5h3m-6.75 2.25h10.5a2.25 2.25 0 002.25-2.25v-15a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 4.5v15a2.25 2.25 0 002.25 2.25z" />
          </svg>
        </div>

        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 flex-wrap">
            <span class="text-sm font-bold text-gray-900 dark:text-white truncate">
              {{ session.browser }} — {{ session.platform }}
            </span>
            <span v-if="session.is_current" class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-full text-[10px] font-bold uppercase tracking-wide">
              <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse" />
              Joriy
            </span>
          </div>
          <div class="flex items-center gap-3 mt-1 flex-wrap">
            <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
              </svg>
              {{ session.ip_address }}
            </span>
            <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              {{ timeAgo(session.last_active_at) }}
            </span>
          </div>
        </div>

        <button
          v-if="!session.is_current"
          :disabled="revokingId === session.id"
          class="flex-shrink-0 p-2 rounded-xl text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors disabled:opacity-50"
          @click="revokeSession(session.id)"
        >
          <svg v-if="revokingId === session.id" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
          </svg>
        </button>
      </div>
    </TransitionGroup>
  </div>
</template>

<style scoped>
.session-leave-active { transition: all 0.3s ease; }
.session-leave-to { opacity: 0; transform: translateX(20px); }
</style>