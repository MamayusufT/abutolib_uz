<template>
  <div class="max-w-3xl">
    <div class="flex items-center gap-3 mb-6">
      <UButton
        to="/roles"
        icon="i-heroicons-arrow-left"
        color="gray"
        variant="ghost"
        size="sm"
      />
      <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
          Rolni tahrirlash
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
          <span v-if="!pageLoading" class="font-medium text-primary-600 dark:text-primary-400">"{{ form.name }}"</span>
          rolini tahrirlayapsiz
        </p>
      </div>
    </div>

    <div v-if="pageLoading" class="flex justify-center py-20">
      <UIcon name="i-heroicons-arrow-path" class="animate-spin w-8 h-8 text-gray-400" />
    </div>

    <form v-else @submit.prevent="submit">
      <div class="space-y-5">
        <UCard>
          <template #header>
            <div class="flex items-center gap-2">
              <UIcon name="i-heroicons-identification" class="text-primary-500 w-5 h-5" />
              <h2 class="text-sm font-semibold">Asosiy ma'lumotlar</h2>
            </div>
          </template>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <UFormGroup label="Rol nomi" name="name" required :error="errors.name">
              <UInput
                v-model="form.name"
                placeholder="masalan: moderator"
                icon="i-heroicons-shield-check"
              />
            </UFormGroup>

            <UFormGroup label="Guard" name="guard_name">
              <USelect
                v-model="form.guard_name"
                :options="guardOptions"
                icon="i-heroicons-lock-closed"
              />
            </UFormGroup>
          </div>
        </UCard>

        <UCard>
          <template #header>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <UIcon name="i-heroicons-key" class="text-primary-500 w-5 h-5" />
                <h2 class="text-sm font-semibold">Ruxsatlar</h2>
              </div>
              <div class="flex items-center gap-2">
                <UBadge
                  v-if="form.permissions.length > 0"
                  :label="`${form.permissions.length} ta tanlangan`"
                  color="primary"
                  variant="soft"
                  size="xs"
                />
                <UButton
                  v-if="form.permissions.length > 0"
                  label="Hammasini olib tashlash"
                  color="red"
                  variant="ghost"
                  size="xs"
                  @click="form.permissions = []"
                />
              </div>
            </div>
          </template>

          <div v-if="permsLoading" class="flex justify-center py-10">
            <UIcon name="i-heroicons-arrow-path" class="animate-spin w-6 h-6 text-gray-400" />
          </div>

          <div v-else class="space-y-3">
            <div
              v-for="(perms, group) in permissions"
              :key="group"
              class="border border-gray-100 dark:border-gray-800 rounded-lg overflow-hidden"
            >
              <div
                class="flex items-center justify-between px-4 py-2.5 bg-gray-50 dark:bg-gray-800/50 cursor-pointer select-none"
                @click="toggleGroupOpen(group)"
              >
                <div class="flex items-center gap-2">
                  <UCheckbox
                    :model-value="isGroupSelected(perms)"
                    :indeterminate="isGroupIndeterminate(perms)"
                    @update:model-value="toggleGroup(group, perms)"
                    @click.stop
                  />
                  <span class="text-sm font-medium capitalize text-gray-700 dark:text-gray-200">
                    {{ group }}
                  </span>
                  <UBadge
                    :label="`${perms.filter(p => form.permissions.includes(p)).length}/${perms.length}`"
                    color="gray"
                    variant="soft"
                    size="xs"
                  />
                </div>
                <UIcon
                  :name="openGroups.includes(group) ? 'i-heroicons-chevron-up' : 'i-heroicons-chevron-down'"
                  class="w-4 h-4 text-gray-400"
                />
              </div>

              <div v-if="openGroups.includes(group)" class="px-4 py-3 flex flex-wrap gap-2">
                <label
                  v-for="perm in perms"
                  :key="perm"
                  class="flex items-center gap-2 cursor-pointer text-sm px-3 py-1.5 rounded-md border transition-all"
                  :class="form.permissions.includes(perm)
                    ? 'border-primary-200 bg-primary-50 text-primary-700 dark:border-primary-700 dark:bg-primary-950 dark:text-primary-300'
                    : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-gray-300'"
                >
                  <UCheckbox
                    :model-value="form.permissions.includes(perm)"
                    @update:model-value="togglePerm(perm)"
                  />
                  {{ perm }}
                </label>
              </div>
            </div>
          </div>
        </UCard>

        <UCard>
          <template #header>
            <div class="flex items-center gap-2">
              <UIcon name="i-heroicons-information-circle" class="text-gray-400 w-5 h-5" />
              <h2 class="text-sm font-semibold">Ma'lumot</h2>
            </div>
          </template>
          <dl class="grid grid-cols-2 gap-3 text-sm">
            <div>
              <dt class="text-gray-500 dark:text-gray-400">ID</dt>
              <dd class="font-mono font-medium">#{{ route.params.id }}</dd>
            </div>
            <div>
              <dt class="text-gray-500 dark:text-gray-400">Yaratilgan</dt>
              <dd class="font-medium">{{ formatDate(createdAt) }}</dd>
            </div>
          </dl>
        </UCard>

        <div class="flex justify-end gap-3">
          <UButton to="/roles" label="Bekor qilish" color="gray" variant="ghost" />
          <UButton
            type="submit"
            label="Yangilash"
            icon="i-heroicons-check"
            :loading="saving"
          />
        </div>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'admin' })

const route = useRoute()
const router = useRouter()
const toast = useToast()
const { get, put } = useApi()

const id = computed(() => route.params.id as string)

const form = reactive({
  name: '',
  guard_name: 'web',
  permissions: [] as string[],
})

const errors = reactive<Record<string, string>>({})
const pageLoading = ref(true)
const permsLoading = ref(false)
const saving = ref(false)
const permissions = ref<Record<string, string[]>>({})
const openGroups = ref<string[]>([])
const createdAt = ref('')

const guardOptions = [
  { label: 'web', value: 'web' },
  { label: 'api', value: 'api' },
]

async function loadRole() {
  pageLoading.value = true
  try {
    const res = await get<any>(`/admin/roles/${id.value}`)
    const role = res?.data
    form.name = role.name
    form.guard_name = role.guard_name
    form.permissions = role.permissions ?? []
    createdAt.value = role.created_at
  } finally {
    pageLoading.value = false
  }
}

async function loadPermissions() {
  permsLoading.value = true
  try {
    const res = await get<any>('/admin/permissions')
    permissions.value = res?.data ?? {}
    openGroups.value = Object.keys(permissions.value)
  } finally {
    permsLoading.value = false
  }
}

function isGroupSelected(perms: string[]) {
  return perms.every(p => form.permissions.includes(p))
}

function isGroupIndeterminate(perms: string[]) {
  const count = perms.filter(p => form.permissions.includes(p)).length
  return count > 0 && count < perms.length
}

function toggleGroupOpen(group: string) {
  if (openGroups.value.includes(group)) {
    openGroups.value = openGroups.value.filter(g => g !== group)
  } else {
    openGroups.value.push(group)
  }
}

function toggleGroup(_group: string, perms: string[]) {
  if (isGroupSelected(perms)) {
    form.permissions = form.permissions.filter(p => !perms.includes(p))
  } else {
    form.permissions = [...new Set([...form.permissions, ...perms])]
  }
}

function togglePerm(perm: string) {
  if (form.permissions.includes(perm)) {
    form.permissions = form.permissions.filter(p => p !== perm)
  } else {
    form.permissions.push(perm)
  }
}

async function submit() {
  Object.keys(errors).forEach(k => delete errors[k])

  if (!form.name.trim()) {
    errors.name = 'Rol nomi kiritilishi shart'
    return
  }

  saving.value = true
  try {
    await put(`/admin/roles/${id.value}`, { ...form })
    toast.add({ title: 'Rol yangilandi', color: 'green', icon: 'i-heroicons-check-circle' })
    router.push('/roles')
  } catch (err: any) {
    const errData = err?.data?.errors ?? {}
    Object.assign(errors, errData)
    toast.add({ title: 'Xatolik yuz berdi', color: 'red', icon: 'i-heroicons-x-circle' })
  } finally {
    saving.value = false
  }
}

function formatDate(date: string) {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('uz-UZ', { year: 'numeric', month: 'short', day: 'numeric' })
}

onMounted(() => Promise.all([loadRole(), loadPermissions()]))
</script>