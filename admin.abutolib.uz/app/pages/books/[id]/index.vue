<template>
  <div class="min-h-screen bg-gray-950 text-white">
    <!-- Header -->
    <div class="border-b border-gray-800 bg-gray-900/60 backdrop-blur px-6 py-4">
      <div class="max-w-5xl mx-auto flex items-center justify-between">
        <div class="flex items-center gap-4">
          <UButton to="/admin/books" icon="i-heroicons-arrow-left" color="gray" variant="ghost" size="sm" />
          <div>
            <h1 class="text-xl font-bold tracking-tight text-white line-clamp-1">
              {{ book?.title ?? '...' }}
            </h1>
            <p class="text-sm text-gray-400 mt-0.5">{{ book?.author }}</p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <UBadge
            v-if="book"
            :color="book.status === 'active' ? 'green' : book.status === 'inactive' ? 'red' : 'yellow'"
            variant="subtle"
            size="md"
          >
            {{ statusLabel(book.status) }}
          </UBadge>
          <UButton
            :to="`/admin/books/${route.params.id}/edit`"
            icon="i-heroicons-pencil-square"
            color="blue"
            size="sm"
          >
            Tahrirlash
          </UButton>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="max-w-5xl mx-auto px-6 py-20 flex justify-center">
      <UIcon name="i-heroicons-arrow-path" class="text-4xl text-gray-500 animate-spin" />
    </div>

    <div v-else-if="book" class="max-w-5xl mx-auto px-6 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="space-y-5">
          <!-- Cover -->
          <UCard class="bg-gray-900 border border-gray-800" :ui="{ body: { padding: 'p-0' } }">
            <div class="aspect-[3/4] relative overflow-hidden rounded-xl">
              <img
                v-if="book.cover_image"
                :src="book.cover_image"
                :alt="book.title"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full bg-gray-800 flex items-center justify-center">
                <UIcon name="i-heroicons-book-open" class="text-gray-600 text-6xl" />
              </div>
            </div>
          </UCard>

          <!-- Quick Stats -->
          <UCard class="bg-gray-900 border border-gray-800">
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-400 flex items-center gap-1.5">
                  <UIcon name="i-heroicons-eye" class="text-blue-400" /> Ko'rishlar
                </span>
                <span class="font-bold text-white">{{ book.view_count?.toLocaleString() ?? 0 }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-400 flex items-center gap-1.5">
                  <UIcon name="i-heroicons-arrow-down-tray" class="text-purple-400" /> Yuklamalar
                </span>
                <span class="font-bold text-white">{{ book.download_count?.toLocaleString() ?? 0 }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-400 flex items-center gap-1.5">
                  <UIcon name="i-heroicons-star" class="text-amber-400" /> Reyting
                </span>
                <span class="font-bold text-amber-400">{{ book.average_rating }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-400 flex items-center gap-1.5">
                  <UIcon name="i-heroicons-chat-bubble-left" class="text-green-400" /> Sharhlar
                </span>
                <span class="font-bold text-white">{{ book.review_count }}</span>
              </div>
            </div>
          </UCard>

          <!-- PDF File -->
          <UCard v-if="book.pdf_file" class="bg-gray-900 border border-gray-800">
            <template #header>
              <h3 class="font-semibold text-white text-sm">PDF Fayl</h3>
            </template>
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-red-500/10 rounded-lg flex items-center justify-center">
                <UIcon name="i-heroicons-document" class="text-red-400 text-xl" />
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm text-gray-300 truncate">PDF fayl mavjud</p>
              </div>
              <UButton :to="book.pdf_file" target="_blank" size="xs" color="blue" icon="i-heroicons-eye">
                Ko'rish
              </UButton>
            </div>
          </UCard>
        </div>

        <!-- Right Column -->
        <div class="lg:col-span-2 space-y-5">
          <!-- Main Info Card -->
          <UCard class="bg-gray-900 border border-gray-800">
            <template #header>
              <h2 class="font-semibold text-white flex items-center gap-2">
                <UIcon name="i-heroicons-information-circle" class="text-blue-400" />
                Asosiy ma'lumotlar
              </h2>
            </template>

            <dl class="grid grid-cols-2 gap-x-6 gap-y-4">
              <div>
                <dt class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Muallif</dt>
                <dd class="mt-1 text-white font-medium">{{ book.author }}</dd>
              </div>
              <div>
                <dt class="text-xs uppercase tracking-wider text-gray-500 font-semibold">ISBN</dt>
                <dd class="mt-1 text-white font-mono">{{ book.isbn || '—' }}</dd>
              </div>
              <div>
                <dt class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Narx</dt>
                <dd class="mt-1 text-emerald-400 font-semibold text-lg">
                  {{ book.price > 0 ? `${Number(book.price).toLocaleString()} so'm` : 'Bepul' }}
                </dd>
              </div>
              <div>
                <dt class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Sahifalar</dt>
                <dd class="mt-1 text-white">{{ book.pages || '—' }}</dd>
              </div>
              <div>
                <dt class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Til</dt>
                <dd class="mt-1 text-white">{{ langLabel(book.language) }}</dd>
              </div>
              <div>
                <dt class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Status</dt>
                <dd class="mt-1">
                  <UBadge
                    :color="book.status === 'active' ? 'green' : book.status === 'inactive' ? 'red' : 'yellow'"
                    variant="subtle"
                  >
                    {{ statusLabel(book.status) }}
                  </UBadge>
                </dd>
              </div>
              <div>
                <dt class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Yaratilgan</dt>
                <dd class="mt-1 text-gray-300 text-sm">{{ formatDate(book.created_at) }}</dd>
              </div>
              <div>
                <dt class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Yangilangan</dt>
                <dd class="mt-1 text-gray-300 text-sm">{{ formatDate(book.updated_at) }}</dd>
              </div>
            </dl>
          </UCard>

          <!-- Description -->
          <UCard v-if="book.description" class="bg-gray-900 border border-gray-800">
            <template #header>
              <h2 class="font-semibold text-white flex items-center gap-2">
                <UIcon name="i-heroicons-document-text" class="text-gray-400" />
                Tavsif
              </h2>
            </template>
            <p class="text-gray-300 leading-relaxed whitespace-pre-wrap">{{ book.description }}</p>
          </UCard>

          <!-- Reviews Table -->
          <UCard class="bg-gray-900 border border-gray-800">
            <template #header>
              <div class="flex items-center justify-between">
                <h2 class="font-semibold text-white flex items-center gap-2">
                  <UIcon name="i-heroicons-chat-bubble-left-right" class="text-green-400" />
                  So'nggi sharhlar
                </h2>
                <UBadge color="gray" variant="solid">{{ book.review_count }} ta</UBadge>
              </div>
            </template>

            <div v-if="reviews.length === 0" class="py-8 text-center text-gray-500">
              <UIcon name="i-heroicons-chat-bubble-left" class="text-3xl mb-2" />
              <p class="text-sm">Hali sharhlar yo'q</p>
            </div>

            <div v-else class="space-y-3">
              <div
                v-for="review in reviews"
                :key="review.id"
                class="p-3 bg-gray-800/40 rounded-lg"
              >
                <div class="flex items-center justify-between mb-1">
                  <p class="text-sm font-medium text-white">{{ review.user?.name ?? 'Foydalanuvchi' }}</p>
                  <div class="flex items-center gap-1">
                    <UIcon name="i-heroicons-star-solid" class="text-amber-400 text-xs" />
                    <span class="text-xs text-amber-400 font-medium">{{ review.rating }}</span>
                  </div>
                </div>
                <p class="text-sm text-gray-400 line-clamp-2">{{ review.comment }}</p>
                <p class="text-xs text-gray-600 mt-1">{{ formatDate(review.created_at) }}</p>
              </div>
            </div>
          </UCard>

          <!-- Actions -->
          <div class="flex gap-3">
            <UButton
              :to="`/admin/books/${route.params.id}/edit`"
              icon="i-heroicons-pencil-square"
              color="blue"
              class="flex-1"
            >
              Tahrirlash
            </UButton>
            <UButton
              icon="i-heroicons-trash"
              color="red"
              variant="outline"
              @click="deleteModal = true"
            >
              O'chirish
            </UButton>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Modal -->
    <UModal v-model="deleteModal">
      <UCard class="bg-gray-900 border border-gray-800">
        <template #header>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-red-500/10 flex items-center justify-center">
              <UIcon name="i-heroicons-trash" class="text-red-400 text-lg" />
            </div>
            <div>
              <h3 class="font-semibold text-white">Kitobni o'chirish</h3>
              <p class="text-sm text-gray-400">Bu amalni qaytarib bo'lmaydi</p>
            </div>
          </div>
        </template>
        <p class="text-gray-300">
          <span class="font-semibold text-white">{{ book?.title }}</span> kitobini o'chirmoqchimisiz?
        </p>
        <template #footer>
          <div class="flex justify-end gap-2">
            <UButton color="gray" variant="ghost" @click="deleteModal = false">Bekor qilish</UButton>
            <UButton color="red" :loading="deleting" @click="deleteBook">O'chirish</UButton>
          </div>
        </template>
      </UCard>
    </UModal>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'admin', middleware: 'auth' })

const route = useRoute()
const api = useApi()
const toast = useToast()
const router = useRouter()

const book = ref<any>(null)
const reviews = ref<any[]>([])
const loading = ref(true)
const deleteModal = ref(false)
const deleting = ref(false)

function statusLabel(s: string) {
  return { active: 'Faol', inactive: 'Nofaol', pending: 'Kutilmoqda' }[s] ?? s
}

function langLabel(l: string) {
  return { uz: "O'zbek", ru: 'Русский', en: 'English' }[l] ?? l
}

function formatDate(date: string) {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('uz-UZ', { year: 'numeric', month: 'long', day: 'numeric' })
}

async function fetchBook() {
  loading.value = true
  try {
    const data = await api.get<any>(`/api/admin/books/${route.params.id}`)
    book.value = data.data ?? data
    reviews.value = book.value.reviews?.slice(0, 5) ?? []
  } catch {
    toast.add({ title: 'Xatolik', description: 'Kitob topilmadi', color: 'red' })
    router.push('/admin/books')
  } finally {
    loading.value = false
  }
}

async function deleteBook() {
  deleting.value = true
  try {
    await api.del(`/api/admin/books/${route.params.id}`)
    toast.add({ title: 'Muvaffaqiyat', description: 'Kitob o\'chirildi', color: 'green' })
    router.push('/admin/books')
  } catch (e: any) {
    toast.add({ title: 'Xatolik', description: e?.data?.message ?? "O'chirishda xato", color: 'red' })
  } finally {
    deleting.value = false
  }
}

onMounted(fetchBook)
</script>