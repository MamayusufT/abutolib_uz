<template>
  <NuxtLayout name="admin">
    <div v-if="pending" class="flex items-center justify-center h-64">
      <UIcon name="i-heroicons-arrow-path" class="w-6 h-6 text-gray-400 animate-spin" />
    </div>

    <div v-else-if="member" class="space-y-6">
      <div class="flex items-center gap-3">
        <NuxtLink to="/team">
          <UButton icon="i-heroicons-arrow-left" color="neutral" variant="ghost" size="sm" />
        </NuxtLink>
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-800 shrink-0">
            <img v-if="member.avatar" :src="member.avatar" class="w-full h-full object-cover" />
            <div v-else class="w-full h-full flex items-center justify-center text-sm font-semibold text-gray-400">
              {{ member.name.charAt(0) }}
            </div>
          </div>
          <div>
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ member.name }}</h2>
            <p class="text-xs text-gray-400">{{ member.role ?? 'Team Member' }}</p>
          </div>
        </div>
      </div>

      <div class="max-w-2xl space-y-6">
        <UCard>
          <template #header>
            <p class="text-sm font-semibold text-gray-900 dark:text-white">Profile</p>
          </template>
          <div class="flex items-start gap-6">
            <div class="shrink-0">
              <div
                class="w-24 h-24 rounded-2xl overflow-hidden border-2 border-dashed border-gray-200 dark:border-gray-700 cursor-pointer hover:border-primary-300 transition-colors"
                @click="imageInputRef?.click()"
              >
                <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full flex flex-col items-center justify-center gap-1 bg-gray-50 dark:bg-gray-800">
                  <UIcon name="i-heroicons-user" class="w-7 h-7 text-gray-300" />
                  <span class="text-[10px] text-gray-400">Photo</span>
                </div>
              </div>
              <input ref="imageInputRef" type="file" accept="image/*" class="hidden" @change="onImageChange" />
              
              <button
                v-if="imagePreview"
                type="button"
                class="mt-1.5 w-full text-xs text-red-400 hover:text-red-500 transition-colors"
                @click="removeImage"
              >
                Remove
              </button>
            </div>

            <div class="flex-1 space-y-3">
              <UFormField label="Full Name" required>
                <UInput v-model="form.name" placeholder="John Doe" class="w-full" />
              </UFormField>
              <UFormField label="Role / Position">
                <UInput v-model="form.role" placeholder="e.g. Lead Developer" class="w-full" />
              </UFormField>
            </div>
          </div>
        </UCard>

        <UCard>
          <template #header>
            <p class="text-sm font-semibold text-gray-900 dark:text-white">Contact & Social</p>
          </template>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <UFormField label="Email">
              <UInput v-model="form.email" type="email" placeholder="john@example.com" leading-icon="i-heroicons-envelope" class="w-full" />
            </UFormField>
            <UFormField label="Telegram">
              <UInput v-model="form.telegram" placeholder="@username" class="w-full">
                <template #leading><span class="text-sky-500 text-xs font-medium pl-1">TG</span></template>
              </UInput>
            </UFormField>
            <UFormField label="Instagram">
              <UInput v-model="form.instagram" placeholder="@username" class="w-full">
                <template #leading><span class="text-pink-500 text-xs font-medium pl-1">IG</span></template>
              </UInput>
            </UFormField>
            <UFormField label="Display Order">
              <UInput v-model.number="form.order" type="number" class="w-full" />
            </UFormField>
          </div>
        </UCard>

        <UCard>
          <template #header>
            <p class="text-sm font-semibold text-gray-900 dark:text-white">Bio</p>
          </template>
          <div class="space-y-4">
            <UTextarea v-model="form.bio" placeholder="Short biography..." :rows="4" class="w-full" />
            <div class="flex items-center justify-between pt-1">
              <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</p>
                <p class="text-xs text-gray-400">Show on public team page</p>
              </div>
              <UToggle v-model="form.is_active" />
            </div>
          </div>
        </UCard>

        <div class="flex items-center gap-3">
          <NuxtLink to="/team" class="flex-1">
            <UButton label="Cancel" color="neutral" variant="soft" block />
          </NuxtLink>
          <UButton
            label="Save Changes"
            color="primary"
            icon="i-heroicons-check"
            :loading="saving"
            class="flex-1"
            @click="save"
          />
        </div>
      </div>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
// Utilitlar va Composable-lar
const route = useRoute()
const router = useRouter()
const api = useApi()
const toast = useToast()
const { public: { apiBase } } = useRuntimeConfig()
const token = useCookie('auth_token')

// Holatlar (State)
const saving = ref(false)
const imageInputRef = ref<HTMLInputElement | null>(null)
const imageFile = ref<File | null>(null)
const imagePreview = ref<string | null>(null)

// Forma obyekti
const form = reactive({
  name: '',
  role: '',
  email: '',
  telegram: '',
  instagram: '',
  bio: '',
  order: 0,
  is_active: true,
})

// Ma'lumotni API'dan olish (Trailing slash CORS xatosini oldini olish uchun qo'shildi)
const { data: member, pending } = await useAsyncData(
  `team-${route.params.id}`,
  () => api.get<any>(`/admin/team-members/${route.params.id}/`).then((r: any) => r.data)
)

// API'dan kelgan ma'lumotni formaga yuklash
watch(member, (m) => {
  if (!m) return
  Object.assign(form, {
    name: m.name || '',
    role: m.role || '',
    email: m.email || '',
    telegram: m.telegram || '',
    instagram: m.instagram || '',
    bio: m.bio || '',
    order: m.order ?? 0,
    is_active: !!m.is_active,
  })
  if (m.avatar) imagePreview.value = m.avatar
}, { immediate: true })

// Rasm o'zgarganda ishlovchi funksiya
function onImageChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (!file) return

  // Eski blob URL'ni xotiradan tozalash (Memory leak oldini olish)
  if (imagePreview.value && imagePreview.value.startsWith('blob:')) {
    URL.revokeObjectURL(imagePreview.value)
  }

  imageFile.value = file
  imagePreview.value = URL.createObjectURL(file)
}

// Rasmni o'chirish
function removeImage() {
  if (imagePreview.value && imagePreview.value.startsWith('blob:')) {
    URL.revokeObjectURL(imagePreview.value)
  }
  imagePreview.value = null
  imageFile.value = null
}

// Saqlash funksiyasi
async function save() {
  if (!form.name.trim()) {
    toast.add({ title: 'Name is required', color: 'error', icon: 'i-heroicons-x-circle' })
    return
  }

  saving.value = true
  try {
    // Agar rasm yuklangan bo'lsa FormData ishlatamiz
    if (imageFile.value) {
      const fd = new FormData()
      
      // Laravel PUT so'rovlarida faylni tanishi uchun spoofing kerak
      fd.append('_method', 'PUT') 
      
      // Formadagi barcha maydonlarni FormData'ga o'tkazish
      Object.entries(form).forEach(([k, v]) => {
        // Boolean qiymatlarni server tushunishi uchun 1/0 ko'rinishida yuboramiz
        const val = typeof v === 'boolean' ? (v ? '1' : '0') : String(v)
        fd.append(k, val)
      })
      
      fd.append('avatar', imageFile.value)

      // FormData yuborilganda trailing slash CORS xatolarini kamaytiradi
      await $fetch(`${apiBase}/admin/team-members/${route.params.id}/`, {
        method: 'POST',
        headers: { Authorization: `Bearer ${token.value}` },
        body: fd,
      })
    } else {
      // Rasm bo'lmasa oddiy JSON PUT so'rovi
      await api.put(`/admin/team-members/${route.params.id}/`, form)
    }

    toast.add({ title: 'Member updated', color: 'success', icon: 'i-heroicons-check-circle' })
    router.push('/team')
  } catch (e: any) {
    // Xatolik xabarini chiqarish
    toast.add({ 
      title: e?.data?.message ?? 'Failed to update member', 
      color: 'error', 
      icon: 'i-heroicons-x-circle' 
    })
  } finally {
    saving.value = false
  }
}

// Sahifadan chiqib ketayotganda xotirani tozalash
onUnmounted(() => {
  if (imagePreview.value && imagePreview.value.startsWith('blob:')) {
    URL.revokeObjectURL(imagePreview.value)
  }
})
</script>