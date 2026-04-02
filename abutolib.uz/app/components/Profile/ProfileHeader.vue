<script setup lang="ts">
const auth = useAuthStore()

const joinedDate = computed(() => {
  if (!auth.user?.created_at) return ''
  return new Date(auth.user.created_at).toLocaleDateString('uz-UZ', {
    year: 'numeric', month: 'long', day: 'numeric',
  })
})

const avatarLetter = computed(() => auth.user?.name?.charAt(0).toUpperCase() || '?')
</script>

<template>
  <div class="bg-white dark:bg-[#161b22] rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">

    <div class="relative h-36 bg-gradient-to-br from-blue-600 via-blue-500 to-indigo-600">
      <div
        class="absolute inset-0 opacity-20"
        style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px), radial-gradient(circle at 80% 20%, white 1px, transparent 1px); background-size: 40px 40px;"
      />
      <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-black/10 to-transparent" />
    </div>

    <div class="px-6 pb-5">
      <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">

        <div class="flex items-end gap-4 -mt-10 relative z-10">
          <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-white dark:ring-[#161b22] flex-shrink-0">
            <span class="text-white text-3xl font-black leading-none">{{ avatarLetter }}</span>
          </div>
          <div class="pb-1">
            <h1 class="text-xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">{{ auth.user?.name }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ auth.user?.email }}</p>
          </div>
        </div>

        <div class="flex items-center gap-2 pb-1 sm:pb-2">
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-full text-xs font-bold border border-emerald-200 dark:border-emerald-800">
            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse" />
            Faol
          </span>
          <span class="inline-flex items-center px-3 py-1.5 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 rounded-full text-xs font-semibold border border-gray-200 dark:border-gray-700">
            #{{ auth.user?.id }}
          </span>
        </div>
      </div>

      <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
        </svg>
        {{ joinedDate }} dan a'zo
      </div>
    </div>

  </div>
</template>