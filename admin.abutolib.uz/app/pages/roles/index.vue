<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Rollar</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Spatie Permission orqali boshqaruv</p>
      </div>
      <UButton
        to="/roles/create"
        icon="i-heroicons-plus"
        size="md"
        color="primary"
        label="Yangi rol"
      />
    </div>

    <UCard :ui="{ body: { padding: '' } }">
      <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row gap-3">
        <UInput
          v-model="search"
          icon="i-heroicons-magnifying-glass"
          placeholder="Rol qidirish..."
          class="w-full sm:max-w-xs"
          @update:model-value="onSearch"
        />
        <div class="ml-auto text-sm text-gray-500 dark:text-gray-400 self-center">
          Jami: <span class="font-semibold text-gray-700 dark:text-gray-200">{{ total }}</span> ta rol
        </div>
      </div>

      <div v-if="loading" class="flex justify-center py-12">
        <UIcon name="i-heroicons-arrow-path" class="animate-spin w-6 h-6 text-gray-400" />
      </div>

      <div v-else-if="roles.length === 0" class="flex flex-col items-center justify-center py-12 gap-2">
        <UIcon name="i-heroicons-shield-check" class="w-10 h-10 text-gray-300 dark:text-gray-600" />
        <p class="text-sm text-gray-400">Rol topilmadi</p>
      </div>

      <table v-else class="w-full text-sm">
        <thead>
          <tr class="border-b border-gray-200 dark:border-gray-700">
            <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Rol nomi</th>
            <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Guard</th>
            <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ruxsatlar</th>
            <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Yaratilgan</th>
            <th class="px-4 py-3" />
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          <tr
            v-for="row in roles"
            :key="row.id"
            class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors"
          >
            <td class="px-4 py-3">
              <div class="flex items-center gap-2">
                <UIcon name="i-heroicons-shield-check" class="text-primary-500 w-4 h-4 shrink-0" />
                <span class="font-medium text-gray-900 dark:text-white">{{ row.name }}</span>
              </div>
            </td>
            <td class="px-4 py-3">
              <UBadge :label="row.guard_name" color="gray" variant="soft" size="xs" />
            </td>
            <td class="px-4 py-3">
              <div class="flex flex-wrap gap-1">
                <span v-if="row.permissions.length === 0" class="text-xs text-gray-400 dark:text-gray-500">
                  Ruxsat yo'q
                </span>
                <template v-else>
                  <UBadge
                    v-for="perm in row.permissions.slice(0, 4)"
                    :key="perm"
                    :label="perm"
                    color="primary"
                    variant="soft"
                    size="xs"
                  />
                  <UBadge
                    v-if="row.permissions.length > 4"
                    :label="`+${row.permissions.length - 4}`"
                    color="gray"
                    variant="soft"
                    size="xs"
                  />
                </template>
              </div>
            </td>
            <td class="px-4 py-3">
              <span class="text-gray-500 dark:text-gray-400">{{ formatDate(row.created_at) }}</span>
            </td>
            <td class="px-4 py-3">
              <div class="flex items-center gap-1 justify-end">
                <UButton
                  :to="`/roles/${row.id}`"
                  icon="i-heroicons-pencil-square"
                  size="xs"
                  color="gray"
                  variant="ghost"
                />
                <UButton
                  icon="i-heroicons-trash"
                  size="xs"
                  color="red"
                  variant="ghost"
                  @click="confirmDelete(row)"
                />
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 flex justify-end">
        <UPagination
          v-model="page"
          :total="total"
          :page-count="perPage"
          @update:model-value="loadRoles"
        />
      </div>
    </UCard>

    <Transition name="fade">
      <div
        v-if="deleteModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40"
        @click.self="deleteModal = false"
      >
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xl w-full max-w-md">
          <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100 dark:border-gray-800">
            <div class="p-2 rounded-full bg-red-50 dark:bg-red-950 shrink-0">
              <UIcon name="i-heroicons-exclamation-triangle" class="text-red-500 w-5 h-5" />
            </div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Rolni o'chirish</h3>
            <button
              class="ml-auto text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors"
              @click="deleteModal = false"
            >
              <UIcon name="i-heroicons-x-mark" class="w-5 h-5" />
            </button>
          </div>
          <div class="px-5 py-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">
              <span class="font-semibold text-gray-900 dark:text-white">{{ deletingRole?.name }}</span>
              rolini o'chirmoqchimisiz? Bu amalni qaytarib bo'lmaydi.
            </p>
          </div>
          <div class="flex justify-end gap-3 px-5 py-4 border-t border-gray-100 dark:border-gray-800">
            <UButton label="Bekor qilish" color="gray" variant="ghost" @click="deleteModal = false" />
            <UButton
              label="O'chirish"
              color="red"
              :loading="deleteLoading"
              @click="deleteRole"
            />
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'admin' })

const toast = useToast()

const search = ref('')
const page = ref(1)
const perPage = 15
const total = ref(0)
const loading = ref(false)
const roles = ref<any[]>([])
const deleteModal = ref(false)
const deletingRole = ref<any>(null)
const deleteLoading = ref(false)

const { get, del } = useApi()

async function loadRoles() {
  loading.value = true
  try {
    const res = await get<any>('/admin/roles', {
      page: page.value,
      per_page: perPage,
      search: search.value || undefined,
    })
    roles.value = res?.data ?? []
    total.value = res?.meta?.total ?? 0
  } finally {
    loading.value = false
  }
}

let searchTimer: ReturnType<typeof setTimeout>
function onSearch() {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => { page.value = 1; loadRoles() }, 400)
}

function confirmDelete(role: any) {
  deletingRole.value = role
  deleteModal.value = true
}

async function deleteRole() {
  deleteLoading.value = true
  try {
    await del(`/admin/roles/${deletingRole.value.id}`)
    toast.add({ title: 'Rol o\'chirildi', color: 'green', icon: 'i-heroicons-check-circle' })
    deleteModal.value = false
    deletingRole.value = null
    loadRoles()
  } catch {
    toast.add({ title: 'Xatolik yuz berdi', color: 'red', icon: 'i-heroicons-x-circle' })
  } finally {
    deleteLoading.value = false
  }
}

function formatDate(date: string) {
  return new Date(date).toLocaleDateString('uz-UZ', { year: 'numeric', month: 'short', day: 'numeric' })
}

onMounted(loadRoles)
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>