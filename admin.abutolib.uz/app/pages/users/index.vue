<template>
  <NuxtLayout name="admin">
    <div class="space-y-5">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2 flex-1 flex-wrap">
          <UInput
            v-model="search"
            placeholder="Search by name, email, phone..."
            leading-icon="i-heroicons-magnifying-glass"
            size="sm"
            class="w-full max-w-xs"
            @input="onSearch"
          />

          <div class="relative" ref="roleWrapRef">
            <button
              type="button"
              class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 hover:border-gray-300 transition-colors min-w-[130px]"
              :class="roleOpen ? 'border-primary-500 ring-2 ring-primary-500/20' : ''"
              @click="roleOpen = !roleOpen"
            >
              <span v-if="filterRole" class="flex-1 capitalize text-gray-800 dark:text-gray-200">{{ filterRole }}</span>
              <span v-else class="flex-1 text-gray-400 text-left">All roles</span>
              <UIcon name="i-heroicons-chevron-up-down" class="w-4 h-4 text-gray-400 shrink-0" />
            </button>
            <Transition name="dropdown">
              <div
                v-if="roleOpen"
                class="absolute z-50 left-0 mt-1.5 w-44 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden py-1"
              >
                <button
                  v-for="opt in roleOptions"
                  :key="opt.value ?? 'all'"
                  type="button"
                  class="w-full flex items-center gap-2.5 px-3 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                  :class="filterRole === opt.value ? 'text-primary-600 bg-primary-50 dark:bg-primary-900/20' : 'text-gray-700 dark:text-gray-300'"
                  @click="selectRole(opt.value)"
                >
                  {{ opt.label }}
                  <UIcon v-if="filterRole === opt.value" name="i-heroicons-check" class="w-4 h-4 text-primary-500 ml-auto" />
                </button>
              </div>
            </Transition>
          </div>
        </div>

        <NuxtLink to="/users/create">
          <UButton icon="i-heroicons-plus" size="sm" color="primary">Add User</UButton>
        </NuxtLink>
      </div>

      <UCard>
        <div v-if="pending" class="flex items-center justify-center py-16">
          <UIcon name="i-heroicons-arrow-path" class="w-6 h-6 text-gray-400 animate-spin" />
        </div>

        <div v-else-if="!users.length" class="flex flex-col items-center justify-center py-16 gap-3 text-gray-400">
          <UIcon name="i-heroicons-users" class="w-10 h-10 opacity-40" />
          <p class="text-sm">No users found</p>
        </div>

        <div v-else class="divide-y divide-gray-100 dark:divide-gray-800 -mx-4 sm:-mx-6">
          <div class="grid grid-cols-12 gap-3 px-4 sm:px-6 py-2.5 bg-gray-50 dark:bg-gray-800/50">
            <span class="col-span-4 text-xs font-semibold uppercase tracking-widest text-gray-400">User</span>
            <span class="col-span-2 text-xs font-semibold uppercase tracking-widest text-gray-400">Phone / TG</span>
            <span class="col-span-2 text-xs font-semibold uppercase tracking-widest text-gray-400">Role</span>
            <span class="col-span-2 text-xs font-semibold uppercase tracking-widest text-gray-400">Status</span>
            <span class="col-span-2 text-xs font-semibold uppercase tracking-widest text-gray-400">Joined</span>
          </div>

          <div
            v-for="row in users"
            :key="row.id"
            class="grid grid-cols-12 gap-3 items-center px-4 sm:px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors"
          >
            <div class="col-span-4 flex items-center gap-3 min-w-0">
              <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center shrink-0">
                <span class="text-sm font-semibold text-primary-600 dark:text-primary-400 select-none">
                  {{ row.name.charAt(0).toUpperCase() }}
                </span>
              </div>
              <div class="min-w-0">
                <p class="font-medium text-sm text-gray-900 dark:text-white truncate">{{ row.name }}</p>
                <div class="flex items-center gap-1">
                  <p class="text-xs text-gray-400 truncate">{{ row.email }}</p>
                  <UIcon
                    v-if="row.email_verified_at"
                    name="i-heroicons-check-badge"
                    class="w-3 h-3 text-primary-500 shrink-0"
                  />
                </div>
              </div>
            </div>

            <div class="col-span-2 min-w-0">
              <span v-if="row.phone" class="text-sm text-gray-600 dark:text-gray-400 truncate block">{{ row.phone }}</span>
              <span v-else-if="row.telegram_username" class="flex items-center gap-1 text-xs text-sky-500">
                <UIcon name="i-heroicons-at-symbol" class="w-3 h-3 shrink-0" />
                <span class="truncate">{{ row.telegram_username }}</span>
              </span>
              <span v-else class="text-xs text-gray-300 dark:text-gray-600">—</span>
            </div>

            <div class="col-span-2">
              <div class="flex flex-wrap gap-1">
                <span
                  v-for="role in row.roles"
                  :key="role"
                  class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium capitalize"
                  :class="roleColor(role)"
                >
                  {{ role.name }}
                </span>
                <span v-if="!row.roles?.length" class="text-xs text-gray-300 dark:text-gray-600">—</span>
              </div>
            </div>

            <div class="col-span-2">
              <button
                type="button"
                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium transition-all"
                :class="row.is_active
                  ? 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400 hover:bg-green-100'
                  : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700'"
                @click="toggleActive(row)"
              >
                <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="row.is_active ? 'bg-green-500' : 'bg-gray-400'" />
                {{ row.is_active ? 'Active' : 'Inactive' }}
              </button>
            </div>

            <div class="col-span-2 flex items-center justify-between gap-2">
              <span class="text-xs text-gray-400 shrink-0">{{ formatDate(row.created_at) }}</span>
              <div class="flex items-center gap-1 shrink-0">
                <NuxtLink :to="`/users/${row.id}/edit`">
                  <UButton icon="i-heroicons-pencil-square" color="neutral" variant="ghost" size="xs" />
                </NuxtLink>
                <UButton icon="i-heroicons-trash" color="error" variant="ghost" size="xs" @click="confirmDelete(row)" />
              </div>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between pt-4 mt-4 border-t border-gray-100 dark:border-gray-800">
          <p class="text-xs text-gray-400">Total: {{ meta?.total ?? 0 }} users</p>
          <UPagination
            v-if="meta && meta.last_page > 1"
            v-model="page" :page-count="meta.per_page" :total="meta.total" size="xs"
          />
        </div>
      </UCard>
    </div>

    <UModal v-model:open="deleteModal">
      <template #content>
        <UCard>
          <template #header>
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full bg-red-100 dark:bg-red-950/30 flex items-center justify-center shrink-0">
                <UIcon name="i-heroicons-exclamation-triangle" class="w-5 h-5 text-red-500" />
              </div>
              <div>
                <p class="font-semibold text-gray-900 dark:text-white text-sm">Delete User</p>
                <p class="text-xs text-gray-400">This action cannot be undone</p>
              </div>
            </div>
          </template>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Are you sure you want to delete
            <span class="font-medium text-gray-900 dark:text-white">{{ deleteTarget?.name }}</span>?
          </p>
          <template #footer>
            <div class="flex justify-end gap-2">
              <UButton label="Cancel" color="neutral" variant="ghost" size="sm" @click="deleteModal = false" />
              <UButton label="Delete" color="error" size="sm" :loading="deleting" @click="doDelete" />
            </div>
          </template>
        </UCard>
      </template>
    </UModal>
  </NuxtLayout>
</template>

<script setup lang="ts">
const api = useApi()
const toast = useToast()

const search = ref('')
const page = ref(1)
const filterRole = ref<string | null>(null)
const roleOpen = ref(false)
const roleWrapRef = ref<HTMLElement | null>(null)
const deleteModal = ref(false)
const deleteTarget = ref<{ id: number; name: string } | null>(null)
const deleting = ref(false)

const roleOptions = [
  { label: 'All roles', value: null },
  { label: 'Admin',     value: 'admin' },
  { label: 'Teacher',   value: 'teacher' },
  { label: 'Student',   value: 'student' },
]

function roleColor(role: string) {
  const map: Record<string, string> = {
    admin:   'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
    teacher: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
    student: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
  }
  return map[role] ?? 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'
}

function selectRole(val: string | null) {
  filterRole.value = val
  roleOpen.value = false
  page.value = 1
  refresh()
}

function formatDate(dt: string) {
  return new Date(dt).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
}

const { data, pending, refresh } = await useAsyncData(
  'users',
  () => {
    const params = new URLSearchParams({ page: String(page.value), per_page: '15', search: search.value })
    if (filterRole.value) params.set('role', filterRole.value)
    return api.get<any>(`/admin/users?${params}`)
  },
  { watch: [page] }
)

const users = computed(() => data.value?.data ?? [])
const meta = computed(() => data.value?.meta ?? null)

let searchTimer: ReturnType<typeof setTimeout>
function onSearch() {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => { page.value = 1; refresh() }, 400)
}

async function toggleActive(user: { id: number; is_active: boolean }) {
  try {
    await api.patch(`/admin/users/${user.id}/toggle-active`, {})
    refresh()
  } catch {
    toast.add({ title: 'Failed to update status', color: 'error', icon: 'i-heroicons-x-circle' })
  }
}

function confirmDelete(row: { id: number; name: string }) {
  deleteTarget.value = row
  deleteModal.value = true
}

async function doDelete() {
  if (!deleteTarget.value) return
  deleting.value = true
  try {
    await api.del(`/admin/users/${deleteTarget.value.id}`)
    toast.add({ title: 'User deleted', color: 'success', icon: 'i-heroicons-check-circle' })
    deleteModal.value = false
    refresh()
  } catch (e: any) {
    toast.add({ title: e?.data?.message ?? 'Failed to delete', color: 'error', icon: 'i-heroicons-x-circle' })
  } finally {
    deleting.value = false
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