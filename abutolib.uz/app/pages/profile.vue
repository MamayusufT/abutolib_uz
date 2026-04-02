<script setup lang="ts">
import ActivityHeatmap from '~/components/ActivityHeatmap.vue'

definePageMeta({ middleware: 'auth' })

const route  = useRoute()
const router = useRouter()

const VALID_TABS = ['profil', 'parol', 'seanslar', 'faoliyat'] as const

const activeTab = computed({
  get: () => {
    const t = route.query.tab as string
    return VALID_TABS.includes(t as any) ? t  : 'profil'
  },
  set: (val: string) => router.replace({ query: { ...route.query, tab: val } }),
})

useSeoMeta({ title: 'Profil' })
</script>

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-[#0d1117]">
    <div class="max-w-5xl mx-auto py-10 px-4 space-y-6">

      <ProfileHeader />

      <div class="bg-white dark:bg-[#161b22] rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
        <ProfileTabs v-model="activeTab" />

        <div class="p-6">
          <ProfileInfo     v-show="activeTab === 'profil'" />
          <ProfilePassword v-show="activeTab === 'parol'" />
          <ProfileSessions v-show="activeTab === 'seanslar'" />

          <div v-show="activeTab === 'faoliyat'">
            <div class="mb-4">
              <h2 class="text-base font-black text-gray-900 dark:text-white">Faoliyat tarixi</h2>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Oxirgi faolligingiz statistikasi</p>
            </div>
            <ActivityHeatmap />
          </div>
        </div>
      </div>

    </div>
  </div>
</template>