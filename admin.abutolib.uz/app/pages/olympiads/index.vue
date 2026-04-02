<template>
  <NuxtLayout name="admin">
    <div class="space-y-5">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2 flex-1 flex-wrap">
          <UInput
            v-model="search"
            placeholder="Search olympiads..."
            leading-icon="i-heroicons-magnifying-glass"
            size="sm"
            class="w-full max-w-xs"
            @input="onSearch"
          />

          <div class="relative" ref="statusWrapRef">
            <button
              type="button"
              class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 min-w-[130px] hover:border-gray-300 transition-colors"
              :class="statusOpen ? 'border-primary-500 ring-2 ring-primary-500/20' : ''"
              @click="statusOpen = !statusOpen"
            >
              <span v-if="filterStatus" class="flex items-center gap-1.5 flex-1">
                <span class="w-2 h-2 rounded-full shrink-0" :class="statusDot(filterStatus)" />
                <span class="capitalize text-gray-800 dark:text-gray-200">{{ filterStatus }}</span>
              </span>
              <span v-else class="flex-1 text-left text-gray-400">All statuses</span>
              <UIcon name="i-heroicons-chevron-up-down" class="w-4 h-4 text-gray-400 shrink-0" />
            </button>
            <Transition name="dd">
              <div v-if="statusOpen" class="absolute z-50 left-0 mt-1.5 w-44 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden py-1">
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

          <div class="relative" ref="typeWrapRef">
            <button
              type="button"
              class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 min-w-[120px] hover:border-gray-300 transition-colors"
              :class="typeOpen ? 'border-primary-500 ring-2 ring-primary-500/20' : ''"
              @click="typeOpen = !typeOpen"
            >
              <span v-if="filterType" class="flex-1 capitalize text-gray-800 dark:text-gray-200">{{ filterType }}</span>
              <span v-else class="flex-1 text-left text-gray-400">All types</span>
              <UIcon name="i-heroicons-chevron-up-down" class="w-4 h-4 text-gray-400 shrink-0" />
            </button>
            <Transition name="dd">
              <div v-if="typeOpen" class="absolute z-50 left-0 mt-1.5 w-40 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden py-1">
                <button
                  v-for="t in typeOptions"
                  :key="t.value ?? 'all'"
                  type="button"
                  class="w-full flex items-center gap-2 px-3 py-2.5 text-sm capitalize hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                  :class="filterType === t.value ? 'text-primary-600 bg-primary-50 dark:bg-primary-900/20' : 'text-gray-700 dark:text-gray-300'"
                  @click="selectType(t.value)"
                >
                  <UIcon :name="t.icon" class="w-4 h-4 shrink-0" />
                  {{ t.label }}
                  <UIcon v-if="filterType === t.value" name="i-heroicons-check" class="w-4 h-4 text-primary-500 ml-auto" />
                </button>
              </div>
            </Transition>
          </div>
        </div>

        <NuxtLink to="/olympiads/create">
          <UButton icon="i-heroicons-plus" size="sm" color="primary">Add Olympiad</UButton>
        </NuxtLink>
      </div>

      <UCard>
        <div v-if="pending" class="flex items-center justify-center py-16">
          <UIcon name="i-heroicons-arrow-path" class="w-6 h-6 text-gray-400 animate-spin" />
        </div>

        <div v-else-if="!list.length" class="flex flex-col items-center justify-center py-16 gap-3 text-gray-400">
          <UIcon name="i-heroicons-trophy" class="w-10 h-10 opacity-40" />
          <p class="text-sm">No olympiads found</p>
        </div>

        <div v-else class="divide-y divide-gray-100 dark:divide-gray-800 -mx-4 sm:-mx-6">
          <div class="grid grid-cols-12 gap-3 px-4 sm:px-6 py-2.5 bg-gray-50 dark:bg-gray-800/50">
            <span class="col-span-4 text-xs font-semibold uppercase tracking-widest text-gray-400">Name</span>
            <span class="col-span-2 text-xs font-semibold uppercase tracking-widest text-gray-400">Type</span>
            <span class="col-span-2 text-xs font-semibold uppercase tracking-widest text-gray-400">Period</span>
            <span class="col-span-1 text-xs font-semibold uppercase tracking-widest text-gray-400">Q</span>
            <span class="col-span-1 text-xs font-semibold uppercase tracking-widest text-gray-400">Reg</span>
            <span class="col-span-1 text-xs font-semibold uppercase tracking-widest text-gray-400">Status</span>
            <span class="col-span-1" />
          </div>

          <div
            v-for="row in list"
            :key="row.id"
            class="grid grid-cols-12 gap-3 items-center px-4 sm:px-6 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors"
          >
            <div class="col-span-4 min-w-0">
              <p class="font-medium text-sm text-gray-900 dark:text-white truncate">{{ row.name_uz }}</p>
              <p class="text-xs text-gray-400 mt-0.5">
                <UIcon name="i-heroicons-clock" class="w-3 h-3 inline mr-0.5" />{{ row.duration_label }}
                <span class="mx-1">·</span>pass {{ row.pass_score }}%
              </p>
            </div>

            <div class="col-span-2">
              <span
                class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-xs font-medium capitalize"
                :class="typeBadge(row.type)"
              >
                <UIcon :name="typeIcon(row.type)" class="w-3 h-3" />
                {{ row.type }}
              </span>
            </div>

            <div class="col-span-2 min-w-0">
              <p class="text-xs text-gray-700 dark:text-gray-300 truncate">{{ fmtDate(row.starts_at) }}</p>
              <p class="text-xs text-gray-400 truncate">{{ fmtDate(row.ends_at) }}</p>
            </div>

            <div class="col-span-1">
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ row.actual_questions_count ?? 0 }}</span>
            </div>

            <div class="col-span-1">
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ row.registrations_count ?? 0 }}</span>
            </div>

            <div class="col-span-1">
              <span
                class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium"
                :class="statusClass(row.status)"
              >
                <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="statusDot(row.status)" />
                {{ row.status }}
              </span>
            </div>

            <div class="col-span-1 flex items-center justify-end gap-1">
              <NuxtLink :to="`/olympiads/${row.id}/edit`">
                <UButton icon="i-heroicons-pencil-square" color="neutral" variant="ghost" size="xs" />
              </NuxtLink>
              <UButton icon="i-heroicons-trash" color="error" variant="ghost" size="xs" @click="confirmDelete(row)" />
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between pt-4 mt-4 border-t border-gray-100 dark:border-gray-800">
          <p class="text-xs text-gray-400">Total: {{ meta?.total ?? 0 }}</p>
          <UPagination v-if="meta && meta.last_page > 1" v-model="page" :page-count="meta.per_page" :total="meta.total" size="xs" />
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
                <p class="font-semibold text-gray-900 dark:text-white text-sm">Delete Olympiad</p>
                <p class="text-xs text-gray-400">All questions and registrations will be removed</p>
              </div>
            </div>
          </template>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Are you sure you want to delete
            <span class="font-medium text-gray-900 dark:text-white">{{ deleteTarget?.name_uz }}</span>?
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
const filterType = ref<string | null>(null)
const statusOpen = ref(false)
const typeOpen = ref(false)
const statusWrapRef = ref<HTMLElement | null>(null)
const typeWrapRef = ref<HTMLElement | null>(null)
const deleteModal = ref(false)
const deleteTarget = ref<{ id: number; name_uz: string } | null>(null)
const deleting = ref(false)

const statusOptions = [
  { label: 'All',      value: null,       dot: 'bg-gray-300' },
  { label: 'Upcoming', value: 'upcoming', dot: 'bg-blue-400' },
  { label: 'Live',     value: 'live',     dot: 'bg-green-400' },
  { label: 'Finished', value: 'finished', dot: 'bg-gray-400' },
]

const typeOptions = [
  { label: 'All',      value: null,        icon: 'i-heroicons-squares-2x2' },
  { label: 'Quiz',     value: 'quiz',      icon: 'i-heroicons-academic-cap' },
  { label: 'Olympiad', value: 'olympiad',  icon: 'i-heroicons-trophy' },
  { label: 'Exam',     value: 'exam',      icon: 'i-heroicons-clipboard-document-check' },
]

function statusDot(s: string) {
  return { upcoming: 'bg-blue-400', live: 'bg-green-500', finished: 'bg-gray-400' }[s] ?? 'bg-gray-300'
}

function statusClass(s: string) {
  return {
    upcoming: 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
    live:     'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400',
    finished: 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400',
  }[s] ?? 'bg-gray-100 text-gray-400'
}

function typeBadge(t: string) {
  return {
    quiz:     'bg-purple-50 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400',
    olympiad: 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400',
    exam:     'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
  }[t] ?? 'bg-gray-100 text-gray-600'
}

function typeIcon(t: string) {
  return { quiz: 'i-heroicons-academic-cap', olympiad: 'i-heroicons-trophy', exam: 'i-heroicons-clipboard-document-check' }[t] ?? 'i-heroicons-question-mark-circle'
}

function fmtDate(d: string) {
  return new Date(d).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

function selectStatus(val: string | null) { filterStatus.value = val; statusOpen.value = false; page.value = 1; refresh() }
function selectType(val: string | null)   { filterType.value = val;   typeOpen.value = false;   page.value = 1; refresh() }

const { data, pending, refresh } = await useAsyncData('olympiads', () => {
  let url = `/admin/olympiads?page=${page.value}&per_page=15&search=${encodeURIComponent(search.value)}`
  if (filterStatus.value) url += `&status=${filterStatus.value}`
  if (filterType.value)   url += `&type=${filterType.value}`
  return api.get<any>(url)
}, { watch: [page] })

const list = computed(() => data.value?.data ?? [])
const meta = computed(() => data.value?.meta ?? null)

let timer: ReturnType<typeof setTimeout>
function onSearch() { clearTimeout(timer); timer = setTimeout(() => { page.value = 1; refresh() }, 400) }

function confirmDelete(row: { id: number; name_uz: string }) { deleteTarget.value = row; deleteModal.value = true }

async function doDelete() {
  if (!deleteTarget.value) return
  deleting.value = true
  try {
    await api.del(`/admin/olympiads/${deleteTarget.value.id}`)
    toast.add({ title: 'Olympiad deleted', color: 'success', icon: 'i-heroicons-check-circle' })
    deleteModal.value = false; refresh()
  } catch { toast.add({ title: 'Failed to delete', color: 'error', icon: 'i-heroicons-x-circle' }) }
  finally { deleting.value = false }
}

onMounted(() => document.addEventListener('click', outside))
onBeforeUnmount(() => document.removeEventListener('click', outside))
function outside(e: MouseEvent) {
  if (statusWrapRef.value && !statusWrapRef.value.contains(e.target as Node)) statusOpen.value = false
  if (typeWrapRef.value  && !typeWrapRef.value.contains(e.target as Node))  typeOpen.value  = false
}
</script>

<style scoped>
.dd-enter-active, .dd-leave-active { transition: all 0.15s ease; }
.dd-enter-from, .dd-leave-to { opacity: 0; transform: translateY(-4px); }
</style>