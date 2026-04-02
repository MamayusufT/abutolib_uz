<template>
  <div class="relative" ref="wrapRef">
    <button
      type="button"
      class="w-full flex items-center justify-between gap-2 px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 hover:border-gray-300 dark:hover:border-gray-600 transition-colors"
      :class="open ? 'border-primary-500 dark:border-primary-500 ring-2 ring-primary-500/20' : ''"
      @click="toggle"
    >
      <span v-if="selected" class="flex items-center gap-2 min-w-0">
        <span
          class="w-5 h-5 rounded flex items-center justify-center shrink-0"
          :style="{ background: selected.color ?? '#e5e7eb' }"
        >
          <UIcon name="i-heroicons-book-open" class="w-3 h-3 text-white" />
        </span>
        <span class="truncate text-gray-900 dark:text-white">{{ selected.name }}</span>
      </span>
      <span v-else class="text-gray-400">{{ placeholder }}</span>
      <UIcon name="i-heroicons-chevron-up-down" class="w-4 h-4 text-gray-400 shrink-0" />
    </button>

    <Transition name="dropdown">
      <div
        v-if="open"
        class="absolute z-50 w-full mt-1.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden"
      >
        <div class="p-2 border-b border-gray-100 dark:border-gray-800">
          <div class="flex items-center gap-2 px-2 py-1.5 rounded-lg bg-gray-50 dark:bg-gray-800">
            <UIcon name="i-heroicons-magnifying-glass" class="w-3.5 h-3.5 text-gray-400 shrink-0" />
            <input
              ref="searchRef"
              v-model="search"
              type="text"
              placeholder="Search subjects..."
              class="flex-1 bg-transparent text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 outline-none"
              @input="onSearch"
            />
            <UIcon v-if="loading" name="i-heroicons-arrow-path" class="w-3.5 h-3.5 text-gray-400 animate-spin" />
          </div>
        </div>

        <div class="max-h-52 overflow-y-auto py-1">
          <div v-if="loading && !options.length" class="px-4 py-3 text-sm text-gray-400 text-center">
            Loading...
          </div>
          <div v-else-if="!options.length" class="px-4 py-3 text-sm text-gray-400 text-center">
            No subjects found
          </div>
          <button
            v-for="item in options"
            :key="item.id"
            type="button"
            class="w-full flex items-center gap-2.5 px-3 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-left"
            :class="modelValue === item.id ? 'bg-primary-50 dark:bg-primary-900/20' : ''"
            @click="select(item)"
          >
            <span
              class="w-6 h-6 rounded flex items-center justify-center shrink-0"
              :style="{ background: item.color ?? '#e5e7eb' }"
            >
              <UIcon name="i-heroicons-book-open" class="w-3.5 h-3.5 text-white" />
            </span>
            <span class="flex-1 truncate text-gray-800 dark:text-gray-200">{{ item.name }}</span>
            <UIcon v-if="modelValue === item.id" name="i-heroicons-check" class="w-4 h-4 text-primary-500 shrink-0" />
          </button>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{
  modelValue: number | null
  placeholder?: string
}>(), {
  placeholder: 'Select a subject',
})

const emit = defineEmits<{ 'update:modelValue': [id: number | null] }>()

const api = useApi()
const wrapRef = ref<HTMLElement | null>(null)
const searchRef = ref<HTMLInputElement | null>(null)
const open = ref(false)
const search = ref('')
const loading = ref(false)
const options = ref<{ id: number; name: string; color?: string }[]>([])

const selected = computed(() => options.value.find(o => o.id === props.modelValue) ?? null)

async function fetchOptions(q = '') {
  loading.value = true
  try {
    const res: any = await api.get('/admin/subjects', { search: q, per_page: 50 })
    options.value = Array.isArray(res?.data) ? res.data : []
  } catch (e) {
    options.value = []
  } finally {
    loading.value = false
  }
}

let timer: ReturnType<typeof setTimeout>
function onSearch() {
  clearTimeout(timer)
  timer = setTimeout(() => fetchOptions(search.value), 300)
}

async function toggle() {
  open.value = !open.value
  if (open.value) {
    fetchOptions()
    await nextTick()
    searchRef.value?.focus()
  }
}

function select(item: { id: number; name: string }) {
  emit('update:modelValue', item.id)
  open.value = false
  search.value = ''
}

onMounted(() => {
  if (props.modelValue) fetchOptions()
  document.addEventListener('click', onClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', onClickOutside)
})

function onClickOutside(e: MouseEvent) {
  if (wrapRef.value && !wrapRef.value.contains(e.target as Node)) {
    open.value = false
  }
}
</script>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active { transition: all 0.15s ease; }
.dropdown-enter-from,
.dropdown-leave-to { opacity: 0; transform: translateY(-4px); }
</style>