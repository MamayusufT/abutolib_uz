<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-950">

    <!-- Header -->
    <div class="border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 px-4 sm:px-6 py-4 sticky top-0 z-10">
      <div class="max-w-5xl mx-auto flex items-center gap-3">
        <UButton to="/books" icon="i-heroicons-arrow-left" color="gray" variant="ghost" size="sm" />
        <div>
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">Kitob qo'shish</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400">Yangi kitob ma'lumotlarini kiriting</p>
        </div>
      </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6">
      <form @submit.prevent="submitForm">
        <div class="flex flex-col lg:flex-row gap-5 items-start">

          <!-- ═══ LEFT COLUMN ═══ -->
          <div class="w-full lg:w-56 flex-shrink-0 space-y-4">

            <!-- Muqova -->
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
              <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Muqova rasmi</span>
              </div>
              <div class="p-4">
                <div
                  class="relative group cursor-pointer"
                  @click="triggerCoverInput"
                  @dragover.prevent
                  @drop.prevent="onCoverDrop"
                >
                  <div v-if="coverPreview" class="w-full aspect-[2/3] rounded-lg overflow-hidden shadow-sm">
                    <img :src="coverPreview" alt="Muqova" class="w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-1.5 rounded-lg">
                      <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center">
                        <UIcon name="i-heroicons-camera" class="text-white text-lg" />
                      </div>
                      <span class="text-white text-xs font-medium">Almashtirish</span>
                    </div>
                  </div>
                  <div
                    v-else
                    class="w-full aspect-[2/3] rounded-lg border-2 border-dashed border-gray-200 dark:border-gray-700 flex flex-col items-center justify-center gap-2.5 bg-gray-50 dark:bg-gray-800/30 hover:border-primary-400 dark:hover:border-primary-500 hover:bg-primary-50/50 dark:hover:bg-primary-900/10 transition-all"
                  >
                    <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                      <UIcon name="i-heroicons-photo" class="text-2xl text-gray-400 dark:text-gray-500" />
                    </div>
                    <div class="text-center px-3 space-y-0.5">
                      <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Bosing yoki sudrab keling</p>
                      <p class="text-xs text-gray-400 dark:text-gray-600">JPG, PNG · max 2MB</p>
                    </div>
                  </div>
                </div>
                <input ref="coverInput" type="file" accept="image/*" class="hidden" @change="onCoverChange" />
                <UButton
                  v-if="coverPreview"
                  size="xs" color="red" variant="soft" block class="mt-3"
                  icon="i-heroicons-trash"
                  @click.stop="removeCover"
                >
                  Rasmni o'chirish
                </UButton>
              </div>
            </div>

            <!-- PDF -->
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
              <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">PDF fayl</span>
              </div>
              <div class="p-4">
                <div
                  class="border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg p-5 text-center bg-gray-50 dark:bg-gray-800/30 hover:border-primary-400 dark:hover:border-primary-500 hover:bg-primary-50/50 dark:hover:bg-primary-900/10 transition-all cursor-pointer"
                  @click="triggerPdfInput"
                  @dragover.prevent
                  @drop.prevent="onPdfDrop"
                >
                  <template v-if="pdfFile">
                    <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-500/10 flex items-center justify-center mx-auto mb-2">
                      <UIcon name="i-heroicons-document-text" class="text-red-500 text-lg" />
                    </div>
                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 break-all line-clamp-2">{{ pdfFile.name }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ formatFileSize(pdfFile.size) }}</p>
                  </template>
                  <template v-else>
                    <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-2">
                      <UIcon name="i-heroicons-document-arrow-up" class="text-gray-400 text-xl" />
                    </div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">PDF yuklash uchun bosing</p>
                    <p class="text-xs text-gray-400 dark:text-gray-600 mt-0.5">max 100MB</p>
                  </template>
                </div>
                <input ref="pdfInput" type="file" accept=".pdf" class="hidden" @change="onPdfChange" />
                <UButton
                  v-if="pdfFile"
                  size="xs" color="red" variant="soft" block class="mt-3"
                  icon="i-heroicons-trash"
                  @click="removePdf"
                >
                  Faylni o'chirish
                </UButton>
              </div>
            </div>

          </div>

          <!-- ═══ RIGHT COLUMN ═══ -->
          <div class="flex-1 min-w-0 space-y-4">

            <!-- Asosiy ma'lumotlar -->
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
              <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800">
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Asosiy ma'lumotlar</span>
              </div>
              <div class="p-5 space-y-5">

                <!-- Kitob nomi + Muallif -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      Kitob nomi <span class="text-red-500">*</span>
                    </label>
                    <UInput
                      v-model="form.title"
                      placeholder="Masalan: O'tkan kunlar"
                      size="md"
                      :class="errors.title ? 'ring-1 ring-red-500 rounded-lg' : ''"
                    />
                    <p v-if="errors.title" class="text-xs text-red-500 flex items-center gap-1 mt-1">
                      <UIcon name="i-heroicons-exclamation-circle-mini" />{{ errors.title }}
                    </p>
                  </div>
                  <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      Muallif <span class="text-red-500">*</span>
                    </label>
                    <UInput
                      v-model="form.author"
                      placeholder="Masalan: Abdulla Qodiriy"
                      size="md"
                      :class="errors.author ? 'ring-1 ring-red-500 rounded-lg' : ''"
                    />
                    <p v-if="errors.author" class="text-xs text-red-500 flex items-center gap-1 mt-1">
                      <UIcon name="i-heroicons-exclamation-circle-mini" />{{ errors.author }}
                    </p>
                  </div>
                </div>

                <!-- Tavsif — RichEditor -->
                <div class="space-y-1.5">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Tavsif <span class="text-red-500">*</span>
                  </label>
                  <RichEditor
                    v-model="form.description"
                    placeholder="Kitob haqida qisqacha ma'lumot..."
                    class="min-h-[160px]"
                    :class="errors.description ? 'ring-1 ring-red-500 rounded-lg' : ''"
                  />
                  <p v-if="errors.description" class="text-xs text-red-500 flex items-center gap-1 mt-1">
                    <UIcon name="i-heroicons-exclamation-circle-mini" />{{ errors.description }}
                  </p>
                </div>

                <!-- ISBN + Sahifalar -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      ISBN <span class="text-red-500">*</span>
                    </label>
                    <UInput
                      v-model="form.isbn"
                      placeholder="978-x-xxx-xxxxx-x"
                      size="md"
                      :class="errors.isbn ? 'ring-1 ring-red-500 rounded-lg' : ''"
                    />
                    <p v-if="errors.isbn" class="text-xs text-red-500 flex items-center gap-1 mt-1">
                      <UIcon name="i-heroicons-exclamation-circle-mini" />{{ errors.isbn }}
                    </p>
                  </div>
                  <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      Sahifalar soni <span class="text-red-500">*</span>
                    </label>
                    <UInput
                      v-model.number="form.pages"
                      type="number"
                      min="1"
                      placeholder="Masalan: 320"
                      size="md"
                      :class="errors.pages ? 'ring-1 ring-red-500 rounded-lg' : ''"
                    />
                    <p v-if="errors.pages" class="text-xs text-red-500 flex items-center gap-1 mt-1">
                      <UIcon name="i-heroicons-exclamation-circle-mini" />{{ errors.pages }}
                    </p>
                  </div>
                </div>

              </div>
            </div>

            <!-- Narx va holat -->
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
              <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800">
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Narx va holat</span>
              </div>
              <div class="p-5">
                <!-- Narx alohida, to'liq kenglikda -->
                <div class="space-y-1.5 mb-4">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Narx <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <UInput
                      v-model.number="form.price"
                      type="number"
                      min="0"
                      placeholder="0"
                      size="md"
                      class="w-full"
                      :class="errors.price ? 'ring-1 ring-red-500 rounded-lg' : ''"
                    >
                      <template #trailing>
                        <span class="text-sm text-gray-500 dark:text-gray-400 font-medium select-none pr-1">so'm</span>
                      </template>
                    </UInput>
                  </div>
                  <p v-if="errors.price" class="text-xs text-red-500 flex items-center gap-1 mt-1">
                    <UIcon name="i-heroicons-exclamation-circle-mini" />{{ errors.price }}
                  </p>
                </div>

                <!-- Til + Status — 2 ta ustun -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      Til <span class="text-red-500">*</span>
                    </label>
                    <USelect
                      v-model="form.language"
                      :options="languageOptions"
                      size="md"
                      class="w-full"
                    />
                  </div>
                  <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      Status <span class="text-red-500">*</span>
                    </label>
                    <USelect
                      v-model="form.status"
                      :options="statusOptions"
                      size="md"
                      class="w-full"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pb-4">
              <UButton to="/books" color="gray" variant="ghost" size="md">
                Bekor qilish
              </UButton>
              <UButton
                type="submit"
                color="primary"
                size="md"
                icon="i-heroicons-plus"
                :loading="submitting"
              >
                Kitob qo'shish
              </UButton>
            </div>

          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'admin' })

const { post } = useApi()
const toast = useToast()
const router = useRouter()

const coverInput = ref<HTMLInputElement | null>(null)
const pdfInput = ref<HTMLInputElement | null>(null)
const coverPreview = ref<string | null>(null)
const coverFile = ref<File | null>(null)
const pdfFile = ref<File | null>(null)
const submitting = ref(false)
const errors = ref<Record<string, string>>({})

const form = reactive({
  title: '',
  author: '',
  description: '',
  isbn: '',
  pages: null as number | null,
  price: 0,
  language: 'uz',
  status: 'available',
})

const languageOptions = [
  { label: "O'zbek", value: 'uz' },
  { label: 'Русский', value: 'ru' },
  { label: 'English', value: 'en' },
]

const statusOptions = [
  { label: 'Mavjud', value: 'available' },
  { label: 'Mavjud emas', value: 'unavailable' },
  { label: 'Tez kunda', value: 'coming_soon' },
]

function triggerCoverInput() { coverInput.value?.click() }
function triggerPdfInput() { pdfInput.value?.click() }

function onCoverChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (file) setCover(file)
}
function onCoverDrop(e: DragEvent) {
  const file = e.dataTransfer?.files?.[0]
  if (file?.type.startsWith('image/')) setCover(file)
}
function setCover(file: File) {
  coverFile.value = file
  const r = new FileReader()
  r.onload = (ev) => { coverPreview.value = ev.target?.result as string }
  r.readAsDataURL(file)
}
function removeCover() {
  coverFile.value = null
  coverPreview.value = null
  if (coverInput.value) coverInput.value.value = ''
}
function onPdfChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (file) pdfFile.value = file
}
function onPdfDrop(e: DragEvent) {
  const file = e.dataTransfer?.files?.[0]
  if (file?.type === 'application/pdf') pdfFile.value = file
}
function removePdf() {
  pdfFile.value = null
  if (pdfInput.value) pdfInput.value.value = ''
}
function formatFileSize(bytes: number) {
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`
  return `${(bytes / (1024 * 1024)).toFixed(1)} MB`
}

function validate() {
  errors.value = {}
  if (!form.title.trim()) errors.value.title = 'Kitob nomi kiritilishi shart'
  if (!form.author.trim()) errors.value.author = 'Muallif kiritilishi shart'
  if (!form.description.trim()) errors.value.description = 'Tavsif kiritilishi shart'
  if (!form.isbn.trim()) errors.value.isbn = 'ISBN kiritilishi shart'
  if (!form.pages || form.pages < 1) errors.value.pages = 'Sahifalar soni kiritilishi shart'
  if (form.price < 0) errors.value.price = "Narx 0 dan kam bo'lmasligi kerak"
  return Object.keys(errors.value).length === 0
}

async function submitForm() {
  if (!validate()) return
  submitting.value = true
  try {
    const fd = new FormData()
    Object.entries(form).forEach(([k, v]) => {
      if (v !== null && v !== undefined) fd.append(k, String(v))
    })
    if (coverFile.value) fd.append('cover_image', coverFile.value)
    if (pdfFile.value) fd.append('pdf_file', pdfFile.value)
    await post('/admin/books', fd)
    toast.add({ title: 'Muvaffaqiyat', description: "Kitob muvaffaqiyatli qo'shildi", color: 'green' })
    router.push('/books')
  } catch (e: any) {
    const se = e?.data?.errors
    if (se) Object.keys(se).forEach(k => { errors.value[k] = se[k][0] })
    else toast.add({ title: 'Xatolik', description: e?.data?.message ?? 'Saqlashda xato', color: 'red' })
  } finally {
    submitting.value = false
  }
}
</script>