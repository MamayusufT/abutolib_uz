<script setup lang="ts">
const { $api } = useApi()

const isChanging     = ref(false)
const success        = ref(false)
const error          = ref<string | null>(null)
const showCurrent    = ref(false)
const showNew        = ref(false)
const showConfirm    = ref(false)

const form = reactive({
  current_password:      '',
  password:              '',
  password_confirmation: '',
})

const isDisabled = computed(() =>
  isChanging.value || !form.current_password || !form.password || !form.password_confirmation
)

async function changePassword() {
  isChanging.value = true
  error.value      = null
  try {
    await $api('/auth/password', {
      method: 'PUT',
      body: {
        current_password:      form.current_password,
        password:              form.password,
        password_confirmation: form.password_confirmation,
      },
    })
    form.current_password      = ''
    form.password              = ''
    form.password_confirmation = ''
    success.value = true
    setTimeout(() => (success.value = false), 3000)
  } catch (err: any) {
    error.value = err?.data?.message || "Parolni o'zgartirishda xatolik"
  } finally {
    isChanging.value = false
  }
}
</script>

<template>
  <div class="space-y-4">
    <div>
      <h2 class="text-base font-black text-gray-900 dark:text-white">Parolni o'zgartirish</h2>
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Xavfsizlik uchun kuchli parol tanlang</p>
    </div>

    <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 -translate-y-1" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
      <div v-if="success" class="flex items-center gap-2 p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl text-sm text-emerald-700 dark:text-emerald-400 font-semibold">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
          <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
        </svg>
        Parol muvaffaqiyatli o'zgartirildi!
      </div>
    </Transition>

    <div v-if="error" class="flex items-center gap-2 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-600 dark:text-red-400">
      {{ error }}
    </div>

    <div>
      <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Joriy parol</label>
      <div class="relative">
        <input
          v-model="form.current_password"
          :type="showCurrent ? 'text' : 'password'"
          placeholder="••••••••"
          class="w-full px-4 py-3 pr-12 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
        />
        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition" @click="showCurrent = !showCurrent">
          <ProfilePasswordEyeIcon :show="showCurrent" />
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Yangi parol</label>
        <div class="relative">
          <input
            v-model="form.password"
            :type="showNew ? 'text' : 'password'"
            placeholder="Min. 8 ta belgi"
            class="w-full px-4 py-3 pr-12 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
          />
          <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition" @click="showNew = !showNew">
            <ProfilePasswordEyeIcon :show="showNew" />
          </button>
        </div>
      </div>
      <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Yangi parolni tasdiqlash</label>
        <div class="relative">
          <input
            v-model="form.password_confirmation"
            :type="showConfirm ? 'text' : 'password'"
            placeholder="Qayta kiriting"
            class="w-full px-4 py-3 pr-12 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
          />
          <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition" @click="showConfirm = !showConfirm">
            <ProfilePasswordEyeIcon :show="showConfirm" />
          </button>
        </div>
      </div>
    </div>

    <button
      :disabled="isDisabled"
      class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-blue-600 hover:bg-gray-700 dark:hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-xl font-bold text-sm transition-all active:scale-95"
      @click="changePassword"
    >
      <svg v-if="isChanging" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
      </svg>
      {{ isChanging ? "O'zgartirilmoqda..." : "Parolni o'zgartirish" }}
    </button>
  </div>
</template>