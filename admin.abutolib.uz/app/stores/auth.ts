import { defineStore } from 'pinia'

interface Role {
  id: number
  name: string
}

interface User {
  id: number
  name: string
  email: string
  roles: Role[]
}

export const useAuthStore = defineStore('auth', () => {
  const { public: { apiBase } } = useRuntimeConfig()
  const runtimeConfig = useRuntimeConfig()

  const user = ref<User | null>(null)
  const tokenValue = ref<string | null>(null)

  const isProd = process.env.NODE_ENV === 'production'

  function init() {
    const cookie = useCookie('auth_token')
    tokenValue.value = cookie.value ?? null
  }

  function setToken(val: string | null) {
    tokenValue.value = val
    const cookie = useCookie('auth_token', {
      maxAge: 60 * 60 * 24 * 7,
      ...(isProd ? { domain: '.abutolib.uz' } : {}),
      path: '/',
      sameSite: 'lax',
      secure: isProd,
    })
    cookie.value = val
  }

  const isAuthenticated = computed(() => !!tokenValue.value)
  const isAdmin = computed(() => user.value?.roles?.some(r => r.name === 'admin') ?? false)

  async function login(email: string, password: string) {
    const res = await $fetch<{ token: string; user: User }>(
      `${apiBase}/auth/login`,
      {
        method: 'POST',
        headers: { Accept: 'application/json' },
        body: { email, password },
      }
    )
    setToken(res.token)
    user.value = res.user
  }

  async function fetchUser() {
    if (!tokenValue.value) return
    try {
      const res = await $fetch<User>(`${apiBase}/auth/me`, {
        headers: {
          Authorization: `Bearer ${tokenValue.value}`,
          Accept: 'application/json',
        },
      })
      user.value = res
    } catch {
      logout()
    }
  }

  function logout() {
    setToken(null)
    user.value = null
    navigateTo('/login')
  }

  return { user, tokenValue, isAuthenticated, isAdmin, init, login, fetchUser, logout }
})