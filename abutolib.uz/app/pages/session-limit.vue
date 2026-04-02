<script setup lang="ts">
import { useAuthStore } from '~/stores/auth'

const auth   = useAuthStore()
const router = useRouter()

onMounted(() => {
  if (!auth.sessionLimitData) {
    router.replace('/login')
  }
})

const sessions  = computed(() => auth.sessionLimitData?.sessions || [])
const revokingId = ref<number | null>(null)

async function selectAndLogin(sessionId: number) {
  revokingId.value = sessionId
  try {
    await auth.revokeAndLogin(sessionId)
    await router.push('/dashboard')
  } catch {
    revokingId.value = null
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
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 to-orange-50 dark:from-slate-950 dark:to-slate-900 px-4 py-8">
    <div class="w-full max-w-md">

      <!-- Card -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden">

        <!-- Header -->
        <div class="p-6 border-b border-gray-100 dark:border-gray-800 bg-orange-50 dark:bg-orange-900/10">
          <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
              </svg>
            </div>
            <div>
              <h1 class="text-lg font-black text-slate-800 dark:text-white">Qurilmalar chegarasiga yetdingiz</h1>
              <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Siz allaqachon <span class="font-bold text-orange-500">3 ta qurilmadan</span> kirgansiiz. Yangi qurilmadan kirish uchun quyidagilardan birini tanlang va o'sha seansni tugatish orqali davom eting.
              </p>
            </div>
          </div>
        </div>

        <!-- Sessions list -->
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
          <div
            v-for="session in sessions"
            :key="session.id"
            class="p-4 hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors"
          >
            <div class="flex items-center gap-3">
              <!-- Icon -->
              <div class="w-11 h-11 bg-gray-100 dark:bg-slate-800 rounded-xl flex items-center justify-center flex-shrink-0">
                <!-- Desktop -->
                <svg v-if="session.device_type === 'desktop'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25A2.25 2.25 0 015.25 3h13.5A2.25 2.25 0 0121 5.25z" />
                </svg>
                <!-- Mobile -->
                <svg v-else-if="session.device_type === 'mobile'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                </svg>
                <!-- Tablet -->
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5h3m-6.75 2.25h10.5a2.25 2.25 0 002.25-2.25v-15a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 4.5v15a2.25 2.25 0 002.25 2.25z" />
                </svg>
              </div>

              <!-- Info -->
              <div class="flex-1 min-w-0">
                <div class="text-sm font-bold text-slate-800 dark:text-white truncate">
                  {{ session.browser }} — {{ session.platform }}
                </div>
                <div class="flex items-center gap-3 mt-0.5 flex-wrap">
                  <span class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                    </svg>
                    {{ session.ip_address }}
                  </span>
                  <span class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ timeAgo(session.last_active_at) }}
                  </span>
                </div>
              </div>

              <!-- Revoke & Login button -->
              <button
                :disabled="revokingId !== null"
                class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-red-500 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                @click="selectAndLogin(session.id)"
              >
                <svg v-if="revokingId === session.id" class="animate-spin w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                </svg>
                Tugatish
              </button>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-slate-800/50">
          <p class="text-xs text-center text-slate-400 dark:text-slate-500">
            Tanlangan qurilmadan chiqiladi va siz avtomatik kirasiz
          </p>
          <div class="mt-3 text-center">
            <NuxtLink to="/login" class="text-sm font-semibold text-blue-600 dark:text-blue-400 hover:underline">
              ← Loginga qaytish
            </NuxtLink>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>