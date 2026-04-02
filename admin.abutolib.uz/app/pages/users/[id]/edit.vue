<template>
  <NuxtLayout name="admin">
    <div v-if="pending" class="flex items-center justify-center h-64">
      <UIcon name="i-heroicons-arrow-path" class="w-6 h-6 text-gray-400 animate-spin" />
    </div>

    <div v-else-if="user" class="max-w-xl space-y-6">
      <div class="flex items-center gap-3">
        <NuxtLink to="/users">
          <UButton icon="i-heroicons-arrow-left" color="neutral" variant="ghost" size="sm" />
        </NuxtLink>
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center shrink-0">
            <span class="text-sm font-semibold text-primary-600 dark:text-primary-400">{{ user.name.charAt(0).toUpperCase() }}</span>
          </div>
          <div>
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ user.name }}</h2>
            <p class="text-xs text-gray-400">{{ user.email }}</p>
          </div>
        </div>
      </div>

      <UCard>
        <template #header>
          <p class="text-sm font-semibold text-gray-900 dark:text-white">Account Info</p>
        </template>
        <div class="space-y-4">
          <UFormField label="Full Name" required>
            <UInput v-model="form.name" placeholder="John Doe" class="w-full" />
          </UFormField>

          <UFormField label="Email" required>
            <UInput v-model="form.email" type="email" leading-icon="i-heroicons-envelope" class="w-full" />
          </UFormField>

          <UFormField label="Phone">
            <UInput v-model="form.phone" placeholder="+998901234567" leading-icon="i-heroicons-phone" class="w-full" />
          </UFormField>

          <div class="grid grid-cols-2 gap-4">
            <UFormField label="New Password">
              <UInput
                v-model="form.password"
                :type="showPwd ? 'text' : 'password'"
                placeholder="Leave blank to keep"
                leading-icon="i-heroicons-lock-closed"
                class="w-full"
                :ui="{ icon: { trailing: { pointer: '' } } }"
              >
                <template #trailing>
                  <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" @click="showPwd = !showPwd">
                    <UIcon :name="showPwd ? 'i-heroicons-eye-slash' : 'i-heroicons-eye'" class="w-4 h-4" />
                  </button>
                </template>
              </UInput>
            </UFormField>

            <UFormField label="Role">
              <div class="relative" ref="roleWrapRef">
                <button
                  type="button"
                  class="w-full flex items-center justify-between gap-2 px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 hover:border-gray-300 transition-colors"
                  :class="roleOpen ? 'border-primary-500 ring-2 ring-primary-500/20' : ''"
                  @click="roleOpen = !roleOpen"
                >
                  <span :class="form.role ? 'capitalize text-gray-800 dark:text-gray-200' : 'text-gray-400'">
                    {{ form.role || 'No role' }}
                  </span>
                  <UIcon name="i-heroicons-chevron-up-down" class="w-4 h-4 text-gray-400 shrink-0" />
                </button>
                <Transition name="dropdown">
                  <div v-if="roleOpen" class="absolute z-50 left-0 mt-1.5 w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden py-1">
                    <button
                      v-for="r in roles"
                      :key="r"
                      type="button"
                      class="w-full flex items-center gap-2 px-3 py-2.5 text-sm capitalize hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                      :class="form.role === r ? 'text-primary-600 bg-primary-50 dark:bg-primary-900/20' : 'text-gray-700 dark:text-gray-300'"
                      @click="form.role = r; roleOpen = false"
                    >
                      <span class="w-2 h-2 rounded-full shrink-0" :class="roleDot(r)" />
                      {{ r }}
                      <UIcon v-if="form.role === r" name="i-heroicons-check" class="w-4 h-4 text-primary-500 ml-auto" />
                    </button>
                  </div>
                </Transition>
              </div>
            </UFormField>
          </div>

          <div class="flex items-center justify-between pt-1 border-t border-gray-100 dark:border-gray-800">
            <div>
              <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</p>
              <p class="text-xs text-gray-400">User can log in</p>
            </div>
            <UToggle v-model="form.is_active" />
          </div>
        </div>
      </UCard>

      <UCard>
        <template #header>
          <p class="text-sm font-semibold text-gray-900 dark:text-white">Details</p>
        </template>
        <div class="grid grid-cols-2 gap-3 text-sm">
          <div class="space-y-0.5">
            <p class="text-xs text-gray-400">Telegram ID</p>
            <p class="text-gray-700 dark:text-gray-300">{{ user.telegram_id ?? '—' }}</p>
          </div>
          <div class="space-y-0.5">
            <p class="text-xs text-gray-400">Telegram Username</p>
            <p class="text-gray-700 dark:text-gray-300">{{ user.telegram_username ? '@' + user.telegram_username : '—' }}</p>
          </div>
          <div class="space-y-0.5">
            <p class="text-xs text-gray-400">Email Verified</p>
            <p :class="user.email_verified_at ? 'text-green-600 dark:text-green-400' : 'text-gray-400'">
              {{ user.email_verified_at ? formatDate(user.email_verified_at) : 'Not verified' }}
            </p>
          </div>
          <div class="space-y-0.5">
            <p class="text-xs text-gray-400">Registered</p>
            <p class="text-gray-700 dark:text-gray-300">{{ formatDate(user.created_at) }}</p>
          </div>
        </div>
      </UCard>

      <div class="flex gap-3">
        <NuxtLink to="/users" class="flex-1">
          <UButton label="Cancel" color="neutral" variant="soft" block />
        </NuxtLink>
        <UButton label="Save Changes" color="primary" icon="i-heroicons-check" :loading="saving" class="flex-1" @click="save" />
      </div>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
const route = useRoute()
const router = useRouter()
const api = useApi()
const toast = useToast()

const saving = ref(false)
const showPwd = ref(false)
const roleOpen = ref(false)
const roleWrapRef = ref<HTMLElement | null>(null)

const roles = ['admin', 'teacher', 'student']

const form = reactive({
  name: '',
  email: '',
  phone: '',
  password: '',
  role: '',
  is_active: true,
})

const { data: user, pending } = await useAsyncData(
  `user-${route.params.id}`,
  () => api.get<any>(`/admin/users/${route.params.id}`).then((r: any) => r.data)
)

watch(user, (u) => {
  if (!u) return
  form.name = u.name
  form.email = u.email
  form.phone = u.phone ?? ''
  form.role = u.roles?.[0] ?? ''
  form.is_active = u.is_active
}, { immediate: true })

function roleDot(role: string) {
  const map: Record<string, string> = {
    admin: 'bg-purple-400', teacher: 'bg-blue-400', student: 'bg-amber-400',
  }
  return map[role] ?? 'bg-gray-400'
}

function formatDate(dt: string) {
  return new Date(dt).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
}

async function save() {
  if (!form.name.trim() || !form.email.trim()) {
    toast.add({ title: 'Name and email are required', color: 'error', icon: 'i-heroicons-x-circle' })
    return
  }
  saving.value = true
  try {
    const payload: Record<string, any> = {
      name: form.name,
      email: form.email,
      phone: form.phone,
      role: form.role,
      is_active: form.is_active ?? false,
    }
    if (form.password) payload.password = form.password

    await api.put(`/admin/users/${route.params.id}`, payload)
    toast.add({ title: 'User updated', color: 'success', icon: 'i-heroicons-check-circle' })
    router.push('/users')
  } catch (e: any) {
    toast.add({ title: e?.data?.message ?? 'Failed to update user', color: 'error', icon: 'i-heroicons-x-circle' })
  } finally {
    saving.value = false
  }
}

onMounted(() => document.addEventListener('click', onClickOutside))
onBeforeUnmount(() => document.removeEventListener('click', onClickOutside))
function onClickOutside(e: MouseEvent) {
  if (roleWrapRef.value && !roleWrapRef.value.contains(e.target as Node)) roleOpen.value = false
}
</script>

<style scoped>
.dropdown-enter-active, .dropdown-leave-active { transition: all 0.15s ease; }
.dropdown-enter-from, .dropdown-leave-to { opacity: 0; transform: translateY(-4px); }
</style>