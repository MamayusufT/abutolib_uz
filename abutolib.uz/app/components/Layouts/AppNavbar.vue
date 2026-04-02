<script setup lang="ts">
import {
  Home, UserCircle, Trophy, Newspaper,
  BookOpen, Phone, Lock, LogOut, LayoutDashboard,
  ChartColumnDecreasing, Menu, X, ShieldCheck,
  Brain, BookText, Subscript, Swords, FlameKindling,
  Medal, Code2, ChevronDown
} from 'lucide-vue-next'

const isMenuOpen = ref(false)
const isDropdownOpen = ref(false)
const openSubmenu = ref<string | null>(null)

const auth = useAuthStore()
const route = useRoute()

const navLinks = [
  { name: 'Bosh sahifa', href: '/', icon: Home },
  { name: 'Biz haqimizda', href: '/about', icon: UserCircle },
  {
    name: 'Musobaqalar',
    icon: Trophy,
    href: null,
    children: [
      { name: 'Olimpiadalar', href: '/olympiads', icon: Swords },
      { name: 'Arena', href: '/arena', icon: FlameKindling },
      { name: 'Turnirlar', href: '/tournaments', icon: Medal },
    ]
  },
  { name: 'Yangiliklar', href: '/news', icon: Newspaper },
  { name: 'Fanlar', href: '/subjects', icon: BookOpen },
  { name: 'Kitoblar', href: '/books', icon: BookText },
  { name: 'Ai', href: '/ai', icon: Brain },
  { name: 'Narxlar', href: '/subscribe', icon: Subscript },
  { name: "Bog'lanish", href: '/contact', icon: Phone },
]

const bottomNavLinks = [
  { name: 'Bosh', href: '/', icon: Home },
  { name: 'Fanlar', href: '/subjects', icon: BookOpen },
  { name: 'Musobaqalar', href: '/olympiads', icon: Trophy },
  { name: 'Yangiliklar', href: '/news', icon: Newspaper },
]

const competitionRoutes = ['/contests', '/arena', '/tournaments', '/hackathons']

function isSubmenuActive(children: { href: string }[]) {
  return children.some(c => route.path === c.href)
}

function toggleSubmenu(name: string) {
  openSubmenu.value = openSubmenu.value === name ? null : name
}

function toggleDropdown() {
  isDropdownOpen.value = !isDropdownOpen.value
}

function closeDropdown() {
  isDropdownOpen.value = false
}

function closeAll() {
  isMenuOpen.value = false
  openSubmenu.value = null
}

async function handleLogout() {
  closeDropdown()
  closeAll()
  await auth.logout()
}

watch(() => route.path, () => {
  isMenuOpen.value = false
  openSubmenu.value = null
  isDropdownOpen.value = false
})
</script>

<template>
  <header class="border-b border-gray-100 dark:border-gray-800 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md sticky top-0 z-50 w-full">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16 sm:h-20">

        <!-- Logo -->
        <div class="shrink-0 p-2">
          <NuxtLink to="/" class="flex items-center group gap-2 sm:gap-3">
            <div class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center transition-all duration-300 group-hover:rotate-3 group-hover:scale-110">
              <img src="/logo.png" alt="Abutolib Logo" class="w-full h-full object-contain" />
            </div>
            <div class="flex flex-col justify-center border-l-2 border-slate-200 dark:border-slate-700 pl-3">
              <div class="flex items-baseline leading-none">
                <span class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tighter">Abutolib</span>
                <span class="text-sm sm:text-base font-black text-transparent bg-clip-text bg-gradient-to-br from-blue-600 to-cyan-500 dark:from-blue-400 dark:to-cyan-300 ml-0.5">.uz</span>
              </div>
              <div class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 tracking-[0.15em] uppercase mt-1">Test tizimi</div>
            </div>
          </NuxtLink>
        </div>

        <!-- Desktop Nav -->
        <nav class="hidden lg:flex items-center space-x-1">
          <template v-for="link in navLinks" :key="link.name">

            <!-- Submenu item -->
            <div v-if="link.children" class="relative">
              <button
                @click="toggleSubmenu(link.name)"
                :class="[
                  'px-3 py-2 rounded-xl text-sm font-semibold flex items-center gap-1.5 transition-all duration-200',
                  isSubmenuActive(link.children) || openSubmenu === link.name
                    ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'
                    : 'text-slate-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-slate-900 dark:hover:text-white'
                ]"
              >
                <component :is="link.icon" class="w-4 h-4 flex-shrink-0" />
                {{ link.name }}
                <ChevronDown class="w-3.5 h-3.5 transition-transform duration-200" :class="openSubmenu === link.name ? 'rotate-180' : ''" />
              </button>

              <Transition
                enter-active-class="transition duration-150 ease-out"
                enter-from-class="opacity-0 scale-95 -translate-y-1"
                enter-to-class="opacity-100 scale-100 translate-y-0"
                leave-active-class="transition duration-100 ease-in"
                leave-from-class="opacity-100 scale-100 translate-y-0"
                leave-to-class="opacity-0 scale-95 -translate-y-1"
              >
                <div
                  v-if="openSubmenu === link.name"
                  class="absolute left-0 top-full mt-2 w-48 bg-white dark:bg-slate-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-xl overflow-hidden z-50"
                >
                  <div class="p-1.5">
                    <NuxtLink
                      v-for="child in link.children"
                      :key="child.href"
                      :to="child.href"
                      @click="openSubmenu = null"
                      class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                      active-class="bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400"
                    >
                      <component :is="child.icon" class="w-4 h-4 flex-shrink-0" />
                      {{ child.name }}
                    </NuxtLink>
                  </div>
                </div>
              </Transition>
            </div>

            <!-- Normal item -->
            <NuxtLink
              v-else
              :to="link.href"
              class="px-3 py-2 rounded-xl text-sm font-semibold flex items-center gap-1.5 transition-all duration-200 text-slate-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-slate-900 dark:hover:text-white"
              active-class="bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400"
            >
              <component :is="link.icon" class="w-4 h-4 flex-shrink-0" />
              {{ link.name }}
            </NuxtLink>

          </template>
        </nav>

        <!-- Right side -->
        <div class="flex items-center gap-1 sm:gap-2">
          <UColorModeButton />

          <!-- Desktop auth -->
          <div class="hidden lg:flex items-center border-l dark:border-gray-700 ml-1 pl-3">

            <!-- Not logged in -->
            <NuxtLink
              v-if="!auth.isLoggedIn"
              to="/login"
              class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#121826] dark:bg-blue-600 hover:bg-slate-700 dark:hover:bg-blue-700 text-white rounded-xl font-bold text-sm shadow-md transition-all active:scale-95"
            >
              <Lock class="w-4 h-4" />
              Kirish
            </NuxtLink>

            <!-- Logged in -->
            <div v-else class="relative">
              <button
                @click="toggleDropdown"
                class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
              >
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                  <span class="text-white text-sm font-bold">{{ auth.user?.name?.charAt(0).toUpperCase() }}</span>
                </div>
                <div class="text-left">
                  <div class="text-sm font-bold text-slate-800 dark:text-white leading-tight">{{ auth.user?.name }}</div>
                  <div class="text-xs text-slate-500 dark:text-slate-400 leading-tight">{{ auth.user?.email }}</div>
                </div>
                <ChevronDown class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="isDropdownOpen ? 'rotate-180' : ''" />
              </button>

              <Transition
                enter-active-class="transition duration-150 ease-out"
                enter-from-class="opacity-0 scale-95 -translate-y-1"
                enter-to-class="opacity-100 scale-100 translate-y-0"
                leave-active-class="transition duration-100 ease-in"
                leave-from-class="opacity-100 scale-100 translate-y-0"
                leave-to-class="opacity-0 scale-95 -translate-y-1"
              >
                <div
                  v-if="isDropdownOpen"
                  class="absolute right-0 top-full mt-2 w-52 bg-white dark:bg-slate-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-xl overflow-hidden z-50"
                >
                  <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                    <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Hisob</div>
                    <div class="text-sm font-bold text-slate-800 dark:text-white truncate">{{ auth.user?.name }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ auth.user?.email }}</div>
                  </div>

                  <div class="p-1.5">
                    <NuxtLink
                      to="/dashboard"
                      @click="closeDropdown"
                      class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                    >
                      <LayoutDashboard class="w-4 h-4" />
                      Bosh sahifa
                    </NuxtLink>

                    <NuxtLink
                      to="/stats"
                      @click="closeDropdown"
                      class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                    >
                      <ChartColumnDecreasing class="w-4 h-4" />
                      Fan statistikasi
                    </NuxtLink>

                    <NuxtLink
                      to="/profile"
                      @click="closeDropdown"
                      class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                    >
                      <UserCircle class="w-4 h-4" />
                      Profil
                    </NuxtLink>

                    <a
                      v-if="auth.isAdmin"
                      href="https://admin.abutolib.uz"
                      target="_blank"
                      @click="closeDropdown"
                      class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-violet-600 dark:text-violet-400 hover:bg-violet-50 dark:hover:bg-violet-900/20 transition-colors"
                    >
                      <ShieldCheck class="w-4 h-4" />
                      Admin panel
                    </a>
                  </div>

                  <div class="p-1.5 border-t border-gray-100 dark:border-gray-800">
                    <button
                      @click="handleLogout"
                      class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                    >
                      <LogOut class="w-4 h-4" />
                      Chiqish
                    </button>
                  </div>
                </div>
              </Transition>
            </div>
          </div>

          <!-- Mobile burger -->
          <button
            @click="isMenuOpen = !isMenuOpen"
            class="lg:hidden p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
          >
            <Menu v-if="!isMenuOpen" class="w-5 h-5" />
            <X v-else class="w-5 h-5" />
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile menu -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div v-if="isMenuOpen" class="lg:hidden border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-slate-900 shadow-xl">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 py-3 space-y-0.5">
          <template v-for="link in navLinks" :key="link.name">

            <!-- Mobile submenu -->
            <div v-if="link.children">
              <button
                @click="toggleSubmenu(link.name)"
                :class="[
                  'w-full flex items-center justify-between gap-2 px-4 py-3 text-sm font-semibold rounded-xl transition-colors',
                  isSubmenuActive(link.children)
                    ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'
                    : 'text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-blue-900/20'
                ]"
              >
                <div class="flex items-center gap-2">
                  <component :is="link.icon" class="w-4 h-4 flex-shrink-0" />
                  {{ link.name }}
                </div>
                <ChevronDown class="w-4 h-4 transition-transform duration-200" :class="openSubmenu === link.name ? 'rotate-180' : ''" />
              </button>

              <Transition
                enter-active-class="transition duration-150 ease-out"
                enter-from-class="opacity-0 -translate-y-1"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-100 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-1"
              >
                <div v-if="openSubmenu === link.name" class="ml-4 mt-0.5 mb-1 pl-3 border-l-2 border-blue-100 dark:border-blue-900/50 space-y-0.5">
                  <NuxtLink
                    v-for="child in link.children"
                    :key="child.href"
                    :to="child.href"
                    @click="closeAll"
                    class="flex items-center gap-2 px-3 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                    active-class="bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400"
                  >
                    <component :is="child.icon" class="w-4 h-4 flex-shrink-0" />
                    {{ child.name }}
                  </NuxtLink>
                </div>
              </Transition>
            </div>

            <!-- Mobile normal link -->
            <NuxtLink
              v-else
              :to="link.href"
              @click="closeAll"
              class="flex items-center gap-2 px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-200 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
              active-class="bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400"
            >
              <component :is="link.icon" class="w-4 h-4 flex-shrink-0" />
              {{ link.name }}
            </NuxtLink>

          </template>
        </nav>

        <!-- Mobile auth section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 pb-4 pt-1 border-t border-gray-100 dark:border-gray-800">

          <NuxtLink
            v-if="!auth.isLoggedIn"
            to="/login"
            @click="closeAll"
            class="flex items-center justify-center gap-2 w-full py-3 bg-[#121826] dark:bg-blue-600 text-white rounded-xl font-bold text-sm transition-all active:scale-95"
          >
            <Lock class="w-4 h-4" />
            Kirish
          </NuxtLink>

          <div v-else class="space-y-2">
            <div class="flex items-center gap-3 px-3 py-3 bg-gray-50 dark:bg-slate-800 rounded-xl">
              <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold text-sm">{{ auth.user?.name?.charAt(0).toUpperCase() }}</span>
              </div>
              <div class="min-w-0">
                <div class="text-sm font-bold text-slate-800 dark:text-white truncate">{{ auth.user?.name }}</div>
                <div class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ auth.user?.email }}</div>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
              <NuxtLink
                to="/dashboard"
                @click="closeAll"
                class="flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 bg-gray-100 dark:bg-slate-800 hover:bg-blue-50 hover:text-blue-600 transition-colors"
              >
                <LayoutDashboard class="w-4 h-4" />
                Dashboard
              </NuxtLink>

              <NuxtLink
                to="/profile"
                @click="closeAll"
                class="flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 bg-gray-100 dark:bg-slate-800 hover:bg-blue-50 hover:text-blue-600 transition-colors"
              >
                <UserCircle class="w-4 h-4" />
                Profil
              </NuxtLink>

              <a
                v-if="auth.isAdmin"
                href="https://admin.abutolib.uz"
                target="_blank"
                @click="closeAll"
                class="col-span-2 flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-semibold text-violet-600 dark:text-violet-400 bg-violet-50 dark:bg-violet-900/20 hover:bg-violet-100 dark:hover:bg-violet-900/30 transition-colors"
              >
                <ShieldCheck class="w-4 h-4" />
                Admin panel
              </a>
            </div>

            <button
              @click="handleLogout"
              class="w-full flex items-center justify-center gap-2 py-3 rounded-xl text-sm font-semibold text-red-500 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 transition-colors"
            >
              <LogOut class="w-4 h-4" />
              Chiqish
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </header>

  <!-- Bottom nav -->
  <div class="lg:hidden fixed bottom-0 left-0 right-0 z-50">
    <div class="mx-3 mb-3">
      <div class="bg-white/95 dark:bg-[#161b22]/95 backdrop-blur-xl border border-gray-200/80 dark:border-white/10 rounded-2xl shadow-2xl shadow-black/20 dark:shadow-black/60 px-2 py-2">
        <div class="flex items-center justify-around">

          <NuxtLink
            v-for="link in bottomNavLinks"
            :key="link.href"
            :to="link.href"
            class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-xl transition-all duration-200 min-w-[56px]"
            :class="
              route.path === link.href || (link.href === '/contests' && competitionRoutes.includes(route.path))
                ? 'text-blue-600 dark:text-blue-400'
                : 'text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300'
            "
          >
            <div
              class="relative flex items-center justify-center w-10 h-8 rounded-xl transition-all duration-200"
              :class="route.path === link.href || (link.href === '/contests' && competitionRoutes.includes(route.path)) ? 'bg-blue-50 dark:bg-blue-500/15' : ''"
            >
              <component
                :is="link.icon"
                class="w-5 h-5 transition-transform duration-200"
                :class="route.path === link.href || (link.href === '/contests' && competitionRoutes.includes(route.path)) ? 'scale-110' : ''"
              />
              <span
                v-if="route.path === link.href || (link.href === '/contests' && competitionRoutes.includes(route.path))"
                class="absolute -bottom-0.5 left-1/2 -translate-x-1/2 w-1 h-1 bg-blue-600 dark:bg-blue-400 rounded-full"
              />
            </div>
            <span class="text-[10px] font-semibold leading-none">{{ link.name }}</span>
          </NuxtLink>

          <!-- Bottom nav auth button -->
          <div class="flex flex-col items-center gap-1 min-w-[56px]">
            <NuxtLink
              v-if="!auth.isLoggedIn"
              to="/login"
              class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-xl text-gray-400 dark:text-gray-500 hover:text-blue-600 transition-all duration-200"
            >
              <div class="flex items-center justify-center w-10 h-8 rounded-xl">
                <Lock class="w-5 h-5" />
              </div>
              <span class="text-[10px] font-semibold leading-none">Kirish</span>
            </NuxtLink>

            <a
              v-else-if="auth.isAdmin"
              href="https://admin.abutolib.uz"
              target="_blank"
              class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-xl text-violet-500 dark:text-violet-400 transition-all duration-200"
            >
              <div class="flex items-center justify-center w-10 h-8 rounded-xl bg-violet-50 dark:bg-violet-500/15">
                <ShieldCheck class="w-5 h-5" />
              </div>
              <span class="text-[10px] font-semibold leading-none">Admin</span>
            </a>

            <NuxtLink
              v-else
              to="/profile"
              class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-xl transition-all duration-200"
              :class="route.path === '/profile' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500'"
            >
              <div
                class="flex items-center justify-center w-10 h-8 rounded-xl transition-all duration-200"
                :class="route.path === '/profile' ? 'bg-blue-50 dark:bg-blue-500/15' : ''"
              >
                <div class="w-6 h-6 bg-blue-600 rounded-lg flex items-center justify-center">
                  <span class="text-white text-[10px] font-black">{{ auth.user?.name?.charAt(0).toUpperCase() }}</span>
                </div>
              </div>
              <span class="text-[10px] font-semibold leading-none">Profil</span>
            </NuxtLink>
          </div>

        </div>
      </div>
    </div>
    <div class="h-safe-area-inset-bottom bg-transparent" />
  </div>
</template>