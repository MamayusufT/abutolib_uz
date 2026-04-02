import { useAuthStore } from "~/stores/auth"

export default defineNuxtRouteMiddleware(async () => {
  const auth = useAuthStore()

  if (!auth.token) {
    return navigateTo('/login')
  }
  if (!auth.user) {
    await auth.fetchUser()
    if (!auth.user) return navigateTo('/login')
  }
})