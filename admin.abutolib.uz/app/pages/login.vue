<template>
  <div class="min-h-screen flex">
    <div class="hidden lg:flex lg:w-[45%] flex-col justify-between p-12 relative overflow-hidden bg-primary-600 dark:bg-primary-700">
      <div class="absolute inset-0 bg-gradient-to-br from-primary-500 to-primary-700 dark:from-primary-600 dark:to-primary-900" />
      <div class="absolute -top-20 -right-20 w-72 h-72 rounded-full bg-white/[0.06]" />
      <div class="absolute -bottom-10 -left-16 w-52 h-52 rounded-full bg-white/[0.06]" />
      <div class="absolute top-1/3 right-8 w-32 h-32 rounded-full bg-white/[0.04]" />

      <div class="relative flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
          <UIcon name="i-heroicons-academic-cap" class="w-5 h-5 text-white" />
        </div>
        <span class="text-white font-medium tracking-tight">Admin Panel</span>
      </div>

      <div class="relative space-y-5">
        <div class="w-8 h-0.5 bg-white/40 rounded-full" />
        <h2 class="text-[2rem] font-semibold text-white leading-snug">
          Manage your<br />platform<br />with ease.
        </h2>
        <p class="text-white/60 text-sm leading-relaxed max-w-xs">
          Full control over subjects, topics, olympiads and users from one central dashboard.
        </p>
      </div>

      <p class="relative text-white/40 text-xs">
        © {{ new Date().getFullYear() }} Admin Panel. All rights reserved.
      </p>
    </div>

    <div class="flex-1 flex items-center justify-center bg-white dark:bg-gray-950 px-6">
      <div class="w-full max-w-[360px]">
        <div class="lg:hidden flex items-center gap-3 mb-10">
          <div class="w-9 h-9 rounded-xl bg-primary-500 flex items-center justify-center">
            <UIcon name="i-heroicons-academic-cap" class="w-5 h-5 text-white" />
          </div>
          <span class="font-medium text-gray-900 dark:text-white">Admin Panel</span>
        </div>

        <div v-if="checkingToken" class="flex flex-col items-center gap-4 py-12">
          <UIcon name="i-heroicons-arrow-path" class="w-8 h-8 text-primary-500 animate-spin" />
          <p class="text-sm text-gray-400">Tekshirilmoqda...</p>
        </div>

        <template v-else>
          <div class="mb-8">
            <h1 class="text-[1.6rem] font-semibold text-gray-900 dark:text-white mb-2 tracking-tight">
              Welcome back
            </h1>
            <p class="text-sm text-gray-400 dark:text-gray-500">
              Sign in to your admin account to continue.
            </p>
          </div>

          <UForm :schema="schema" :state="state" class="space-y-4" @submit="onSubmit">
            <UFormField name="email">
              <template #label>
                <span class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
                  Email address
                </span>
              </template>
              <UInput
                v-model="state.email"
                type="email"
                placeholder="admin@example.com"
                leading-icon="i-heroicons-envelope"
                size="md"
                class="w-full"
              />
            </UFormField>

            <UFormField name="password">
              <template #label>
                <span class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
                  Password
                </span>
              </template>
              <UInput
                v-model="state.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Enter your password"
                leading-icon="i-heroicons-lock-closed"
                size="md"
                class="w-full"
              >
                <template #trailing>
                  <button
                    type="button"
                    class="flex items-center justify-center w-6 h-6 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                    @click="showPassword = !showPassword"
                  >
                    <UIcon
                      :name="showPassword ? 'i-heroicons-eye-slash' : 'i-heroicons-eye'"
                      class="w-4 h-4"
                    />
                  </button>
                </template>
              </UInput>
            </UFormField>

            <Transition name="slide-fade">
              <div
                v-if="error"
                class="flex items-start gap-2.5 px-3.5 py-3 rounded-lg bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/40"
              >
                <UIcon name="i-heroicons-exclamation-circle" class="w-4 h-4 text-red-500 shrink-0 mt-px" />
                <p class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
              </div>
            </Transition>

            <div class="pt-1">
              <UButton
                type="submit"
                block
                size="md"
                color="primary"
                :loading="loading"
                class="justify-center font-medium"
              >
                <template #leading>
                  <UIcon v-if="!loading" name="i-heroicons-arrow-right-on-rectangle" class="w-4 h-4" />
                </template>
                Sign In
              </UButton>
            </div>
          </UForm>

          <div class="mt-6 text-center">
            <a href="https://abutolib.uz" class="text-xs text-gray-400 hover:text-primary-500 transition-colors">
              ← abutolib.uz ga qaytish
            </a>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { z } from 'zod'
import { useAuthStore } from '../stores/auth'

useSeoMeta({ title: 'Admin — Kirish' })
definePageMeta({ layout: false })

const auth = useAuthStore()
const error = ref('')
const loading = ref(false)
const showPassword = ref(false)
const checkingToken = ref(false)

const schema = z.object({
  email: z.string().email('Email noto\'g\'ri kiritilgan'),
  password: z.string().min(6, 'Parol kamida 6 ta belgi bo\'lishi kerak'),
})

const state = reactive({ email: '', password: '' })

async function onSubmit() {
  error.value = ''
  loading.value = true
  try {
    await auth.login(state.email, state.password)

    if (!auth.isAdmin) {
      auth.logout()
      error.value = 'Sizda admin huquqi yo\'q'
      return
    }

    await navigateTo('/subjects')
  } catch (e: unknown) {
    error.value =
      (e as { data?: { message?: string } })?.data?.message ??
      'Email yoki parol noto\'g\'ri'
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  if (auth.tokenValue) {
    checkingToken.value = true
    await auth.fetchUser()
    checkingToken.value = false

    if (auth.isAuthenticated && auth.isAdmin) {
      await navigateTo('/subjects')
    }
  }
})
</script>

<style scoped>
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.2s ease;
}
.slide-fade-enter-from,
.slide-fade-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
</style>