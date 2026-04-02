<template>
  <NuxtLayout name="admin">
    <div class="space-y-5">
      <div class="flex items-center justify-between gap-4">
        <UInput
          v-model="search"
          placeholder="Search by name or role..."
          leading-icon="i-heroicons-magnifying-glass"
          size="sm"
          class="w-full max-w-sm"
          @input="onSearch"
        />
        <NuxtLink to="/team/create">
          <UButton icon="i-heroicons-plus" size="sm" color="primary">Add Member</UButton>
        </NuxtLink>
      </div>

      <div v-if="pending" class="flex items-center justify-center py-20">
        <UIcon name="i-heroicons-arrow-path" class="w-6 h-6 text-gray-400 animate-spin" />
      </div>

      <div v-else-if="!members.length" class="flex flex-col items-center justify-center py-20 gap-3 text-gray-400">
        <UIcon name="i-heroicons-user-group" class="w-10 h-10 opacity-40" />
        <p class="text-sm">No team members found</p>
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <div
          v-for="member in members"
          :key="member.id"
          class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all duration-200"
        >
          <div class="relative h-36 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700">
            <img
              v-if="member.avatar"
              :src="member.avatar"
              :alt="member.name"
              class="w-full h-full object-cover"
            />
            <div v-else class="w-full h-full flex items-center justify-center">
              <span class="text-4xl font-semibold text-gray-300 dark:text-gray-600 select-none">
                {{ member.name.charAt(0).toUpperCase() }}
              </span>
            </div>

            <div class="absolute top-2 right-2">
              <span
                class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium"
                :class="member.is_active
                  ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400'
                  : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400'"
              >
                <span class="w-1.5 h-1.5 rounded-full" :class="member.is_active ? 'bg-green-500' : 'bg-gray-400'" />
                {{ member.is_active ? 'Active' : 'Inactive' }}
              </span>
            </div>
          </div>

          <div class="p-4">
            <p class="font-semibold text-sm text-gray-900 dark:text-white truncate">{{ member.name }}</p>
            <p class="text-xs text-gray-400 mt-0.5 truncate">{{ member.role ?? '—' }}</p>

            <div class="flex items-center gap-2 mt-3">
              <a
                v-if="member.telegram"
                :href="`https://t.me/${member.telegram.replace('@', '')}`"
                target="_blank"
                class="w-7 h-7 rounded-lg bg-sky-50 dark:bg-sky-900/20 flex items-center justify-center text-sky-500 hover:bg-sky-100 transition-colors"
                @click.stop
              >
                <UIcon name="i-simple-icons-telegram" class="w-3.5 h-3.5" />
              </a>
              <a
                v-if="member.instagram"
                :href="`https://instagram.com/${member.instagram.replace('@', '')}`"
                target="_blank"
                class="w-7 h-7 rounded-lg bg-pink-50 dark:bg-pink-900/20 flex items-center justify-center text-pink-500 hover:bg-pink-100 transition-colors"
                @click.stop
              >
                <UIcon name="i-simple-icons-instagram" class="w-3.5 h-3.5" />
              </a>
              <a
                v-if="member.email"
                :href="`mailto:${member.email}`"
                class="w-7 h-7 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-gray-100 transition-colors"
                @click.stop
              >
                <UIcon name="i-heroicons-envelope" class="w-3.5 h-3.5" />
              </a>
            </div>

            <div class="flex items-center gap-1.5 mt-4 pt-3 border-t border-gray-100 dark:border-gray-800">
              <NuxtLink :to="`/team/${member.id}/edit`" class="flex-1">
                <UButton
                  icon="i-heroicons-pencil-square"
                  label="Edit"
                  color="neutral"
                  variant="soft"
                  size="xs"
                  block
                />
              </NuxtLink>
              <UButton
                icon="i-heroicons-trash"
                color="error"
                variant="ghost"
                size="xs"
                @click="confirmDelete(member)"
              />
            </div>
          </div>
        </div>
      </div>

      <div v-if="meta && meta.last_page > 1" class="flex items-center justify-between pt-2">
        <p class="text-xs text-gray-400">Total: {{ meta.total }} members</p>
        <UPagination v-model="page" :page-count="meta.per_page" :total="meta.total" size="xs" />
      </div>
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
                <p class="font-semibold text-gray-900 dark:text-white text-sm">Remove Member</p>
                <p class="text-xs text-gray-400">This action cannot be undone</p>
              </div>
            </div>
          </template>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Are you sure you want to remove
            <span class="font-medium text-gray-900 dark:text-white">{{ deleteTarget?.name }}</span>
            from the team?
          </p>
          <template #footer>
            <div class="flex justify-end gap-2">
              <UButton label="Cancel" color="neutral" variant="ghost" size="sm" @click="deleteModal = false" />
              <UButton label="Remove" color="error" size="sm" :loading="deleting" @click="doDelete" />
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
const deleteModal = ref(false)
const deleteTarget = ref<{ id: number; name: string } | null>(null)
const deleting = ref(false)

const { data, pending, refresh } = await useAsyncData(
  'team',
  () => {
    const params = new URLSearchParams({ page: String(page.value), per_page: '20', search: search.value })
    return api.get<any>(`/admin/team-members?${params}`)
  },
  { watch: [page] }
)

const members = computed(() => data.value?.data ?? [])
const meta = computed(() => data.value?.meta ?? null)

let searchTimer: ReturnType<typeof setTimeout>
function onSearch() {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => { page.value = 1; refresh() }, 400)
}

function confirmDelete(member: { id: number; name: string }) {
  deleteTarget.value = member
  deleteModal.value = true
}

async function doDelete() {
  if (!deleteTarget.value) return
  deleting.value = true
  try {
    await api.del(`/admin/team-members/${deleteTarget.value.id}`)
    toast.add({ title: 'Member removed', color: 'success', icon: 'i-heroicons-check-circle' })
    deleteModal.value = false
    refresh()
  } catch {
    toast.add({ title: 'Failed to remove', color: 'error', icon: 'i-heroicons-x-circle' })
  } finally {
    deleting.value = false
  }
}
</script>