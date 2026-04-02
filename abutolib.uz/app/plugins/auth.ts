export default defineNuxtPlugin(async (nuxtApp) => {
  const auth = useAuthStore(nuxtApp.$pinia)
  auth.initToken()
  if (auth.token && !auth.user) {
    await auth.fetchUser()
  }
})