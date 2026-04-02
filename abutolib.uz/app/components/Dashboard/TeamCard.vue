<script setup lang="ts">
defineProps<{
  member: {
    id: number
    name: string
    role: string
    avatar: string | null
    email: string | null
    telegram: string | null
    instagram: string | null
    bio: string | null
  }
}>()
</script>

<template>
  <div class="flex flex-col items-center text-center p-5 bg-gray-50 dark:bg-slate-800/50 rounded-2xl hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-colors group">
    <!-- Avatar -->
    <div class="w-16 h-16 rounded-2xl overflow-hidden mb-3 shadow-md flex-shrink-0">
      <img
        v-if="member.avatar"
        :src="member.avatar"
        :alt="member.name"
        class="w-full h-full object-cover"
      />
      <div v-else class="w-full h-full bg-blue-600 flex items-center justify-center">
        <span class="text-white text-xl font-black">{{ member.name.charAt(0) }}</span>
      </div>
    </div>

    <!-- Info -->
    <div class="font-black text-sm text-slate-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
      {{ member.name }}
    </div>
    <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-semibold">{{ member.role }}</div>

    <p v-if="member.bio" class="text-xs text-slate-400 dark:text-slate-500 mt-2 line-clamp-2 leading-relaxed">
      {{ member.bio }}
    </p>

    <!-- Social -->
    <div class="flex items-center gap-2 mt-3">
      <a v-if="member.telegram" :href="`https://t.me/${member.telegram}`" target="_blank"
        class="w-7 h-7 bg-sky-100 dark:bg-sky-900/30 text-sky-500 rounded-lg flex items-center justify-center hover:bg-sky-200 dark:hover:bg-sky-900/50 transition-colors">
        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
        </svg>
      </a>
      <a v-if="member.instagram" :href="`https://instagram.com/${member.instagram}`" target="_blank"
        class="w-7 h-7 bg-pink-100 dark:bg-pink-900/30 text-pink-500 rounded-lg flex items-center justify-center hover:bg-pink-200 dark:hover:bg-pink-900/50 transition-colors">
        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/>
        </svg>
      </a>
      <a v-if="member.email" :href="`mailto:${member.email}`"
        class="w-7 h-7 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 rounded-lg flex items-center justify-center hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
        </svg>
      </a>
    </div>
  </div>
</template>