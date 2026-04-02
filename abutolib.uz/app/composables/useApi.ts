export const useApi = () => {
  const config = useRuntimeConfig()
  const token  = useCookie('auth_token')

  const $api = $fetch.create({
    baseURL: config.public.apiBase,
    headers: {
      Accept: 'application/json',
      ...(token.value ? { Authorization: `Bearer ${token.value}` } : {}),
    },
    onResponseError({ response }) {
      if (response.status === 401) {
        useCookie('auth_token').value = null
        navigateTo('/login')
      }
    },
  })

  return { $api }
}