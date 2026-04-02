<script setup lang="ts">
const auth = useAuthStore()
const { $api } = useApi()

const isEditing   = ref(false)
const isSaving    = ref(false)
const saveSuccess = ref(false)
const saveError   = ref<string | null>(null)

const form = reactive({
  name:  auth.user?.name  || '',
  email: auth.user?.email || '',
})

function startEdit() {
  form.name  = auth.user?.name  || ''
  form.email = auth.user?.email || ''
  isEditing.value   = true
  saveError.value   = null
  saveSuccess.value = false
}

function cancelEdit() {
  isEditing.value = false
  saveError.value = null
}

async function saveProfile() {
  isSaving.value  = true
  saveError.value = null
  try {
    const res: any = await $api('/auth/profile', {
      method: 'PUT',
      body: { name: form.name, email: form.email },
    })
    auth.user         = res.user || res
    isEditing.value   = false
    saveSuccess.value = true
    setTimeout(() => (saveSuccess.value = false), 3000)
  } catch (err: any) {
    saveError.value = err?.data?.message || 'Saqlashda xatolik'
  } finally {
    isSaving.value = false
  }
}
</script>

<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-base font-black text-gray-900 dark:text-white">Shaxsiy ma'lumotlar</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Ism va email manzilini tahrirlang</p>
      </div>
      <button
        v-if="!isEditing"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors"
        @click="startEdit"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
        </svg>
        Tahrirlash
      </button>
    </div>

    <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 -translate-y-1" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
      <div v-if="saveSuccess" class="flex items-center gap-2 p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl text-sm text-emerald-700 dark:text-emerald-400 font-semibold">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
          <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
        </svg>
        Profil muvaffaqiyatli saqlandi!
      </div>
    </Transition>

    <div v-if="saveError" class="flex items-center gap-2 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-600 dark:text-red-400">
      {{ saveError }}
    </div>

    <template v-if="!isEditing">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="p-4 bg-gray-50 dark:bg-gray-800/60 rounded-xl border border-gray-100 dark:border-gray-700">
          <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Ism Familiya</div>
          <div class="text-sm font-bold text-gray-900 dark:text-white">{{ auth.user?.name }}</div>
        </div>
        <div class="p-4 bg-gray-50 dark:bg-gray-800/60 rounded-xl border border-gray-100 dark:border-gray-700">
          <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Email</div>
          <div class="text-sm font-bold text-gray-900 dark:text-white">{{ auth.user?.email }}</div>
        </div>
      </div>
    </template>

    <template v-else>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Ism Familiya</label>
          <input v-model="form.name" type="text" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
          <input v-model="form.email" type="email" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
        </div>
      </div>
      <div class="flex gap-3 pt-1">
        <button
          :disabled="isSaving"
          class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white rounded-xl font-bold text-sm transition-all active:scale-95"
          @click="saveProfile"
        >
          <svg v-if="isSaving" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
          </svg>
          {{ isSaving ? 'Saqlanmoqda...' : 'Saqlash' }}
        </button>
        <button
          class="px-5 py-2.5 rounded-xl font-bold text-sm text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all active:scale-95"
          @click="cancelEdit"
        >
          Bekor qilish
        </button>
      </div>
    </template>

    <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-4 bg-red-50 dark:bg-red-900/10 rounded-xl border border-red-100 dark:border-red-900/30">
        <div>
          <div class="text-sm font-bold text-gray-900 dark:text-white">Hisobdan chiqish</div>
          <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Joriy sessiyani yakunlash</div>
        </div>
        <button
          class="inline-flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-transparent hover:bg-red-100 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800 rounded-xl font-bold text-sm transition-all active:scale-95"
          @click="useAuthStore().logout()"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
          </svg>
          Chiqish
        </button>
      </div>
    </div>
  </div>
</template>