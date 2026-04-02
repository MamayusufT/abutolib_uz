<template>
  <div class="space-y-3">
    <div
      class="border-2 border-dashed rounded-xl p-6 text-center transition-colors cursor-pointer"
      :class="isDragging
        ? 'border-primary-400 bg-primary-50 dark:bg-primary-950/20'
        : 'border-gray-200 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-700'"
      @dragover.prevent="isDragging = true"
      @dragleave.prevent="isDragging = false"
      @drop.prevent="onDrop"
      @click="inputRef?.click()"
    >
      <UIcon name="i-heroicons-cloud-arrow-up" class="w-8 h-8 text-gray-400 mx-auto mb-2" />
      <p class="text-sm text-gray-500 dark:text-gray-400">
        Drag & drop files here, or <span class="text-primary-500 font-medium">browse</span>
      </p>
      <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Max 10MB per file</p>
      <input
        ref="inputRef"
        type="file"
        multiple
        class="hidden"
        @change="onFileChange"
      />
    </div>

    <div v-if="existingFiles.length" class="space-y-2">
      <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Existing files</p>
      <div
        v-for="file in existingFiles"
        :key="file.id"
        class="flex items-center gap-3 px-3 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900"
      >
        <UIcon :name="getFileIcon(file.file_type)" class="w-4 h-4 text-gray-400 shrink-0" />
        <div class="flex-1 min-w-0">
          <p class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ file.file_name }}</p>
          <p class="text-xs text-gray-400">{{ formatSize(file.file_size) }}</p>
        </div>
        <a :href="file.url" target="_blank" class="text-gray-400 hover:text-primary-500 transition-colors">
          <UIcon name="i-heroicons-arrow-top-right-on-square" class="w-4 h-4" />
        </a>
        <button
          type="button"
          class="text-gray-400 hover:text-red-500 transition-colors"
          @click="removeExisting(file.id)"
        >
          <UIcon name="i-heroicons-x-mark" class="w-4 h-4" />
        </button>
      </div>
    </div>

    <div v-if="newFiles.length" class="space-y-2">
      <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">New files</p>
      <div
        v-for="(file, index) in newFiles"
        :key="index"
        class="flex items-center gap-3 px-3 py-2.5 rounded-lg border border-primary-200 dark:border-primary-900/40 bg-primary-50 dark:bg-primary-950/20"
      >
        <UIcon :name="getFileIcon(file.type)" class="w-4 h-4 text-primary-400 shrink-0" />
        <div class="flex-1 min-w-0">
          <p class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ file.name }}</p>
          <p class="text-xs text-gray-400">{{ formatSize(file.size) }}</p>
        </div>
        <button
          type="button"
          class="text-gray-400 hover:text-red-500 transition-colors"
          @click="removeNew(index)"
        >
          <UIcon name="i-heroicons-x-mark" class="w-4 h-4" />
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface ExistingFile {
  id: number
  file_name: string
  file_type: string
  file_size: number
  url: string
}

const props = defineProps<{
  existingFiles?: ExistingFile[]
}>()

const emit = defineEmits<{
  'update:newFiles': [files: File[]]
  'update:deletedIds': [ids: number[]]
}>()

const inputRef = ref<HTMLInputElement | null>(null)
const isDragging = ref(false)
const newFiles = ref<File[]>([])
const deletedIds = ref<number[]>([])
const existingFiles = ref<ExistingFile[]>([...(props.existingFiles ?? [])])

watch(() => props.existingFiles, (val) => {
  existingFiles.value = [...(val ?? [])]
})

function onFileChange(e: Event) {
  const input = e.target as HTMLInputElement
  if (input.files) addFiles(Array.from(input.files))
  input.value = ''
}

function onDrop(e: DragEvent) {
  isDragging.value = false
  if (e.dataTransfer?.files) addFiles(Array.from(e.dataTransfer.files))
}

function addFiles(files: File[]) {
  newFiles.value.push(...files)
  emit('update:newFiles', newFiles.value)
}

function removeNew(index: number) {
  newFiles.value.splice(index, 1)
  emit('update:newFiles', newFiles.value)
}

function removeExisting(id: number) {
  existingFiles.value = existingFiles.value.filter(f => f.id !== id)
  deletedIds.value.push(id)
  emit('update:deletedIds', deletedIds.value)
}

function formatSize(bytes: number): string {
  if (bytes < 1024) return bytes + ' B'
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB'
  return (bytes / (1024 * 1024)).toFixed(1) + ' MB'
}

function getFileIcon(type: string): string {
  if (type.includes('image')) return 'i-heroicons-photo'
  if (type.includes('pdf')) return 'i-heroicons-document-text'
  if (type.includes('video')) return 'i-heroicons-video-camera'
  if (type.includes('audio')) return 'i-heroicons-musical-note'
  return 'i-heroicons-paper-clip'
}
</script>