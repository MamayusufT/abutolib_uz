<template>
  <div class="space-y-6">
    <UCard>
      <template #header>
        <div class="flex items-center gap-2">
          <UIcon name="i-heroicons-information-circle" class="w-5 h-5 text-primary-500" />
          <h3 class="font-semibold text-gray-900 dark:text-white">Asosiy ma'lumotlar</h3>
        </div>
      </template>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <UFormGroup label="Fan nomi *" class="md:col-span-2">
          <UInput
            v-model="form.name"
            placeholder="Masalan: Matematika"
            icon="i-heroicons-book-open"
            size="md"
            :disabled="loading"
          />
        </UFormGroup>

        <UFormGroup label="Rang">
          <div class="flex items-center gap-3">
            <input
              v-model="form.color"
              type="color"
              class="w-10 h-10 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer bg-transparent"
            />
            <UInput
              v-model="form.color"
              placeholder="#6366f1"
              class="flex-1"
              :disabled="loading"
            />
            <div
              class="w-10 h-10 rounded-lg flex-shrink-0"
              :style="{ background: form.color || '#6366f1' }"
            />
          </div>
        </UFormGroup>

        <UFormGroup label="Tartib raqami">
          <UInput
            v-model.number="form.order"
            type="number"
            placeholder="0"
            icon="i-heroicons-bars-3-bottom-left"
            :disabled="loading"
          />
        </UFormGroup>

        <UFormGroup label="Tavsif" class="md:col-span-2">
          <UTextarea
            v-model="form.description"
            placeholder="Fan haqida qisqacha ma'lumot..."
            :rows="3"
            :disabled="loading"
          />
        </UFormGroup>

        <UFormGroup label="Holat">
          <div class="flex items-center gap-3">
            <UToggle v-model="form.is_active" :disabled="loading" />
            <span class="text-sm text-gray-600 dark:text-gray-400">
              {{ form.is_active ? 'Faol' : 'Nofaol' }}
            </span>
          </div>
        </UFormGroup>
      </div>
    </UCard>

    <UCard>
      <template #header>
        <div class="flex items-center gap-2">
          <UIcon name="i-heroicons-photo" class="w-5 h-5 text-primary-500" />
          <h3 class="font-semibold text-gray-900 dark:text-white">Rasm</h3>
        </div>
      </template>

      <div class="flex items-start gap-4">
        <div
          v-if="imagePreview || form.existingImage"
          class="w-24 h-24 rounded-xl overflow-hidden border-2 border-gray-200 dark:border-gray-700 flex-shrink-0"
        >
          <img
            :src="imagePreview || form.existingImage"
            alt="Preview"
            class="w-full h-full object-cover"
          />
        </div>
        <div
          v-else
          class="w-24 h-24 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center flex-shrink-0"
        >
          <UIcon name="i-heroicons-photo" class="w-8 h-8 text-gray-400" />
        </div>

        <div class="flex-1">
          <label class="block">
            <input
              type="file"
              accept="image/*"
              class="hidden"
              ref="imageInput"
              @change="onImageChange"
            />
            <UButton
              color="gray"
              variant="outline"
              icon="i-heroicons-arrow-up-tray"
              @click="imageInput?.click()"
              :disabled="loading"
            >
              Rasm tanlash
            </UButton>
          </label>
          <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">PNG, JPG, WEBP. Maksimal 2MB</p>
          <UButton
            v-if="imagePreview"
            color="red"
            variant="ghost"
            size="xs"
            icon="i-heroicons-x-mark"
            class="mt-2"
            @click="removeImage"
          >
            Rasmni o'chirish
          </UButton>
        </div>
      </div>
    </UCard>

    <UCard>
      <template #header>
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <UIcon name="i-heroicons-tag" class="w-5 h-5 text-primary-500" />
            <h3 class="font-semibold text-gray-900 dark:text-white">Mavzular</h3>
          </div>
          <UButton
            icon="i-heroicons-plus"
            size="xs"
            variant="soft"
            @click="addTopic"
            :disabled="loading"
          >
            Mavzu qo'shish
          </UButton>
        </div>
      </template>

      <div v-if="form.topics.length === 0" class="py-8 text-center">
        <UIcon name="i-heroicons-tag" class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-2" />
        <p class="text-gray-400 dark:text-gray-500 text-sm">Hali mavzu qo'shilmagan</p>
      </div>

      <TransitionGroup
        name="topic-list"
        tag="div"
        class="space-y-3"
      >
        <div
          v-for="(topic, idx) in form.topics"
          :key="topic._key"
          class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700"
        >
          <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
              <div class="w-6 h-6 bg-primary-100 dark:bg-primary-900/40 rounded-full flex items-center justify-center">
                <span class="text-xs font-bold text-primary-600 dark:text-primary-400">{{ idx + 1 }}</span>
              </div>
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Mavzu</span>
            </div>
            <UButton
              icon="i-heroicons-trash"
              color="red"
              variant="ghost"
              size="xs"
              @click="removeTopic(idx)"
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <UFormGroup label="Nomi *">
              <UInput
                v-model="topic.name"
                placeholder="Mavzu nomi"
                size="sm"
                :disabled="loading"
              />
            </UFormGroup>

            <UFormGroup label="Vaqt chegarasi (daqiqa)">
              <UInput
                v-model.number="topic.time_limit"
                type="number"
                placeholder="30"
                size="sm"
                :disabled="loading"
              />
            </UFormGroup>

            <UFormGroup label="Tavsif" class="md:col-span-2">
              <UTextarea
                v-model="topic.description"
                placeholder="Mavzu tavsifi..."
                :rows="2"
                size="sm"
                :disabled="loading"
              />
            </UFormGroup>

            <UFormGroup label="Tartib">
              <UInput
                v-model.number="topic.order"
                type="number"
                placeholder="0"
                size="sm"
                :disabled="loading"
              />
            </UFormGroup>

            <UFormGroup label="Holat">
              <div class="flex items-center gap-2 h-9">
                <UToggle v-model="topic.is_active" size="sm" :disabled="loading" />
                <span class="text-xs text-gray-500 dark:text-gray-400">
                  {{ topic.is_active ? 'Faol' : 'Nofaol' }}
                </span>
              </div>
            </UFormGroup>
          </div>
        </div>
      </TransitionGroup>
    </UCard>

    <UCard>
      <template #header>
        <div class="flex items-center gap-2">
          <UIcon name="i-heroicons-paper-clip" class="w-5 h-5 text-primary-500" />
          <h3 class="font-semibold text-gray-900 dark:text-white">Fayllar</h3>
        </div>
      </template>

      <div
        class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center hover:border-primary-400 dark:hover:border-primary-500 transition-colors cursor-pointer"
        @click="filesInput?.click()"
        @dragover.prevent
        @drop.prevent="onDrop"
      >
        <UIcon name="i-heroicons-cloud-arrow-up" class="w-10 h-10 text-gray-400 mx-auto mb-2" />
        <p class="text-sm text-gray-600 dark:text-gray-400">
          Fayllarni bu yerga tashlang yoki <span class="text-primary-600 dark:text-primary-400 font-medium">tanlang</span>
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Maksimal 10MB har bir fayl</p>
        <input type="file" multiple class="hidden" ref="filesInput" @change="onFilesChange" />
      </div>

      <div v-if="existingFiles.length > 0" class="mt-4 space-y-2">
        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Mavjud fayllar</p>
        <div
          v-for="file in existingFiles"
          :key="file.id"
          class="flex items-center justify-between bg-gray-50 dark:bg-gray-800/50 rounded-lg px-3 py-2.5 border border-gray-200 dark:border-gray-700"
        >
          <div class="flex items-center gap-2 min-w-0">
            <UIcon :name="getFileIcon(file.file_type)" class="w-5 h-5 text-primary-500 flex-shrink-0" />
            <div class="min-w-0">
              <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ file.file_name }}</p>
              <p class="text-xs text-gray-400">{{ formatSize(file.file_size) }}</p>
            </div>
          </div>
          <UButton
            icon="i-heroicons-trash"
            color="red"
            variant="ghost"
            size="xs"
            @click="removeExistingFile(file)"
          />
        </div>
      </div>

      <div v-if="newFiles.length > 0" class="mt-4 space-y-2">
        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Yangi fayllar</p>
        <div
          v-for="(file, idx) in newFiles"
          :key="idx"
          class="flex items-center justify-between bg-primary-50 dark:bg-primary-900/20 rounded-lg px-3 py-2.5 border border-primary-200 dark:border-primary-800"
        >
          <div class="flex items-center gap-2 min-w-0">
            <UIcon :name="getFileIcon(file.type)" class="w-5 h-5 text-primary-500 flex-shrink-0" />
            <div class="min-w-0">
              <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ file.name }}</p>
              <p class="text-xs text-gray-400">{{ formatSize(file.size) }}</p>
            </div>
          </div>
          <UButton
            icon="i-heroicons-x-mark"
            color="gray"
            variant="ghost"
            size="xs"
            @click="newFiles.splice(idx, 1)"
          />
        </div>
      </div>
    </UCard>

    <div class="flex justify-end gap-3 pt-2">
      <UButton
        color="gray"
        variant="outline"
        to="/admin/subjects"
        :disabled="loading"
        icon="i-heroicons-arrow-left"
      >
        Orqaga
      </UButton>
      <UButton
        :loading="loading"
        icon="i-heroicons-check"
        class="font-semibold"
        @click="$emit('submit', getFormData())"
      >
        {{ submitLabel }}
      </UButton>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Topic {
  id?: number
  _key: string
  name: string
  description: string
  order: number
  time_limit: number | null
  is_active: boolean
}

interface SubjectFile {
  id: number
  file_name: string
  file_path: string
  file_type: string
  file_size: number
}

const props = defineProps<{
  initialData?: any
  loading?: boolean
  submitLabel?: string
}>()

const emit = defineEmits<{
  submit: [formData: FormData]
}>()

const imageInput = ref<HTMLInputElement>()
const filesInput = ref<HTMLInputElement>()
const imagePreview = ref<string | null>(null)
const imageFile = ref<File | null>(null)
const newFiles = ref<File[]>([])
const existingFiles = ref<SubjectFile[]>([])

const form = reactive({
  name: '',
  color: '#6366f1',
  order: 0,
  description: '',
  is_active: true,
  existingImage: null as string | null,
  topics: [] as Topic[],
})

watch(() => props.initialData, (data) => {
  if (!data) return
  form.name = data.name || ''
  form.color = data.color || '#6366f1'
  form.order = data.order || 0
  form.description = data.description || ''
  form.is_active = data.is_active ?? true
  form.existingImage = data.image ? `/storage/${data.image}` : null
  existingFiles.value = data.files || []
  form.topics = (data.topics || []).map((t: any) => ({
    ...t,
    _key: String(t.id || Math.random()),
  }))
}, { immediate: true })

function addTopic() {
  form.topics.push({
    _key: String(Math.random()),
    name: '',
    description: '',
    order: form.topics.length,
    time_limit: null,
    is_active: true,
  })
}

function removeTopic(idx: number) {
  form.topics.splice(idx, 1)
}

function onImageChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (!file) return
  imageFile.value = file
  imagePreview.value = URL.createObjectURL(file)
}

function removeImage() {
  imageFile.value = null
  imagePreview.value = null
  if (imageInput.value) imageInput.value.value = ''
}

function onFilesChange(e: Event) {
  const files = (e.target as HTMLInputElement).files
  if (!files) return
  newFiles.value.push(...Array.from(files))
}

function onDrop(e: DragEvent) {
  const files = e.dataTransfer?.files
  if (!files) return
  newFiles.value.push(...Array.from(files))
}

const { apiFetch } = useApi()

async function removeExistingFile(file: SubjectFile) {
  await apiFetch(`/admin/subject-files/${file.id}`, { method: 'DELETE' })
  existingFiles.value = existingFiles.value.filter(f => f.id !== file.id)
}

function getFormData(): FormData {
  const fd = new FormData()
  fd.append('name', form.name)
  fd.append('color', form.color)
  fd.append('order', String(form.order))
  fd.append('description', form.description || '')
  fd.append('is_active', form.is_active ? '1' : '0')
  if (imageFile.value) fd.append('image', imageFile.value)
  form.topics.forEach((t, i) => {
    if (t.id) fd.append(`topics[${i}][id]`, String(t.id))
    fd.append(`topics[${i}][name]`, t.name)
    fd.append(`topics[${i}][description]`, t.description || '')
    fd.append(`topics[${i}][order]`, String(t.order))
    fd.append(`topics[${i}][time_limit]`, t.time_limit ? String(t.time_limit) : '')
    fd.append(`topics[${i}][is_active]`, t.is_active ? '1' : '0')
  })
  newFiles.value.forEach(f => fd.append('files[]', f))
  return fd
}

function getFileIcon(type: string): string {
  if (type?.includes('pdf')) return 'i-heroicons-document-text'
  if (type?.includes('image')) return 'i-heroicons-photo'
  if (type?.includes('video')) return 'i-heroicons-video-camera'
  if (type?.includes('audio')) return 'i-heroicons-musical-note'
  return 'i-heroicons-document'
}

function formatSize(bytes: number): string {
  if (bytes < 1024) return `${bytes} B`
  if (bytes < 1048576) return `${(bytes / 1024).toFixed(1)} KB`
  return `${(bytes / 1048576).toFixed(1)} MB`
}
</script>

<style scoped>
.topic-list-enter-active,
.topic-list-leave-active {
  transition: all 0.25s ease;
}
.topic-list-enter-from,
.topic-list-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>