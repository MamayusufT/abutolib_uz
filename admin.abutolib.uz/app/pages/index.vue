<template>
  <NuxtLayout name="admin">
    <div class="space-y-6">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <NuxtLink
          v-for="stat in stats"
          :key="stat.label"
          :to="stat.to"
          class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-4 sm:p-5 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group"
        >
          <div class="flex items-start justify-between gap-3">
            <div
              class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
              :class="stat.bg"
            >
              <UIcon :name="stat.icon" class="w-5 h-5" :class="stat.color" />
            </div>
            <UIcon name="i-heroicons-arrow-up-right" class="w-4 h-4 text-gray-300 dark:text-gray-700 group-hover:text-gray-400 transition-colors" />
          </div>
          <div class="mt-3">
            <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
              <span v-if="loading">
                <span class="inline-block w-12 h-7 bg-gray-100 dark:bg-gray-800 rounded-lg animate-pulse" />
              </span>
              <span v-else>{{ stat.count }}</span>
            </p>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5 font-medium">{{ stat.label }}</p>
          </div>
        </NuxtLink>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <UCard>
          <template #header>
            <div class="flex items-center justify-between">
              <p class="text-sm font-semibold text-gray-900 dark:text-white">Recent News</p>
              <NuxtLink to="/news" class="text-xs text-primary-500 hover:text-primary-600 transition-colors font-medium">View all</NuxtLink>
            </div>
          </template>
          <div v-if="recentNews.length" class="space-y-3">
            <div v-for="item in recentNews" :key="item.id" class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl overflow-hidden shrink-0 bg-gray-100 dark:bg-gray-800">
                <img v-if="item.image" :src="item.image" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full flex items-center justify-center">
                  <UIcon name="i-heroicons-newspaper" class="w-4 h-4 text-gray-300" />
                </div>
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ item.title }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ item.category ?? 'General' }}</p>
              </div>
              <UBadge :label="item.status" :color="item.status === 'published' ? 'success' : 'warning'" variant="soft" size="xs" class="capitalize shrink-0" />
            </div>
          </div>
          <div v-else class="flex flex-col items-center justify-center py-8 gap-2 text-gray-400">
            <UIcon name="i-heroicons-newspaper" class="w-8 h-8 opacity-40" />
            <p class="text-xs">No news articles yet</p>
          </div>
        </UCard>

        <UCard>
          <template #header>
            <div class="flex items-center justify-between">
              <p class="text-sm font-semibold text-gray-900 dark:text-white">Upcoming Olympiads</p>
              <NuxtLink to="/olympiads" class="text-xs text-primary-500 hover:text-primary-600 transition-colors font-medium">View all</NuxtLink>
            </div>
          </template>
          <div v-if="upcomingOlympiads.length" class="space-y-3">
            <div v-for="item in upcomingOlympiads" :key="item.id" class="flex items-center gap-3">
              <div
                class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                :class="typeBg(item.type)"
              >
                <UIcon :name="typeIcon(item.type)" class="w-4 h-4" :class="typeColor(item.type)" />
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ item.name_uz }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ item.duration_label }} · {{ fmtDate(item.starts_at) }}</p>
              </div>
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium shrink-0" :class="statusClass(item.status)">
                <span class="w-1.5 h-1.5 rounded-full" :class="statusDot(item.status)" />
                {{ item.status }}
              </span>
            </div>
          </div>
          <div v-else class="flex flex-col items-center justify-center py-8 gap-2 text-gray-400">
            <UIcon name="i-heroicons-trophy" class="w-8 h-8 opacity-40" />
            <p class="text-xs">No upcoming olympiads</p>
          </div>
        </UCard>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <UCard>
          <template #header>
            <div class="flex items-center justify-between">
              <p class="text-sm font-semibold text-gray-900 dark:text-white">Subjects</p>
              <NuxtLink to="/subjects" class="text-xs text-primary-500 hover:text-primary-600 transition-colors font-medium">Manage</NuxtLink>
            </div>
          </template>
          <div v-if="recentSubjects.length" class="space-y-2">
            <div v-for="s in recentSubjects" :key="s.id" class="flex items-center gap-3">
              <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0" :style="{ background: s.color ?? '#6366f1' }">
                <UIcon name="i-heroicons-book-open" class="w-3.5 h-3.5 text-white" />
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ s.name }}</p>
              </div>
              <span class="text-xs text-gray-400 shrink-0">{{ s.topics_count ?? 0 }} topics</span>
            </div>
          </div>
          <div v-else class="flex flex-col items-center justify-center py-8 gap-2 text-gray-400">
            <UIcon name="i-heroicons-book-open" class="w-8 h-8 opacity-40" />
            <p class="text-xs">No subjects yet</p>
          </div>
        </UCard>

        <UCard>
          <template #header>
            <div class="flex items-center justify-between">
              <p class="text-sm font-semibold text-gray-900 dark:text-white">Team Members</p>
              <NuxtLink to="/team" class="text-xs text-primary-500 hover:text-primary-600 transition-colors font-medium">Manage</NuxtLink>
            </div>
          </template>
          <div v-if="teamMembers.length" class="space-y-2">
            <div v-for="m in teamMembers" :key="m.id" class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-800 shrink-0">
                <img v-if="m.avatar" :src="m.avatar" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full flex items-center justify-center">
                  <span class="text-xs font-semibold text-gray-400">{{ m.name.charAt(0).toUpperCase() }}</span>
                </div>
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ m.name }}</p>
                <p class="text-xs text-gray-400 truncate">{{ m.role ?? '—' }}</p>
              </div>
              <span class="w-2 h-2 rounded-full shrink-0" :class="m.is_active ? 'bg-green-400' : 'bg-gray-300'" />
            </div>
          </div>
          <div v-else class="flex flex-col items-center justify-center py-8 gap-2 text-gray-400">
            <UIcon name="i-heroicons-user-group" class="w-8 h-8 opacity-40" />
            <p class="text-xs">No team members yet</p>
          </div>
        </UCard>

        <UCard>
          <template #header>
            <p class="text-sm font-semibold text-gray-900 dark:text-white">Quick Actions</p>
          </template>
          <div class="space-y-2">
            <NuxtLink
              v-for="action in quickActions"
              :key="action.to"
              :to="action.to"
              class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group"
            >
              <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" :class="action.bg">
                <UIcon :name="action.icon" class="w-4 h-4" :class="action.color" />
              </div>
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">{{ action.label }}</span>
              <UIcon name="i-heroicons-chevron-right" class="w-4 h-4 text-gray-300 dark:text-gray-700 group-hover:text-gray-400 ml-auto transition-colors" />
            </NuxtLink>
          </div>
        </UCard>
      </div>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
const api = useApi()
const loading = ref(true)

const counts = ref({ subjects: 0, topics: 0, olympiads: 0, users: 0 })
const recentNews = ref<any[]>([])
const upcomingOlympiads = ref<any[]>([])
const recentSubjects = ref<any[]>([])
const teamMembers = ref<any[]>([])

const stats = computed(() => [
  { label: 'Subjects',  count: counts.value.subjects,  icon: 'i-heroicons-book-open',     bg: 'bg-primary-50 dark:bg-primary-900/20', color: 'text-primary-500', to: '/subjects' },
  { label: 'Topics',    count: counts.value.topics,    icon: 'i-heroicons-document-text', bg: 'bg-purple-50 dark:bg-purple-900/20',  color: 'text-purple-500',  to: '/topics' },
  { label: 'Olympiads', count: counts.value.olympiads, icon: 'i-heroicons-trophy',         bg: 'bg-amber-50 dark:bg-amber-900/20',    color: 'text-amber-500',   to: '/olympiads' },
  { label: 'Users',     count: counts.value.users,     icon: 'i-heroicons-users',          bg: 'bg-blue-50 dark:bg-blue-900/20',      color: 'text-blue-500',    to: '/users' },
])

const quickActions = [
  { label: 'Add Subject',  to: '/subjects/create',  icon: 'i-heroicons-plus-circle',   bg: 'bg-primary-50 dark:bg-primary-900/20', color: 'text-primary-500' },
  { label: 'Add Topic',    to: '/topics/create',    icon: 'i-heroicons-plus-circle',   bg: 'bg-purple-50 dark:bg-purple-900/20',  color: 'text-purple-500' },
  { label: 'New Olympiad', to: '/olympiads/create', icon: 'i-heroicons-trophy',         bg: 'bg-amber-50 dark:bg-amber-900/20',    color: 'text-amber-500' },
  { label: 'Write News',   to: '/news/create',      icon: 'i-heroicons-newspaper',     bg: 'bg-green-50 dark:bg-green-900/20',    color: 'text-green-500' },
  { label: 'Add User',     to: '/users/create',     icon: 'i-heroicons-user-plus',     bg: 'bg-blue-50 dark:bg-blue-900/20',      color: 'text-blue-500' },
]

function statusDot(s: string) { return { upcoming: 'bg-blue-400', live: 'bg-green-500', finished: 'bg-gray-400' }[s] ?? 'bg-gray-300' }
function statusClass(s: string) { return { upcoming: 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400', live: 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400', finished: 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' }[s] ?? '' }
function typeBg(t: string) { return { quiz: 'bg-purple-50 dark:bg-purple-900/20', olympiad: 'bg-amber-50 dark:bg-amber-900/20', exam: 'bg-blue-50 dark:bg-blue-900/20' }[t] ?? 'bg-gray-50 dark:bg-gray-800' }
function typeColor(t: string) { return { quiz: 'text-purple-500', olympiad: 'text-amber-500', exam: 'text-blue-500' }[t] ?? 'text-gray-400' }
function typeIcon(t: string) { return { quiz: 'i-heroicons-academic-cap', olympiad: 'i-heroicons-trophy', exam: 'i-heroicons-clipboard-document-check' }[t] ?? 'i-heroicons-question-mark-circle' }
function fmtDate(d: string) { return new Date(d).toLocaleDateString('en-GB', { day: '2-digit', month: 'short' }) }

onMounted(async () => {
  try {
    const [s, t, o, u, news, olympiads, subjects, team] = await Promise.allSettled([
      api.get<any>('/admin/subjects?per_page=1'),
      api.get<any>('/admin/topics?per_page=1'),
      api.get<any>('/admin/olympiads?per_page=1'),
      api.get<any>('/admin/users?per_page=1'),
      api.get<any>('/admin/news?per_page=5'),
      api.get<any>('/admin/olympiads?per_page=5'),
      api.get<any>('/admin/subjects?per_page=5'),
      api.get<any>('/admin/team-members?per_page=5'),
    ])

    if (s.status === 'fulfilled') counts.value.subjects = s.value?.meta?.total ?? 0
    if (t.status === 'fulfilled') counts.value.topics = t.value?.meta?.total ?? 0
    if (o.status === 'fulfilled') counts.value.olympiads = o.value?.meta?.total ?? 0
    if (u.status === 'fulfilled') counts.value.users = u.value?.meta?.total ?? 0
    if (news.status === 'fulfilled') recentNews.value = news.value?.data ?? []
    if (olympiads.status === 'fulfilled') upcomingOlympiads.value = olympiads.value?.data ?? []
    if (subjects.status === 'fulfilled') recentSubjects.value = subjects.value?.data ?? []
    if (team.status === 'fulfilled') teamMembers.value = team.value?.data ?? []
  } finally {
    loading.value = false
  }
})
</script>