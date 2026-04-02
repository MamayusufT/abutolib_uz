<template>
  <NuxtLayout name="admin">
    <div class="space-y-5">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2 flex-1 flex-wrap">
          <UInput
            v-model="search"
            placeholder="Search topics..."
            leading-icon="i-heroicons-magnifying-glass"
            size="sm"
            class="w-full max-w-xs"
            @input="onSearch"
          />

          <div class="relative" ref="filterWrapRef">
            <button
              type="button"
              class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 hover:border-gray-300 transition-colors min-w-[160px]"
              :class="filterOpen ? 'border-primary-500 ring-2 ring-primary-500/20' : ''"
              @click="filterOpen = !filterOpen"
            >
              <span v-if="selectedSubjectLabel" class="flex items-center gap-2 flex-1 min-w-0">
                <span
                  class="w-4 h-4 rounded shrink-0 flex items-center justify-center"
                  :style="{ background: selectedSubjectColor ?? '#e5e7eb' }"
                >
                  <UIcon name="i-heroicons-book-open" class="w-2.5 h-2.5 text-white" />
                </span>
                <span class="truncate text-gray-800 dark:text-gray-200">{{ selectedSubjectLabel }}</span>
              </span>
              <span v-else class="flex-1 text-gray-400 text-left">All subjects</span>
              <UIcon name="i-heroicons-chevron-up-down" class="w-4 h-4 text-gray-400 shrink-0" />
            </button>

            <Transition name="dropdown">
              <div
                v-if="filterOpen"
                class="absolute z-50 left-0 mt-1.5 w-56 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden"
              >
                <div class="p-2 border-b border-gray-100 dark:border-gray-800">
                  <div class="flex items-center gap-2 px-2 py-1.5 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <UIcon name="i-heroicons-magnifying-glass" class="w-3.5 h-3.5 text-gray-400 shrink-0" />
                    <input
                      v-model="subjectSearch"
                      type="text"
                      placeholder="Search..."
                      class="flex-1 bg-transparent text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 outline-none"
                    />
                  </div>
                </div>
                <div class="max-h-52 overflow-y-auto py-1">
                  <button
                    type="button"
                    class="w-full flex items-center gap-2 px-3 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                    :class="filterSubject === null ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600' : 'text-gray-700 dark:text-gray-300'"
                    @click="selectFilter(null)"
                  >
                    <span class="w-5 h-5 rounded bg-gray-200 dark:bg-gray-700 flex items-center justify-center shrink-0">
                      <UIcon name="i-heroicons-squares-2x2" class="w-3 h-3 text-gray-500" />
                    </span>
                    All subjects
                    <UIcon v-if="filterSubject === null" name="i-heroicons-check" class="w-4 h-4 text-primary-500 ml-auto" />
                  </button>
                  <button
                    v-for="s in filteredSubjectList"
                    :key="s.id"
                    type="button"
                    class="w-full flex items-center gap-2 px-3 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                    :class="filterSubject === s.id ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600' : 'text-gray-700 dark:text-gray-300'"
                    @click="selectFilter(s.id)"
                  >
                    <span
                      class="w-5 h-5 rounded flex items-center justify-center shrink-0"
                      :style="{ background: s.color ?? '#e5e7eb' }"
                    >
                      <UIcon name="i-heroicons-book-open" class="w-3 h-3 text-white" />
                    </span>
                    <span class="truncate flex-1">{{ s.name }}</span>
                    <UIcon v-if="filterSubject === s.id" name="i-heroicons-check" class="w-4 h-4 text-primary-500 ml-auto shrink-0" />
                  </button>
                </div>
              </div>
            </Transition>
          </div>
        </div>

        <NuxtLink to="/topics/create">
          <UButton icon="i-heroicons-plus" size="sm" color="primary">Add Topic</UButton>
        </NuxtLink>
      </div>

      <UCard>
        <div v-if="pending" class="flex items-center justify-center py-16">
          <UIcon name="i-heroicons-arrow-path" class="w-6 h-6 text-gray-400 animate-spin" />
        </div>

        <div v-else-if="!topics.length" class="flex flex-col items-center justify-center py-16 gap-3 text-gray-400">
          <UIcon name="i-heroicons-document-text" class="w-10 h-10 opacity-40" />
          <p class="text-sm">No topics found</p>
        </div>

        <div v-else class="divide-y divide-gray-100 dark:divide-gray-800 -mx-4 sm:-mx-6">
          <div class="grid grid-cols-12 gap-4 px-4 sm:px-6 py-2.5 bg-gray-50 dark:bg-gray-800/50">
            <span class="col-span-4 text-xs font-semibold uppercase tracking-widest text-gray-400">Topic</span>
            <span class="col-span-3 text-xs font-semibold uppercase tracking-widest text-gray-400">Subject</span>
            <span class="col-span-2 text-xs font-semibold uppercase tracking-widest text-gray-400">Questions</span>
            <span class="col-span-2 text-xs font-semibold uppercase tracking-widest text-gray-400">Status</span>
            <span class="col-span-1" />
          </div>

          <div
            v-for="row in topics"
            :key="row.id"
            class="grid grid-cols-12 gap-4 items-center px-4 sm:px-6 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors"
          >
            <div class="col-span-4 min-w-0">
              <p class="font-medium text-sm text-gray-900 dark:text-white truncate">{{ row.name }}</p>
              <p v-if="row.time_limit" class="text-xs text-gray-400 mt-0.5">
                <UIcon name="i-heroicons-clock" class="w-3 h-3 inline mr-0.5" />{{ row.time_limit }} min
              </p>
            </div>

            <div class="col-span-3 min-w-0">
              <div v-if="row.subject" class="flex items-center gap-2">
                <span
                  class="w-5 h-5 rounded flex items-center justify-center shrink-0"
                  :style="{ background: row.subject.color ?? '#e5e7eb' }"
                >
                  <UIcon name="i-heroicons-book-open" class="w-3 h-3 text-white" />
                </span>
                <span class="text-sm text-gray-600 dark:text-gray-400 truncate">{{ row.subject.name }}</span>
              </div>
            </div>

            <div class="col-span-2">
              <UBadge :label="String(row.questions_count ?? 0)" color="primary" variant="soft" size="sm" />
            </div>

            <div class="col-span-2">
              <UBadge
                :label="row.is_active ? 'Active' : 'Inactive'"
                :color="row.is_active ? 'success' : 'neutral'"
                variant="soft" size="sm"
              />
            </div>

            <div class="col-span-1 flex items-center justify-end gap-1">
              <NuxtLink :to="`/topics/${row.id}/edit`">
                <UButton icon="i-heroicons-pencil-square" color="neutral" variant="ghost" size="xs" />
              </NuxtLink>
              <UButton icon="i-heroicons-trash" color="error" variant="ghost" size="xs" @click="confirmDelete(row)" />
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between pt-4 mt-4 border-t border-gray-100 dark:border-gray-800">
          <p class="text-xs text-gray-400">Total: {{ meta?.total ?? 0 }} topics</p>
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
                <p class="font-semibold text-gray-900 dark:text-white text-sm">Delete Topic</p>
                <p class="text-xs text-gray-400">All questions will be removed</p>
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
const filterSubject = ref<number | null>(null)
const filterOpen = ref(false)
const filterWrapRef = ref<HTMLElement | null>(null)
const subjectSearch = ref('')
const deleteModal = ref(false)
const deleteTarget = ref<{ id: number; name: string } | null>(null)
const deleting = ref(false)

const { data: subjectsData } = await useAsyncData('subjects-select',
  () => api.get<any>('/admin/subjects?per_page=100')
)

const allSubjects = computed<{ id: number; name: string; color?: string }[]>(
  () => subjectsData.value?.data ?? []
)

const filteredSubjectList = computed(() =>
  allSubjects.value.filter(s =>
    s.name.toLowerCase().includes(subjectSearch.value.toLowerCase())
  )
)

const selectedSubjectLabel = computed(() =>
  allSubjects.value.find(s => s.id === filterSubject.value)?.name ?? null
)

const selectedSubjectColor = computed(() =>
  allSubjects.value.find(s => s.id === filterSubject.value)?.color ?? null
)

function selectFilter(id: number | null) {
  filterSubject.value = id
  filterOpen.value = false
  subjectSearch.value = ''
  page.value = 1
  refresh()
}

const { data, pending, refresh } = await useAsyncData(
  'topics',
  () => {
    const params = new URLSearchParams({
      page: String(page.value),
      per_page: '15',
      search: search.value,
    })
    if (filterSubject.value) params.set('subject_id', String(filterSubject.value))
    return api.get<any>(`/admin/topics?${params}`)
  },
  { watch: [page] }
)

const topics = computed(() => data.value?.data ?? [])
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
    await api.del(`/admin/topics/${deleteTarget.value.id}`)
    toast.add({ title: 'Topic deleted', color: 'success', icon: 'i-heroicons-check-circle' })
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
  if (filterWrapRef.value && !filterWrapRef.value.contains(e.target as Node)) {
    filterOpen.value = false
  }
}
</script>

<style scoped>
.dropdown-enter-active, .dropdown-leave-active { transition: all 0.15s ease; }
.dropdown-enter-from, .dropdown-leave-to { opacity: 0; transform: translateY(-4px); }
</style>