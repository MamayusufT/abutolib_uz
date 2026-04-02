import { useAuthStore } from "../stores/auth"

export function useApi() {
  const { public: { apiBase } } = useRuntimeConfig()
  const auth = useAuthStore()

  const headers = computed(() => ({
    Authorization: `Bearer ${auth.tokenValue}`,
    Accept: 'application/json',
  }))

  async function get<T>(path: string, params?: Record<string, unknown>): Promise<T> {
    return $fetch<T>(`${apiBase}${path}`, {
      headers: headers.value,
      params,
    })
  }

  async function post<T>(path: string, body?: unknown): Promise<T> {
    return $fetch<T>(`${apiBase}${path}`, {
      method: 'POST',
      headers: headers.value,
      body,
    })
  }

  async function put<T>(path: string, body?: unknown): Promise<T> {
    return $fetch<T>(`${apiBase}${path}`, {
      method: 'PUT',
      headers: headers.value,
      body,
    })
  }

  async function patch<T>(path: string, body?: unknown): Promise<T> {
    return $fetch<T>(`${apiBase}${path}`, {
      method: 'PATCH',
      headers: headers.value,
      body,
    })
  }

  async function del<T>(path: string): Promise<T> {
    return $fetch<T>(`${apiBase}${path}`, {
      method: 'DELETE',
      headers: headers.value,
    })
  }

  return { get, post, put, patch, del }
}