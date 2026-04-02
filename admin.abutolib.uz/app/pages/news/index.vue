<template>
  <NuxtLayout name="admin">
    <div class="space-y-5">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2 flex-1 flex-wrap">
          <UInput
            v-model="search"
            placeholder="Search news..."
            leading-icon="i-heroicons-magnifying-glass"
            size="sm"
            class="w-full max-w-xs"
            @input="onSearch"
          />

          <div class="relative" ref="statusWrapRef">
            <button
              type="button"
              class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 hover:border-gray-300 transition-colors min-w-[130px]"
              :class="statusOpen ? 'border-primary-500 ring-2 ring-primary-500/20' : ''"
              @click="statusOpen = !statusOpen"
            >
              <span v-if="filterStatus" class="flex items-center gap-1.5 flex-1">
                <span class="w-2 h-2 rounded-full shrink-0" :class="statusDot(filterStatus)" />
                <span class="capitalize text-gray-800 dark:text-gray-200">{{ filterStatus }}</span>
              </span>
              <span v-else class="flex-1 text-gray-400 text-left">All statuses</span>
              <UIcon name="i-heroicons-chevron-up-down" class="w-4 h-4 text-gray-400 shrink-0" />
            </button>

            <Transition name="dropdown">
              <div
                v-if="statusOpen"
                class="absolute z-50 left-0 mt-1.5 w-44 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden py-1"
              >
                <button
                  v-for="opt in statusOptions"
                  :key="opt.value ?? 'all'"
                  type="button"
                  class="w-full flex items-center gap-2.5 px-3 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                  :class="filterStatus === opt.value ? 'text-primary-600 bg-primary-50 dark:bg-primary-900/20' : 'text-gray-700 dark:text-gray-300'"
                  @click="selectStatus(opt.value)"
                >
                  <span class="w-2 h-2 rounded-full shrink-0" :class="opt.dot" />
                  {{ opt.label }}
                  <UIcon v-if="filterStatus === opt.value" name="i-heroicons-check" class="w-4 h-4 text-primary-500 ml-auto" />
                </button>
              </div>
            </Transition>
          </div>
        </div>

        <NuxtLink to="/news/create">
          <UButton icon="i-heroicons-plus" size="sm" color="primary">Add News</UButton>
        </NuxtLink>
      </div>

      <UCard>
        <div v-if="pending" class="flex items-center justify-center py-16">
          <UIcon name="i-heroicons-arrow-path" class="w-6 h-6 text-gray-400 animate-spin" />
        </div>

        <div v-else-if="!newsList.length" class="flex flex-col items-center justify-center py-16 gap-3 text-gray-400">
          <UIcon name="i-heroicons-newspaper" class="w-10 h-10 opacity-40" />
          <p class="text-sm">No news found</p>
        </div>

        <div v-else class="divide-y divide-gray-100 dark:divide-gray-800 -mx-4 sm:-mx-6">
          <div class="grid grid-cols-12 gap-4 px-4 sm:px-6 py-2.5 bg-gray-50 dark:bg-gray-800/50">
            <span class="col-span-5 text-xs font-semibold uppercase tracking-widest text-gray-400">Title</span>
            <span class="col-span-2 text-xs font-semibold uppercase tracking-widest text-gray-400">Category</span>
            <span class="col-span-2 text-xs font-semibold uppercase tracking-widest text-gray-400">Status</span>
            <span class="col-span-2 text-xs font-semibold uppercase tracking-widest text-gray-400">Published</span>
            <span class="col-span-1" />
          </div>

          <div
            v-for="row in newsList"
            :key="row.id"
            class="grid grid-cols-12 gap-4 items-center px-4 sm:px-6 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors"
          >
            <div class="col-span-5 flex items-center gap-3 min-w-0">
              <div class="w-10 h-10 rounded-lg overflow-hidden shrink-0 bg-gray-100 dark:bg-gray-800">
                <img v-if="row.image" :src="row.image" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full flex items-center justify-center">
                  <UIcon name="i-heroicons-newspaper" class="w-5 h-5 text-gray-300" />
                </div>
              </div>
              <div class="min-w-0">
                <p class="font-medium text-sm text-gray-900 dark:text-white truncate">{{ row.title }}</p>
                <p v-if="row.excerpt" class="text-xs text-gray-400 truncate">{{ row.excerpt }}</p>
              </div>
            </div>

            <div class="col-span-2">
              <span v-if="row.category" class="text-sm text-gray-600 dark:text-gray-400">{{ row.category }}</span>
              <span v-else class="text-xs text-gray-300 dark:text-gray-600">—</span>
            </div>

            <div class="col-span-2">
              <UBadge
                :label="row.status"
                :color="statusColor(row.status)"
                variant="soft"
                size="sm"
                class="capitalize"
              />
            </div>

            <div class="col-span-2">
              <span class="text-xs text-gray-500 dark:text-gray-400">
                {{ row.published_at ? formatDate(row.published_at) : '—' }}
              </span>
            </div>

            <div class="col-span-1 flex items-center justify-end gap-1">
              <NuxtLink :to="`/news/${row.id}/edit`">
                <UButton icon="i-heroicons-pencil-square" color="neutral" variant="ghost" size="xs" />
              </NuxtLink>
              <UButton icon="i-heroicons-trash" color="error" variant="ghost" size="xs" @click="confirmDelete(row)" />
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between pt-4 mt-4 border-t border-gray-100 dark:border-gray-800">
          <p class="text-xs text-gray-400">Total: {{ meta?.total ?? 0 }} articles</p>
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
                <p class="font-semibold text-gray-900 dark:text-white text-sm">Delete Article</p>
                <p class="text-xs text-gray-400">This action cannot be undone</p>
              </div>
            </div>
          </template>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Are you sure you want to delete
            <span class="font-medium text-gray-900 dark:text-white">{{ deleteTarget?.title }}</span>?
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
const filterStatus = ref<string | null>(null)
const statusOpen = ref(false)
const statusWrapRef = ref<HTMLElement | null>(null)
const deleteModal = ref(false)
const deleteTarget = ref<{ id: number; title: string } | null>(null)
const deleting = ref(false)

const statusOptions = [
  { label: 'All statuses', value: null,        dot: 'bg-gray-300' },
  { label: 'Draft',        value: 'draft',     dot: 'bg-amber-400' },
  { label: 'Published',    value: 'published', dot: 'bg-green-400' },
  { label: 'Archived',     value: 'archived',  dot: 'bg-gray-400' },
]

function selectStatus(val: string | null) {
  filterStatus.value = val
  statusOpen.value = false
  page.value = 1
  refresh()
}

function statusDot(status: string) {
  return statusOptions.find(o => o.value === status)?.dot ?? 'bg-gray-300'
}

function statusColor(status: string) {
  if (status === 'published') return 'success'
  if (status === 'draft') return 'warning'
  return 'neutral'
}

function formatDate(dt: string) {
  return new Date(dt).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
}

const { data, pending, refresh } = await useAsyncData(
  'news',
  () => {
    const params = new URLSearchParams({ page: String(page.value), per_page: '15', search: search.value })
    if (filterStatus.value) params.set('status', filterStatus.value)
    return api.get<any>(`/admin/news?${params}`)
  },
  { watch: [page] }
)

const newsList = computed(() => data.value?.data ?? [])
const meta = computed(() => data.value?.meta ?? null)

let searchTimer: ReturnType<typeof setTimeout>
function onSearch() {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => { page.value = 1; refresh() }, 400)
}

function confirmDelete(row: { id: number; title: string }) {
  deleteTarget.value = row
  deleteModal.value = true
}

async function doDelete() {
  if (!deleteTarget.value) return
  deleting.value = true
  try {
    await api.del(`/admin/news/${deleteTarget.value.id}`)
    toast.add({ title: 'Article deleted', color: 'success', icon: 'i-heroicons-check-circle' })
    deleteModal.value = false
    refresh()
  } catch {
    toast.add({ title: 'Failed to delete', color: 'error', icon: 'i-heroicons-x-circle' })
  } finally {
    deleting.value = false
  }
}

onMounted(() => document.addEventListener('click', onClickOutside))
onBeforeUnmount(() => document.removeEventListener('click', onClickOutside))

function onClickOutside(e: MouseEvent) {
  if (statusWrapRef.value && !statusWrapRef.value.contains(e.target as Node)) statusOpen.value = false
}
</script>

<style scoped>
.dropdown-enter-active, .dropdown-leave-active { transition: all 0.15s ease; }
.dropdown-enter-from, .dropdown-leave-to { opacity: 0; transform: translateY(-4px); }
</style>