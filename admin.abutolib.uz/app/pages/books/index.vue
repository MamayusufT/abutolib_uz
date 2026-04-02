<template>
  <div class="min-h-screen bg-white dark:bg-gray-950">
    <!-- Header -->
    <div class="border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 px-6 py-4">
      <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Kitoblar</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Barcha kitoblarni boshqarish</p>
        </div>
        <UButton to="/books/create" icon="i-heroicons-plus" color="primary" size="md">
          Kitob qo'shish
        </UButton>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-8">
      <!-- Filters -->
      <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <UInput
          v-model="search"
          placeholder="Kitob nomi, muallif yoki ISBN..."
          icon="i-heroicons-magnifying-glass"
          class="flex-1"
          @input="debouncedFetch"
        />
        <USelect
          v-model="statusFilter"
          :options="statusOptions"
          placeholder="Status"
          class="w-44"
          @change="fetchBooks"
        />
      </div>

      <!-- Table Card -->
      <UCard
        class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800"
        :ui="{ body: { padding: 'p-0' } }"
      >
        <!-- Loading -->
        <div v-if="loading" class="flex justify-center items-center py-16">
          <UIcon name="i-heroicons-arrow-path" class="text-3xl text-gray-400 animate-spin" />
        </div>

        <!-- Empty -->
        <div v-else-if="books.length === 0" class="flex flex-col items-center justify-center py-16 text-gray-400">
          <UIcon name="i-heroicons-book-open" class="text-4xl mb-2" />
          <p class="text-sm">Kitoblar topilmadi</p>
        </div>

        <!-- Table -->
        <div v-else class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 w-14">Rasm</th>
                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Kitob</th>
                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">ISBN</th>
                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Narx</th>
                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Sahifalar</th>
                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Reyting</th>
                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Statistika</th>
                <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Amallar</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
              <tr
                v-for="book in books"
                :key="book.id"
                class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors"
              >
                <!-- Cover -->
                <td class="px-4 py-3">
                  <div class="w-9 h-12 rounded overflow-hidden bg-gray-100 dark:bg-gray-800 flex items-center justify-center flex-shrink-0">
                    <img v-if="book.cover_image" :src="book.cover_image" :alt="book.title" class="w-full h-full object-cover" />
                    <UIcon v-else name="i-heroicons-book-open" class="text-gray-400 dark:text-gray-600" />
                  </div>
                </td>

                <!-- Title + Author -->
                <td class="px-4 py-3 max-w-xs">
                  <p class="font-semibold text-gray-900 dark:text-white line-clamp-1">{{ book.title }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ book.author }}</p>
                </td>

                <!-- ISBN -->
                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-mono text-xs">
                  {{ book.isbn || '—' }}
                </td>

                <!-- Price -->
                <td class="px-4 py-3">
                  <span class="font-mono text-emerald-600 dark:text-emerald-400">
                    {{ Number(book.price) > 0 ? `${Number(book.price).toLocaleString()} so'm` : 'Bepul' }}
                  </span>
                </td>

                <!-- Pages -->
                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                  {{ book.pages || '—' }}
                </td>

                <!-- Rating -->
                <td class="px-4 py-3">
                  <div class="flex items-center gap-1">
                    <UIcon name="i-heroicons-star-solid" class="text-amber-400 text-xs" />
                    <span class="text-gray-700 dark:text-gray-300 font-medium">{{ book.average_rating }}</span>
                    <span class="text-xs text-gray-400">({{ book.review_count }})</span>
                  </div>
                </td>

                <!-- Status -->
                <td class="px-4 py-3">
                  <UBadge :color="statusColor(book.status)" variant="subtle" size="sm">
                    {{ statusLabel(book.status) }}
                  </UBadge>
                </td>

                <!-- Stats -->
                <td class="px-4 py-3">
                  <div class="flex gap-3 text-xs text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1">
                      <UIcon name="i-heroicons-eye" />
                      {{ book.view_count?.toLocaleString() ?? 0 }}
                    </span>
                    <span class="flex items-center gap-1">
                      <UIcon name="i-heroicons-arrow-down-tray" />
                      {{ book.download_count?.toLocaleString() ?? 0 }}
                    </span>
                  </div>
                </td>

                <!-- Actions -->
                <td class="px-4 py-3">
                  <div class="flex items-center justify-end gap-1">
                    <UButton
                      :to="`/books/${book.id}`"
                      icon="i-heroicons-eye"
                      size="xs"
                      color="gray"
                      variant="ghost"
                    />
                    <UButton
                      :to="`/books/${book.id}/edit`"
                      icon="i-heroicons-pencil-square"
                      size="xs"
                      color="blue"
                      variant="ghost"
                    />
                    <UButton
                      icon="i-heroicons-trash"
                      size="xs"
                      color="red"
                      variant="ghost"
                      @click="confirmDelete(book)"
                    />
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between px-4 py-3 border-t border-gray-200 dark:border-gray-800">
          <p class="text-sm text-gray-500 dark:text-gray-400">
            Jami <span class="font-semibold text-gray-900 dark:text-white">{{ pagination.total }}</span> ta kitob
          </p>
          <UPagination
            v-model="pagination.page"
            :page-count="pagination.perPage"
            :total="pagination.total"
            @update:model-value="fetchBooks"
          />
        </div>
      </UCard>
    </div>

    <!-- Delete Modal — custom fixed overlay -->
    <Transition name="modal">
      <div
        v-if="deleteModal"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
        @click.self="deleteModal = false"
      >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/60" @click="deleteModal = false" />

        <!-- Dialog -->
        <div class="relative z-10 w-full max-w-md bg-white dark:bg-gray-900 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700">
          <!-- Header -->
          <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-500/10 flex items-center justify-center flex-shrink-0">
              <UIcon name="i-heroicons-trash" class="text-red-500 dark:text-red-400 text-lg" />
            </div>
            <div>
              <h3 class="font-semibold text-gray-900 dark:text-white">Kitobni o'chirish</h3>
              <p class="text-sm text-gray-500 dark:text-gray-400">Bu amalni ortga qaytarib bo'lmaydi</p>
            </div>
            <button class="ml-auto text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" @click="deleteModal = false">
              <UIcon name="i-heroicons-x-mark" class="text-xl" />
            </button>
          </div>

          <!-- Body -->
          <div class="px-5 py-4">
            <p class="text-gray-600 dark:text-gray-300">
              <span class="font-semibold text-gray-900 dark:text-white">{{ deleteTarget?.title }}</span> kitobini o'chirmoqchimisiz?
            </p>
          </div>

          <!-- Footer -->
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
import { useDebounceFn } from '@vueuse/core'

definePageMeta({ layout: 'admin' })

const { get, del } = useApi()
const toast = useToast()

const books = ref<any[]>([])
const loading = ref(false)
const search = ref('')
const statusFilter = ref('')
const deleteModal = ref(false)
const deleteTarget = ref<any>(null)
const deleting = ref(false)
const pagination = ref({ page: 1, perPage: 15, total: 0 })

const statusOptions = [
  { label: 'Barchasi', value: '' },
  { label: 'Mavjud', value: 'available' },
  { label: 'Faol', value: 'active' },
  { label: 'Nofaol', value: 'inactive' },
  { label: 'Kutilmoqda', value: 'pending' },
]

function statusLabel(s: string) {
  const map: Record<string, string> = {
    available: 'Mavjud',
    active: 'Faol',
    inactive: 'Nofaol',
    pending: 'Kutilmoqda',
  }
  return map[s] ?? s
}

function statusColor(s: string) {
  const map: Record<string, string> = {
    available: 'green',
    active: 'green',
    inactive: 'red',
    pending: 'yellow',
  }
  return (map[s] ?? 'gray') as any
}

async function fetchBooks() {
  loading.value = true
  try {
    const data = await get<any>('/books', {
      page: pagination.value.page,
      per_page: pagination.value.perPage,
      search: search.value || undefined,
      status: statusFilter.value || undefined,
    })
    books.value = data.data
    pagination.value.total = data.meta?.total ?? data.total ?? 0
  } catch (e: any) {
    toast.add({ title: 'Xatolik', description: e?.data?.message ?? 'Kitoblarni yuklashda xato', color: 'red' })
  } finally {
    loading.value = false
  }
}

const debouncedFetch = useDebounceFn(fetchBooks, 400)

function confirmDelete(book: any) {
  deleteTarget.value = book
  deleteModal.value = true
}

async function deleteBook() {
  if (!deleteTarget.value) return
  deleting.value = true
  try {
    await del(`/admin/books/${deleteTarget.value.id}`)
    toast.add({ title: 'Muvaffaqiyat', description: "Kitob o'chirildi", color: 'green' })
    deleteModal.value = false
    fetchBooks()
  } catch (e: any) {
    toast.add({ title: 'Xatolik', description: e?.data?.message ?? "O'chirishda xato", color: 'red' })
  } finally {
    deleting.value = false
  }
}

onMounted(fetchBooks)
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