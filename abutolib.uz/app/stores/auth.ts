import { defineStore } from 'pinia'

interface SessionLimitData {
  email: string
  password: string
  sessions: any[]
}

interface AuthState {
  user: User | null
  token: string | null
  loading: boolean
  error: string | null
  sessionLimitData: SessionLimitData | null
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    user: null,
    token: null,
    loading: false,
    error: null,
    sessionLimitData: null,
  }),

  getters: {
    isLoggedIn: (state) => !!state.token && !!state.user,
    isAdmin: (state) => (state.user as any)?.roles?.some((r: any) => r.name === 'admin') ?? false,
  },

  actions: {
    initToken() {
      const cookie = useCookie('auth_token')
      if (cookie.value) {
        this.token = cookie.value
      }
    },

    async register(name: string, email: string, password: string, password_confirmation: string) {
      const { $api } = useApi()
      this.loading = true
      this.error = null
      try {
        const res: any = await $api('/auth/register', {
          method: 'POST',
          body: { name, email, password, password_confirmation },
        })
        this._setSession(res.user, res.token)
        await navigateTo('/dashboard')
      } catch (err: any) {
        this.error = err?.data?.message || "Ro'yxatdan o'tishda xatolik"
        throw err
      } finally {
        this.loading = false
      }
    },

    async login(email: string, password: string) {
      const { $api } = useApi()
      this.loading = true
      this.error = null
      try {
        const res: any = await $api('/auth/login', {
          method: 'POST',
          body: { email, password },
        })
        this._setSession(res.user, res.token)
        await navigateTo('/dashboard')
      } catch (err: any) {
        const errorData = err.data
        if (errorData?.error === 'session_limit') {
          this.sessionLimitData = {
            email,
            password,
            sessions: errorData.sessions,
          }
          return await navigateTo('/session-limit')
        }
        this.error = errorData?.message || "Email yoki parol noto'g'ri"
        throw err
      } finally {
        this.loading = false
      }
    },

    async revokeAndLogin(sessionId: number) {
      const { $api } = useApi()
      this.loading = true
      this.error = null
      try {
        const res: any = await $api('/auth/revoke-and-login', {
          method: 'POST',
          body: {
            email: this.sessionLimitData?.email,
            password: this.sessionLimitData?.password,
            session_id: sessionId,
          },
        })
        this.sessionLimitData = null
        this._setSession(res.user, res.token)
      } catch (err: any) {
        this.error = err?.data?.message || 'Xatolik yuz berdi'
        throw err
      } finally {
        this.loading = false
      }
    },

    async logout() {
      const { $api } = useApi()
      try {
        await $api('/auth/logout', { method: 'POST' })
      } finally {
        this._clearSession()
        await navigateTo('/login')
      }
    },

    async fetchUser() {
      if (!this.token) return
      const { $api } = useApi()
      try {
        const user: any = await $api('/auth/me')
        this.user = user
      } catch {
        this._clearSession()
      }
    },

    _setSession(user: User, token: string) {
      this.user = user
      this.token = token
      const isProd = process.env.NODE_ENV === 'production'
      const cookie = useCookie('auth_token', {
        maxAge: 60 * 60 * 24 * 7,
        ...(isProd ? { domain: '.abutolib.uz' } : {}),
        path: '/',
        sameSite: 'lax',
        secure: isProd,
      })
      cookie.value = token
    },

    _clearSession() {
      this.user = null
      this.token = null
      const isProd = process.env.NODE_ENV === 'production'
      const cookie = useCookie('auth_token', {
        ...(isProd ? { domain: '.abutolib.uz' } : {}),
        path: '/',
      })
      cookie.value = null
    },
  },
})