import { defineStore } from 'pinia'

interface Plan {
  id: number
  name: string
  slug: string
  price_usd: number
  price_uzs: number
  included_models: string[]
  monthly_token_limit: number
  max_pages_per_ocr: number
}

interface Subscription {
  id: number
  plan_id: number
  starts_at: string
  expires_at: string
  remaining_tokens: number
  status: 'active' | 'expired' | 'cancelled'
  plan: Plan
}

interface SubscriptionState {
  plans: Plan[]
  loading: boolean
  activeSubscription: Subscription | null
  exchangeRate: number
}

export const useSubscriptionStore = defineStore('subscription', {
  state: (): SubscriptionState => ({
    plans: [],
    loading: false,
    activeSubscription: null,
    exchangeRate: 12800,
  }),

  actions: {
    async fetchPlans() {
      const { $api } = useApi()
      this.loading = true
      try {
        const response = await $api<any>('/plans')
        this.plans = response.plans
        this.exchangeRate = response.exchange_rate
      } catch (error) {
        console.error(error)
      } finally {
        this.loading = false
      }
    },

    async fetchCurrentSubscription() {
      const { $api } = useApi()
      try {
        const response = await $api<{ data: Subscription }>('/my-subscription')
        this.activeSubscription = response.data
      } catch (error) {
        this.activeSubscription = null
      }
    },

    async checkout(planId: number, method: 'click' | 'payme' | 'uzum') {
      const { $api } = useApi()
      try {
        const response = await $api<{ checkout_url: string }>('/subscribe', {
          method: 'POST',
          body: {
            plan_id: planId,
            payment_method: method,
          },
        })

        if (response.checkout_url) {
          window.location.href = response.checkout_url
        }
      } catch (error) {
        console.error(error)
      }
    },
  },
})