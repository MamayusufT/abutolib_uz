<script setup lang="ts">
import { useAuthStore } from '~/stores/auth'
import {
  KeyRound, Mail, Send, Eye, EyeOff, Phone,
  AlertCircle, CheckCircle, Loader2, LogIn, RefreshCw,
} from 'lucide-vue-next'

definePageMeta({ middleware: 'guest', layout: 'auth' })

const config = useRuntimeConfig()

useSeoMeta({
  title: 'Kirish',
  description: 'Hisobingizga kiring.',
  ogUrl: `${config.public.baseURL}/login`,
})

const auth = useAuthStore()

type Tab = 'password' | 'email_otp' | 'telegram_otp'
const tab = ref<Tab>('password')

type TgMode = 'username' | 'phone'
const tgMode = ref<TgMode>('username')

const email    = ref('')
const password = ref('')
const showPass = ref(false)

async function submitPassword() {
  await auth.login(email.value, password.value)
}

const otpEmail   = ref('')
const otpCode    = ref('')
const otpSent    = ref(false)
const otpLoading = ref(false)
const otpError   = ref('')
const otpSuccess = ref('')

async function sendEmailOtp() {
  otpError.value = otpSuccess.value = ''
  otpLoading.value = true
  try {
    await $fetch(`${config.public.apiBase}/auth/otp/email/send`, {
      method: 'POST',
      body: { email: otpEmail.value },
    })
    otpSent.value    = true
    otpSuccess.value = 'Kod emailga yuborildi!'
    startCountdown()
  } catch (e: any) {
    otpError.value = e?.data?.message ?? 'Xatolik yuz berdi.'
  } finally {
    otpLoading.value = false
  }
}

async function verifyEmailOtp() {
  otpError.value   = ''
  otpLoading.value = true
  try {
    const res: any = await $fetch(`${config.public.apiBase}/auth/otp/email/verify`, {
      method: 'POST',
      body: { email: otpEmail.value, code: otpCode.value },
    })
    auth._setSession(res.user, res.token)
    await navigateTo('/dashboard')
  } catch (e: any) {
    otpError.value = e?.data?.errors?.code?.[0] ?? e?.data?.message ?? "Kod noto'g'ri."
  } finally {
    otpLoading.value = false
  }
}

const tgUsername = ref('')
const tgPhone    = ref('')
const tgCode     = ref('')
const tgSent     = ref(false)
const tgLoading  = ref(false)
const tgError    = ref('')
const tgSuccess  = ref('')

async function sendTelegramOtp() {
  tgError.value = tgSuccess.value = ''
  tgLoading.value = true
  try {
    const body = tgMode.value === 'username'
      ? { telegram_username: tgUsername.value }
      : { phone: tgPhone.value }
    await $fetch(`${config.public.apiBase}/auth/otp/telegram/send`, {
      method: 'POST',
      body,
    })
    tgSent.value    = true
    tgSuccess.value = 'Kod Telegram ga yuborildi!'
    startCountdown()
  } catch (e: any) {
    tgError.value = e?.data?.message ?? 'Xatolik yuz berdi.'
  } finally {
    tgLoading.value = false
  }
}

async function verifyTelegramOtp() {
  tgError.value   = ''
  tgLoading.value = true
  try {
    const body = tgMode.value === 'username'
      ? { telegram_username: tgUsername.value, code: tgCode.value }
      : { phone: tgPhone.value, code: tgCode.value }
    const res: any = await $fetch(`${config.public.apiBase}/auth/otp/telegram/verify`, {
      method: 'POST',
      body,
    })
    auth._setSession(res.user, res.token)
    await navigateTo('/dashboard')
  } catch (e: any) {
    tgError.value = e?.data?.errors?.code?.[0] ?? e?.data?.message ?? "Kod noto'g'ri."
  } finally {
    tgLoading.value = false
  }
}

const countdown = ref(0)
let countdownTimer: any = null

function startCountdown() {
  countdown.value = 60
  clearInterval(countdownTimer)
  countdownTimer = setInterval(() => {
    if (countdown.value <= 0) {
      clearInterval(countdownTimer)
      return
    }
    countdown.value--
  }, 1000)
}

function resetState() {
  otpSent.value    = false
  otpLoading.value = false
  otpCode.value    = ''
  otpError.value   = ''
  otpSuccess.value = ''
  tgSent.value     = false
  tgLoading.value  = false
  tgCode.value     = ''
  tgError.value    = ''
  tgSuccess.value  = ''
  countdown.value  = 0
  clearInterval(countdownTimer)
}

watch(tab, resetState)
watch(tgMode, () => {
  tgSent.value  = false
  tgCode.value  = ''
  tgError.value = ''
})

onBeforeUnmount(() => clearInterval(countdownTimer))

const tgInputValid = computed(() =>
  tgMode.value === 'username'
    ? tgUsername.value.length > 2
    : tgPhone.value.length > 7
)
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 to-blue-50 dark:from-slate-950 dark:to-slate-900 px-4">
    <div class="w-full max-w-md">
      <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-800 p-8">

        <div class="flex flex-col items-center mb-6">
          <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200 dark:shadow-none mb-4">
            <KeyRound class="w-7 h-7 text-white" />
          </div>
          <h1 class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-tight">Kirish</h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Hisobingizga kiring</p>
        </div>

        <div class="flex gap-1 p-1 bg-gray-100 dark:bg-slate-800 rounded-xl mb-6">
          <button
            v-for="t in [
              { key: 'password',     icon: KeyRound, label: 'Parol'    },
              { key: 'email_otp',    icon: Mail,     label: 'Email'    },
              { key: 'telegram_otp', icon: Send,     label: 'Telegram' },
            ]"
            :key="t.key"
            class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-bold rounded-lg transition-all"
            :class="tab === t.key
              ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm'
              : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
            @click="tab = t.key as Tab"
          >
            <component :is="t.icon" class="w-3.5 h-3.5" />
            {{ t.label }}
          </button>
        </div>

        <div v-if="tab === 'password'">
          <div
            v-if="auth.error"
            class="mb-5 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-600 dark:text-red-400 flex items-center gap-2"
          >
            <AlertCircle class="w-4 h-4 flex-shrink-0" />
            {{ auth.error }}
          </div>

          <form class="space-y-4" @submit.prevent="submitPassword">
            <div>
              <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
              <input
                v-model="email"
                type="email"
                required
                placeholder="example@mail.com"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800 text-slate-800 dark:text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
              />
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Parol</label>
              <div class="relative">
                <input
                  v-model="password"
                  :type="showPass ? 'text' : 'password'"
                  required
                  placeholder="••••••••"
                  class="w-full px-4 py-3 pr-12 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800 text-slate-800 dark:text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                />
                <button
                  type="button"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition"
                  @click="showPass = !showPass"
                >
                  <Eye v-if="!showPass" class="w-5 h-5" />
                  <EyeOff v-else class="w-5 h-5" />
                </button>
              </div>
            </div>

            <button
              type="submit"
              :disabled="auth.loading"
              class="w-full py-3 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white rounded-xl font-bold text-sm shadow-md shadow-blue-200 dark:shadow-none transition-all active:scale-95 flex items-center justify-center gap-2"
            >
              <Loader2 v-if="auth.loading" class="w-4 h-4 animate-spin" />
              <LogIn v-else class="w-4 h-4" />
              {{ auth.loading ? 'Kirish...' : 'Kirish' }}
            </button>
          </form>
        </div>

        <div v-else-if="tab === 'email_otp'">
          <div
            v-if="otpError"
            class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-600 dark:text-red-400 flex items-center gap-2"
          >
            <AlertCircle class="w-4 h-4 flex-shrink-0" />
            {{ otpError }}
          </div>

          <div
            v-if="otpSuccess"
            class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-sm text-green-600 dark:text-green-400 flex items-center gap-2"
          >
            <CheckCircle class="w-4 h-4 flex-shrink-0" />
            {{ otpSuccess }}
          </div>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
              <div class="flex gap-2">
                <input
                  v-model="otpEmail"
                  type="email"
                  :disabled="otpSent"
                  placeholder="example@mail.com"
                  class="flex-1 px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800 text-slate-800 dark:text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition disabled:opacity-50"
                />
                <button
                  :disabled="otpLoading || !otpEmail || (otpSent && countdown > 0)"
                  class="px-4 py-3 rounded-xl font-bold text-sm transition-all disabled:opacity-50 flex-shrink-0 flex items-center gap-1.5"
                  :class="otpSent ? 'bg-gray-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' : 'bg-blue-600 hover:bg-blue-700 text-white'"
                  @click="sendEmailOtp"
                >
                  <Loader2 v-if="otpLoading" class="w-4 h-4 animate-spin" />
                  <RefreshCw v-else-if="otpSent && countdown > 0" class="w-4 h-4" />
                  <Mail v-else class="w-4 h-4" />
                  <span v-if="otpSent && countdown > 0">{{ countdown }}s</span>
                  <span v-else-if="!otpLoading">Yuborish</span>
                </button>
              </div>
            </div>

            <Transition
              enter-active-class="transition-all duration-300"
              enter-from-class="opacity-0 -translate-y-2"
              enter-to-class="opacity-100 translate-y-0"
            >
              <div v-if="otpSent">
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tasdiqlash kodi</label>
                <input
                  v-model="otpCode"
                  type="text"
                  maxlength="6"
                  placeholder="000000"
                  class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800 text-slate-800 dark:text-white text-center tracking-widest font-black text-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                />
                <button
                  :disabled="otpLoading || otpCode.length < 6"
                  class="w-full mt-3 py-3 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white rounded-xl font-bold text-sm transition-all active:scale-95 flex items-center justify-center gap-2"
                  @click="verifyEmailOtp"
                >
                  <Loader2 v-if="otpLoading" class="w-4 h-4 animate-spin" />
                  <CheckCircle v-else class="w-4 h-4" />
                  {{ otpLoading ? 'Tekshirilmoqda...' : 'Tasdiqlash' }}
                </button>
              </div>
            </Transition>
          </div>
        </div>

        <div v-else-if="tab === 'telegram_otp'">
          <div class="flex gap-1 p-1 bg-gray-100 dark:bg-slate-800 rounded-xl mb-4">
            <button
              class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-bold rounded-lg transition-all"
              :class="tgMode === 'username'
                ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm'
                : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
              @click="tgMode = 'username'"
            >
              <Send class="w-3.5 h-3.5" />
              Username
            </button>
            <button
              class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-bold rounded-lg transition-all"
              :class="tgMode === 'phone'
                ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm'
                : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
              @click="tgMode = 'phone'"
            >
              <Phone class="w-3.5 h-3.5" />
              Telefon
            </button>
          </div>

          <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl text-xs text-blue-700 dark:text-blue-300 flex items-start gap-2">
            <Send class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
            <span>
              Avval botga <strong>/start</strong> yuboring:
              <a href="https://t.me/abutolibot" target="_blank" class="underline font-bold">@abutolibot</a>
            </span>
          </div>

          <div
            v-if="tgError"
            class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-600 dark:text-red-400 flex items-center gap-2"
          >
            <AlertCircle class="w-4 h-4 flex-shrink-0" />
            {{ tgError }}
          </div>

          <div
            v-if="tgSuccess"
            class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-sm text-green-600 dark:text-green-400 flex items-center gap-2"
          >
            <CheckCircle class="w-4 h-4 flex-shrink-0" />
            {{ tgSuccess }}
          </div>

          <div class="space-y-4">
            <Transition
              enter-active-class="transition-all duration-200"
              enter-from-class="opacity-0"
              enter-to-class="opacity-100"
              mode="out-in"
            >
              <div v-if="tgMode === 'username'" key="username">
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Telegram username</label>
                <div class="flex gap-2">
                  <div class="relative flex-1">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm select-none">@</span>
                    <input
                      v-model="tgUsername"
                      type="text"
                      :disabled="tgSent"
                      placeholder="username"
                      class="w-full pl-7 pr-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800 text-slate-800 dark:text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition disabled:opacity-50"
                    />
                  </div>
                  <button
                    :disabled="tgLoading || !tgInputValid || (tgSent && countdown > 0)"
                    class="px-4 py-3 rounded-xl font-bold text-sm transition-all disabled:opacity-50 flex-shrink-0 flex items-center gap-1.5"
                    :class="tgSent ? 'bg-gray-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' : 'bg-blue-600 hover:bg-blue-700 text-white'"
                    @click="sendTelegramOtp"
                  >
                    <Loader2 v-if="tgLoading" class="w-4 h-4 animate-spin" />
                    <RefreshCw v-else-if="tgSent && countdown > 0" class="w-4 h-4" />
                    <Send v-else class="w-4 h-4" />
                    <span v-if="tgSent && countdown > 0">{{ countdown }}s</span>
                    <span v-else-if="!tgLoading">Yuborish</span>
                  </button>
                </div>
              </div>

              <div v-else key="phone">
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Telefon raqam</label>
                <div class="flex gap-2">
                  <div class="relative flex-1">
                    <Phone class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                    <input
                      v-model="tgPhone"
                      type="tel"
                      :disabled="tgSent"
                      placeholder="+998901234567"
                      class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800 text-slate-800 dark:text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition disabled:opacity-50"
                    />
                  </div>
                  <button
                    :disabled="tgLoading || !tgInputValid || (tgSent && countdown > 0)"
                    class="px-4 py-3 rounded-xl font-bold text-sm transition-all disabled:opacity-50 flex-shrink-0 flex items-center gap-1.5"
                    :class="tgSent ? 'bg-gray-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' : 'bg-blue-600 hover:bg-blue-700 text-white'"
                    @click="sendTelegramOtp"
                  >
                    <Loader2 v-if="tgLoading" class="w-4 h-4 animate-spin" />
                    <RefreshCw v-else-if="tgSent && countdown > 0" class="w-4 h-4" />
                    <Send v-else class="w-4 h-4" />
                    <span v-if="tgSent && countdown > 0">{{ countdown }}s</span>
                    <span v-else-if="!tgLoading">Yuborish</span>
                  </button>
                </div>
              </div>
            </Transition>

            <Transition
              enter-active-class="transition-all duration-300"
              enter-from-class="opacity-0 -translate-y-2"
              enter-to-class="opacity-100 translate-y-0"
            >
              <div v-if="tgSent">
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tasdiqlash kodi</label>
                <input
                  v-model="tgCode"
                  type="text"
                  maxlength="6"
                  placeholder="000000"
                  class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800 text-slate-800 dark:text-white text-center tracking-widest font-black text-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                />
                <button
                  :disabled="tgLoading || tgCode.length < 6"
                  class="w-full mt-3 py-3 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white rounded-xl font-bold text-sm transition-all active:scale-95 flex items-center justify-center gap-2"
                  @click="verifyTelegramOtp"
                >
                  <Loader2 v-if="tgLoading" class="w-4 h-4 animate-spin" />
                  <CheckCircle v-else class="w-4 h-4" />
                  {{ tgLoading ? 'Tekshirilmoqda...' : 'Tasdiqlash' }}
                </button>
              </div>
            </Transition>
          </div>
        </div>

        <p class="text-center text-sm text-slate-500 dark:text-slate-400 mt-6">
          Hisob yo'qmi?
          <NuxtLink to="/register" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline">
            Ro'yxatdan o'tish
          </NuxtLink>
        </p>

      </div>
    </div>
  </div>
</template>