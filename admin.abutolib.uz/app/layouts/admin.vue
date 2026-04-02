<template>
  <div class="flex h-dvh bg-white dark:bg-zinc-950 overflow-hidden">

    <aside
      class="fixed inset-y-0 left-0 z-40 flex flex-col w-60 bg-white dark:bg-zinc-900 border-r border-gray-100 dark:border-zinc-800 transition-transform duration-300 ease-in-out lg:translate-x-0"
      :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
      <div class="flex items-center gap-2.5 h-14 px-4 border-b border-gray-100 dark:border-zinc-800 shrink-0">
        <div class="w-7 h-7 rounded-lg bg-zinc-900 dark:bg-white flex items-center justify-center shrink-0">
          <UIcon name="i-heroicons-academic-cap" class="w-4 h-4 text-white dark:text-zinc-900" />
        </div>
        <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 tracking-tight">Admin Panel</span>
        <button
          class="ml-auto lg:hidden p-1 rounded-md text-zinc-400 hover:text-zinc-600 hover:bg-gray-100 dark:hover:bg-zinc-800 dark:hover:text-zinc-200 transition-colors"
          @click="sidebarOpen = false"
        >
          <UIcon name="i-heroicons-x-mark" class="w-4 h-4" />
        </button>
      </div>

      <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5">
        <p class="text-[10px] font-semibold uppercase tracking-widest text-zinc-300 dark:text-zinc-600 px-2.5 pb-2">
          Menu
        </p>
        <NuxtLink
          v-for="item in navItems"
          :key="item.to"
          :to="item.to"
          class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm font-medium transition-colors group"
          :class="isActive(item.to)
            ? 'bg-zinc-900 dark:bg-white text-white dark:text-zinc-900'
            : 'text-zinc-400 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-zinc-100'"
          @click="sidebarOpen = false"
        >
          <span
            class="w-6 h-6 rounded-md flex items-center justify-center shrink-0 transition-colors"
            :class="isActive(item.to)
              ? 'bg-white/15 dark:bg-zinc-900/15'
              : 'bg-gray-100 dark:bg-zinc-800 group-hover:bg-gray-200 dark:group-hover:bg-zinc-700'"
          >
            <UIcon
              :name="item.icon"
              class="w-3.5 h-3.5 transition-colors"
              :class="isActive(item.to)
                ? 'text-white dark:text-zinc-900'
                : 'text-zinc-300 dark:text-zinc-500 group-hover:text-zinc-500 dark:group-hover:text-zinc-300'"
            />
          </span>
          <span class="flex-1">{{ item.label }}</span>
          <span
            v-if="isActive(item.to)"
            class="w-1.5 h-1.5 rounded-full bg-white/60 dark:bg-zinc-900/60 shrink-0"
          />
        </NuxtLink>
      </nav>

      <div class="px-2 py-2.5 border-t border-gray-100 dark:border-zinc-800 shrink-0">
        <div class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg bg-gray-50 dark:bg-zinc-800/60">
          <div class="w-7 h-7 rounded-lg bg-zinc-900 dark:bg-zinc-100 flex items-center justify-center shrink-0">
            <span class="text-xs font-bold text-white dark:text-zinc-900 select-none">
              {{ (auth.user?.name ?? 'A').charAt(0).toUpperCase() }}
            </span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 truncate leading-tight">
              {{ auth.user?.name ?? 'Admin' }}
            </p>
            <p class="text-[11px] text-zinc-400 dark:text-zinc-500 truncate leading-tight">
              {{ auth.user?.email ?? '' }}
            </p>
          </div>
          <button
            class="w-6 h-6 rounded-md flex items-center justify-center text-zinc-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors shrink-0"
            title="Chiqish"
            @click="handleLogout"
          >
            <UIcon name="i-heroicons-arrow-right-on-rectangle" class="w-3.5 h-3.5" />
          </button>
        </div>
      </div>
    </aside>

    <div
      v-if="sidebarOpen"
      class="fixed inset-0 z-30 bg-black/40 backdrop-blur-sm lg:hidden"
      @click="sidebarOpen = false"
    />

    <div class="flex flex-col flex-1 lg:pl-60 min-h-dvh min-w-0">

      <header class="sticky top-0 z-20 flex items-center justify-between h-14 px-4 sm:px-6 bg-white/90 dark:bg-zinc-950/90 backdrop-blur-md border-b border-gray-100 dark:border-zinc-800 shrink-0">
        <div class="flex items-center gap-3 min-w-0">
          <button
            class="lg:hidden w-8 h-8 flex items-center justify-center rounded-lg text-zinc-400 hover:text-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-800 dark:hover:text-zinc-200 transition-colors shrink-0"
            @click="sidebarOpen = true"
          >
            <UIcon name="i-heroicons-bars-3" class="w-5 h-5" />
          </button>
          <div class="min-w-0">
            <h1 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 truncate leading-tight tracking-tight">
              {{ currentPage.title }}
            </h1>
            <p class="text-[11px] text-zinc-400 dark:text-zinc-500 hidden sm:block leading-tight truncate">
              {{ currentPage.description }}
            </p>
          </div>
        </div>

        <div class="flex items-center gap-1.5 shrink-0">
          <button
            class="w-8 h-8 flex items-center justify-center rounded-lg text-zinc-400 hover:text-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-800 dark:hover:text-zinc-200 border border-gray-100 dark:border-zinc-700 transition-colors"
            @click="toggleColorMode"
          >
            <UIcon
              :name="colorMode.value === 'dark' ? 'i-heroicons-sun' : 'i-heroicons-moon'"
              class="w-4 h-4"
            />
          </button>
        </div>
      </header>

      <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-gray-50 dark:bg-zinc-950">
        <slot />
      </main>

    </div>
  </div>
</template>

<script setup lang="ts">
const auth = useAuthStore()
const route = useRoute()
const colorMode = useColorMode()
const sidebarOpen = ref(false)

const navItems = [
  { label: 'Dashboard', to: '/',          icon: 'i-heroicons-squares-2x2'   },
  { label: 'Subjects',  to: '/subjects',  icon: 'i-heroicons-book-open'     },
  { label: 'Topics',    to: '/topics',    icon: 'i-heroicons-document-text' },
  { label: 'Olympiads', to: '/olympiads', icon: 'i-heroicons-trophy'        },
  { label: 'News',      to: '/news',      icon: 'i-heroicons-newspaper'     },
  { label: 'Team',      to: '/team',      icon: 'i-heroicons-user-group'    },
  { label: 'Books',      to: '/books',      icon: 'i-heroicons-book-open'    },
  { label: 'Users',     to: '/users',     icon: 'i-heroicons-users'         },
  { label: 'Roles',     to: '/roles',     icon: 'i-heroicons-shield-check'  },
]

const pageMap: Record<string, { title: string; description: string }> = {
  '/subjects':  { title: 'Subjects',  description: 'Manage academic subjects'     },
  '/topics':    { title: 'Topics',    description: 'Manage topics per subject'    },
  '/olympiads': { title: 'Olympiads', description: 'Manage olympiad competitions' },
  '/news':      { title: 'News',      description: 'Manage news and articles'     },
  '/team':      { title: 'Team',      description: 'Manage team members'          },
  '/books':     { title: 'Books',      description: 'Manage books'                },
  '/users':     { title: 'Users',     description: 'Manage registered users'      },
  '/roles':     { title: 'Roles',     description: 'Manage roles & permissions'   },
  '/':          { title: 'Dashboard', description: 'Overview of your platform'    },
}

const currentPage = computed(() => {
  const match = Object.entries(pageMap).find(([key]) =>
    key === '/' ? route.path === '/' : route.path.startsWith(key)
  )
  return match ? match[1] : { title: 'Dashboard', description: 'Overview' }
})

function isActive(path: string) {
  if (path === '/') return route.path === '/'
  return route.path.startsWith(path)
}

function toggleColorMode() {
  colorMode.preference = colorMode.value === 'dark' ? 'light' : 'dark'
}

function handleLogout() {
  auth.logout()
}

onMounted(() => {
  auth.fetchUser()
})
</script>