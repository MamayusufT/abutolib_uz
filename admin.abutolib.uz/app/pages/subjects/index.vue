<template>
  <NuxtLayout name="admin">
    <div class="space-y-4">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <UInput
          v-model="search"
          placeholder="Search subjects..."
          leading-icon="i-heroicons-magnifying-glass"
          size="sm"
          class="w-full sm:w-72"
          @input="onSearch"
        />
        <NuxtLink to="/subjects/create">
          <UButton icon="i-heroicons-plus" size="sm" color="primary" class="w-full sm:w-auto justify-center">
            <span class="hidden sm:inline">Add Subject</span>
            <span class="sm:hidden">Add</span>
          </UButton>
        </NuxtLink>
      </div>

      <UCard :ui="{ body: { padding: 'p-0 sm:p-0' } }">
        <div v-if="pending" class="flex items-center justify-center py-20">
          <UIcon name="i-heroicons-arrow-path" class="w-6 h-6 text-gray-400 animate-spin" />
        </div>

        <div v-else-if="!subjects.length" class="flex flex-col items-center justify-center py-20 gap-3">
          <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
            <UIcon name="i-heroicons-book-open" class="w-7 h-7 text-gray-400" />
          </div>
          <div class="text-center">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">No subjects found</p>
            <p class="text-xs text-gray-400 mt-1">Create your first subject to get started</p>
          </div>
        </div>

        <div v-else>
          <div class="hidden sm:grid grid-cols-12 gap-4 px-4 sm:px-6 py-3 border-b border-gray-100 dark:border-gray-800 bg-gray-50/80 dark:bg-gray-800/40">
            <span class="col-span-5 text-xs font-semibold uppercase tracking-widest text-gray-400">Subject</span>
            <span class="col-span-2 text-xs font-semibold uppercase tracking-widest text-gray-400">Topics</span>
            <span class="col-span-2 text-xs font-semibold uppercase tracking-widest text-gray-400">Order</span>
            <span class="col-span-2 text-xs font-semibold uppercase tracking-widest text-gray-400">Status</span>
            <span class="col-span-1" />
          </div>

          <div class="divide-y divide-gray-100 dark:divide-gray-800">
            <div
              v-for="row in subjects"
              :key="row.id"
              class="group px-4 sm:px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors"
            >
              <div class="sm:grid sm:grid-cols-12 sm:gap-4 sm:items-center flex flex-col gap-3">
                <div class="sm:col-span-5 flex items-center gap-3 min-w-0">
                  <div
                    class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-sm"
                    :style="{ background: row.color ?? '#6366f1' }"
                  >
                    <UIcon name="i-heroicons-book-open" class="w-5 h-5 text-white" />
                  </div>
                  <div class="min-w-0">
                    <p class="font-semibold text-sm text-gray-900 dark:text-white truncate">{{ row.name }}</p>
                    <p class="text-xs text-gray-400 truncate mt-0.5">{{ row.slug }}</p>
                  </div>
                </div>

                <div class="sm:col-span-2 flex items-center gap-2 sm:block">
                  <span class="sm:hidden text-xs text-gray-400 w-16 shrink-0">Topics:</span>
                  <UBadge :label="String(row.topics_count ?? 0)" color="primary" variant="soft" size="sm" />
                </div>

                <div class="sm:col-span-2 flex items-center gap-2 sm:block">
                  <span class="sm:hidden text-xs text-gray-400 w-16 shrink-0">Order:</span>
                  <span class="text-sm text-gray-600 dark:text-gray-400">{{ row.order }}</span>
                </div>

                <div class="sm:col-span-2 flex items-center gap-2 sm:block">
                  <span class="sm:hidden text-xs text-gray-400 w-16 shrink-0">Status:</span>
                  <UBadge
                    :label="row.is_active ? 'Active' : 'Inactive'"
                    :color="row.is_active ? 'success' : 'neutral'"
                    variant="soft"
                    size="sm"
                  />
                </div>

                <div class="sm:col-span-1 flex items-center gap-1 sm:justify-end">
                  <NuxtLink :to="`/subjects/${row.id}/edit`">
                    <UButton icon="i-heroicons-pencil-square" color="neutral" variant="ghost" size="xs" />
                  </NuxtLink>
                  <UButton
                    icon="i-heroicons-trash"
                    color="error"
                    variant="ghost"
                    size="xs"
                    @click="confirmDelete(row)"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 px-4 sm:px-6 py-4 border-t border-gray-100 dark:border-gray-800">
          <p class="text-xs text-gray-400">{{ meta?.total ?? 0 }} subjects total</p>
          <UPagination
            v-if="meta && meta.last_page > 1"
            v-model="page"
            :page-count="meta.per_page"
            :total="meta.total"
            size="xs"
          />
        </div>
      </UCard>
    </div>

    <UModal v-model:open="deleteModal">
      <template #content>
        <UCard>
          <template #header>
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-950/30 flex items-center justify-center shrink-0">
                <UIcon name="i-heroicons-exclamation-triangle" class="w-5 h-5 text-red-500" />
              </div>
              <div>
                <p class="font-semibold text-gray-900 dark:text-white text-sm">Delete Subject</p>
                <p class="text-xs text-gray-400 mt-0.5">This action cannot be undone</p>
              </div>
            </div>
          </template>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Are you sure you want to delete
            <span class="font-semibold text-gray-900 dark:text-white">{{ deleteTarget?.name }}</span>?
            All topics and files will be permanently removed.
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
const deleteModal = ref(false)
const deleteTarget = ref<{ id: number; name: string } | null>(null)
const deleting = ref(false)

const { data, pending, refresh } = await useAsyncData(
  'subjects',
  () => api.get<any>(`/admin/subjects?page=${page.value}&per_page=15&search=${encodeURIComponent(search.value)}`),
  { watch: [page] }
)

const subjects = computed(() => data.value?.data ?? [])
const meta = computed(() => data.value?.meta ?? null)

let searchTimer: ReturnType<typeof setTimeout>
function onSearch() {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => { page.value = 1; refresh() }, 400)
}

function confirmDelete(row: { id: number; name: string }) {
  deleteTarget.value = row
  deleteModal.value = true
}

async function doDelete() {
  if (!deleteTarget.value) return
  deleting.value = true
  try {
    await api.del(`/admin/subjects/${deleteTarget.value.id}`)
    toast.add({ title: 'Subject deleted', color: 'success', icon: 'i-heroicons-check-circle' })
    deleteModal.value = false
    refresh()
  } catch {
    toast.add({ title: 'Failed to delete', color: 'error', icon: 'i-heroicons-x-circle' })
  } finally {
    deleting.value = false
  }
}
</script>