<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-950">

    <!-- Header -->
    <div class="border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 px-4 sm:px-6 py-4 sticky top-0 z-10">
      <div class="max-w-5xl mx-auto flex items-center gap-3">
        <UButton to="/books" icon="i-heroicons-arrow-left" color="gray" variant="ghost" size="sm" />
        <div class="flex-1 min-w-0">
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">Kitobni tahrirlash</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
            <span v-if="!pageLoading">{{ form.title || '—' }}</span>
            <USkeleton v-else class="h-3.5 w-40 inline-block align-middle" />
          </p>
        </div>
        <UBadge v-if="!pageLoading" :color="statusColor(form.status)" variant="subtle" size="sm">
          {{ statusLabel(form.status) }}
        </UBadge>
      </div>
    </div>

    <!-- Page skeleton -->
    <div v-if="pageLoading" class="max-w-5xl mx-auto px-4 sm:px-6 py-6">
      <div class="flex flex-col lg:flex-row gap-5">
        <div class="w-full lg:w-56 space-y-4">
          <USkeleton class="w-full aspect-[2/3] rounded-xl" />
          <USkeleton class="h-36 rounded-xl" />
        </div>
        <div class="flex-1 space-y-4">
          <USkeleton class="h-80 rounded-xl" />
          <USkeleton class="h-36 rounded-xl" />
        </div>
      </div>
    </div>

    <!-- Form -->
    <div v-else class="max-w-5xl mx-auto px-4 sm:px-6 py-6">
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
              <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">PDF fayl</span>
                <UBadge v-if="existingPdf && !pdfFile" color="green" variant="subtle" size="xs">Mavjud</UBadge>
              </div>
              <div class="p-4 space-y-3">
                <!-- Existing PDF info -->
                <div
                  v-if="existingPdf && !pdfFile"
                  class="flex items-center gap-2.5 p-2.5 rounded-lg bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700"
                >
                  <UIcon name="i-heroicons-document-text" class="text-red-500 flex-shrink-0 text-lg" />
                  <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate">{{ existingPdfName }}</p>
                    <p class="text-xs text-gray-400">Hozirgi fayl</p>
                  </div>
                </div>
                <div
                  class="border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg p-4 text-center bg-gray-50 dark:bg-gray-800/30 hover:border-primary-400 dark:hover:border-primary-500 hover:bg-primary-50/50 dark:hover:bg-primary-900/10 transition-all cursor-pointer"
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
                    <div class="w-9 h-9 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-1.5">
                      <UIcon name="i-heroicons-document-arrow-up" class="text-gray-400" />
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ existingPdf ? 'Yangi PDF (almashtirish)' : 'PDF yuklash uchun bosing' }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-600 mt-0.5">max 100MB</p>
                  </template>
                </div>
                <input ref="pdfInput" type="file" accept=".pdf" class="hidden" @change="onPdfChange" />
                <UButton
                  v-if="pdfFile"
                  size="xs" color="red" variant="soft" block
                  icon="i-heroicons-trash"
                  @click="removePdf"
                >
                  Yangi faylni bekor qilish
                </UButton>
              </div>
            </div>

            <!-- Statistika -->
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
              <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Statistika</span>
              </div>
              <div class="p-4 divide-y divide-gray-100 dark:divide-gray-800">
                <div class="flex items-center justify-between py-2.5 first:pt-0 last:pb-0">
                  <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <UIcon name="i-heroicons-star-solid" class="text-amber-400 flex-shrink-0" />
                    <span>Reyting</span>
                  </div>
                  <div class="text-right">
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ bookStats.average_rating }}</span>
                    <span class="text-xs text-gray-400 ml-1">({{ bookStats.review_count }})</span>
                  </div>
                </div>
                <div class="flex items-center justify-between py-2.5">
                  <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <UIcon name="i-heroicons-eye" class="text-blue-400 flex-shrink-0" />
                    <span>Ko'rishlar</span>
                  </div>
                  <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ bookStats.view_count?.toLocaleString() ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between py-2.5 last:pb-0">
                  <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <UIcon name="i-heroicons-arrow-down-tray" class="text-emerald-400 flex-shrink-0" />
                    <span>Yuklamalar</span>
                  </div>
                  <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ bookStats.download_count?.toLocaleString() ?? 0 }}</span>
                </div>
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

                <!-- Nomi + Muallif -->
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
              <div class="p-5 space-y-4">

                <!-- Narx — alohida -->
                <div class="space-y-1.5">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Narx <span class="text-red-500">*</span>
                  </label>
                  <UInput
                    v-model.number="form.price"
                    type="number"
                    min="0"
                    placeholder="0"
                    size="md"
                    :class="errors.price ? 'ring-1 ring-red-500 rounded-lg' : ''"
                  >
                    <template #trailing>
                      <span class="text-sm text-gray-500 dark:text-gray-400 font-medium select-none pr-1">so'm</span>
                    </template>
                  </UInput>
                  <p v-if="errors.price" class="text-xs text-red-500 flex items-center gap-1 mt-1">
                    <UIcon name="i-heroicons-exclamation-circle-mini" />{{ errors.price }}
                  </p>
                </div>

                <!-- Til + Status — 2 ustun -->
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

            <!-- Buttons -->
            <div class="flex items-center justify-between pb-4">
              <UButton
                icon="i-heroicons-trash"
                color="red"
                variant="soft"
                size="md"
                @click="confirmDelete"
              >
                Kitobni o'chirish
              </UButton>
              <div class="flex items-center gap-3">
                <UButton to="/books" color="gray" variant="ghost" size="md">Bekor qilish</UButton>
                <UButton
                  type="submit"
                  color="primary"
                  size="md"
                  icon="i-heroicons-check"
                  :loading="submitting"
                >
                  Saqlash
                </UButton>
              </div>
            </div>

          </div>
        </div>
      </form>
    </div>

    <!-- Delete Modal -->
    <Transition name="modal">
      <div
        v-if="deleteModal"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
        @click.self="deleteModal = false"
      >
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="deleteModal = false" />
        <div class="relative z-10 w-full max-w-md bg-white dark:bg-gray-900 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700">
          <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-500/10 flex items-center justify-center flex-shrink-0">
              <UIcon name="i-heroicons-trash" class="text-red-500 dark:text-red-400 text-lg" />
            </div>
            <div>
              <h3 class="font-semibold text-gray-900 dark:text-white">Kitobni o'chirish</h3>
              <p class="text-sm text-gray-500">Bu amalni ortga qaytarib bo'lmaydi</p>
            </div>
            <button class="ml-auto text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1" @click="deleteModal = false">
              <UIcon name="i-heroicons-x-mark" class="text-xl" />
            </button>
          </div>
          <div class="px-5 py-4">
            <p class="text-gray-600 dark:text-gray-300 text-sm">
              <span class="font-semibold text-gray-900 dark:text-white">{{ form.title }}</span> kitobini o'chirishni tasdiqlaysizmi?
            </p>
          </div>
          <div class="flex justify-end gap-2 px-5 py-4 border-t border-gray-200 dark:border-gray-700">
            <UButton color="gray" variant="ghost" @click="deleteModal = false">Bekor qilish</UButton>
            <UButton color="red" :loading="deleting" @click="deleteBook">O'chirish</UButton>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'admin' })

const { get, post, del } = useApi()
const toast = useToast()
const router = useRouter()
const route = useRoute()
const bookId = route.params.id

const coverInput = ref<HTMLInputElement | null>(null)
const pdfInput = ref<HTMLInputElement | null>(null)
const coverPreview = ref<string | null>(null)
const coverFile = ref<File | null>(null)
const pdfFile = ref<File | null>(null)
const existingPdf = ref<string | null>(null)
const pageLoading = ref(true)
const submitting = ref(false)
const deleting = ref(false)
const deleteModal = ref(false)
const errors = ref<Record<string, string>>({})

const bookStats = ref({
  average_rating: 0,
  review_count: 0,
  view_count: 0,
  download_count: 0,
})

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

const existingPdfName = computed(() => existingPdf.value?.split('/').pop() ?? 'kitob.pdf')

function statusLabel(s: string) {
  const map: Record<string, string> = { available: 'Mavjud', unavailable: 'Mavjud emas', coming_soon: 'Tez kunda' }
  return map[s] ?? s
}
function statusColor(s: string) {
  const map: Record<string, string> = { available: 'green', unavailable: 'red', coming_soon: 'yellow' }
  return (map[s] ?? 'gray') as any
}

onMounted(async () => {
  try {
    const data = await get<any>(`/books/${bookId}`)
    const b = data.data ?? data
    form.title = b.title ?? ''
    form.author = b.author ?? ''
    form.description = b.description ?? ''
    form.isbn = b.isbn ?? ''
    form.pages = b.pages ?? null
    form.price = Number(b.price) ?? 0
    form.language = b.language ?? 'uz'
    form.status = b.status ?? 'available'
    if (b.cover_image) coverPreview.value = b.cover_image
    if (b.pdf_file) existingPdf.value = b.pdf_file
    bookStats.value = {
      average_rating: b.average_rating ?? 0,
      review_count: b.review_count ?? 0,
      view_count: b.view_count ?? 0,
      download_count: b.download_count ?? 0,
    }
  } catch (e: any) {
    toast.add({ title: 'Xatolik', description: e?.data?.message ?? "Yuklashda xato", color: 'red' })
    router.push('/books')
  } finally {
    pageLoading.value = false
  }
})

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
    fd.append('_method', 'PUT')
    await post(`/admin/books/${bookId}`, fd)
    toast.add({ title: 'Muvaffaqiyat', description: "O'zgarishlar saqlandi", color: 'green' })
    router.push('/books')
  } catch (e: any) {
    const se = e?.data?.errors
    if (se) Object.keys(se).forEach(k => { errors.value[k] = se[k][0] })
    else toast.add({ title: 'Xatolik', description: e?.data?.message ?? 'Saqlashda xato', color: 'red' })
  } finally {
    submitting.value = false
  }
}

function confirmDelete() { deleteModal.value = true }

async function deleteBook() {
  deleting.value = true
  try {
    await del(`/admin/books/${bookId}`)
    toast.add({ title: 'Muvaffaqiyat', description: "Kitob o'chirildi", color: 'green' })
    router.push('/books')
  } catch (e: any) {
    toast.add({ title: 'Xatolik', description: e?.data?.message ?? "O'chirishda xato", color: 'red' })
  } finally {
    deleting.value = false
    deleteModal.value = false
  }
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>