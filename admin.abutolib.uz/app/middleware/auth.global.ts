import { useAuthStore } from "../stores/auth"

export default defineNuxtRouteMiddleware(async (to) => {
  const auth = useAuthStore()
  auth.init()

  if (to.path === '/login') {
    if (auth.tokenValue) {
      await auth.fetchUser()
      if (auth.isAuthenticated && auth.isAdmin) {
        return navigateTo('/subjects')
      }
    }
    return
  }

  if (!auth.tokenValue) {
    return navigateTo('/login')
  }

  if (!auth.user) {
    await auth.fetchUser()
  }

  if (!auth.isAdmin) {
    return navigateTo('/login')
  }
})