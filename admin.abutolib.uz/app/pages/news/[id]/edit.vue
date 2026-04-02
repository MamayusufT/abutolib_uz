<template>
  <NuxtLayout name="admin">
    <div v-if="pending" class="flex items-center justify-center h-64">
      <UIcon name="i-heroicons-arrow-path" class="w-6 h-6 text-gray-400 animate-spin" />
    </div>

    <div v-else-if="article" class="max-w-4xl space-y-6">
      <div class="flex items-center gap-3">
        <NuxtLink to="/news">
          <UButton icon="i-heroicons-arrow-left" color="neutral" variant="ghost" size="sm" />
        </NuxtLink>
        <div>
          <h2 class="text-base font-semibold text-gray-900 dark:text-white">Edit Article</h2>
          <p class="text-xs text-gray-400 truncate max-w-xs">{{ article.title }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-5">
          <UCard>
            <template #header>
              <p class="text-sm font-semibold text-gray-900 dark:text-white">Content</p>
            </template>
            <div class="space-y-4">
              <UFormField label="Title" required>
                <UInput v-model="form.title" placeholder="Article title" class="w-full" />
              </UFormField>

              <UFormField label="Excerpt">
                <UTextarea
                  v-model="form.excerpt"
                  placeholder="Short description (max 500 characters)..."
                  :rows="2"
                  class="w-full"
                />
              </UFormField>

              <UFormField label="Body" required>
                <RichEditor v-model="form.body" />
              </UFormField>
            </div>
          </UCard>
        </div>

        <div class="space-y-5">
          <UCard>
            <template #header>
              <p class="text-sm font-semibold text-gray-900 dark:text-white">Settings</p>
            </template>
            <div class="space-y-4">
              <UFormField label="Status" required>
                <div class="flex flex-col gap-1.5">
                  <label
                    v-for="opt in statusOptions"
                    :key="opt.value"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg border cursor-pointer transition-colors"
                    :class="form.status === opt.value
                      ? 'border-primary-300 bg-primary-50 dark:border-primary-700 dark:bg-primary-900/20'
                      : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/50'"
                  >
                    <input v-model="form.status" type="radio" :value="opt.value" class="hidden" />
                    <span class="w-2 h-2 rounded-full shrink-0" :class="opt.dot" />
                    <span class="text-sm text-gray-700 dark:text-gray-300 flex-1">{{ opt.label }}</span>
                    <UIcon
                      v-if="form.status === opt.value"
                      name="i-heroicons-check-circle"
                      class="w-4 h-4 text-primary-500"
                    />
                  </label>
                </div>
              </UFormField>

              <UFormField label="Category">
                <UInput v-model="form.category" placeholder="e.g. Science, Math..." class="w-full" />
              </UFormField>

              <UFormField label="Publish Date">
                <UInput v-model="form.published_at" type="datetime-local" class="w-full" />
              </UFormField>

              <div class="pt-1 text-xs text-gray-400 space-y-1 border-t border-gray-100 dark:border-gray-800">
                <p>Views: <span class="font-medium text-gray-600 dark:text-gray-400">{{ article.views ?? 0 }}</span></p>
                <p>Author: <span class="font-medium text-gray-600 dark:text-gray-400">{{ article.author?.name ?? '—' }}</span></p>
              </div>
            </div>
          </UCard>

          <UCard>
            <template #header>
              <p class="text-sm font-semibold text-gray-900 dark:text-white">Cover Image</p>
            </template>
            <div class="space-y-3">
              <div
                class="relative border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden cursor-pointer hover:border-primary-300 transition-colors"
                style="height: 150px"
                @click="imageInputRef?.click()"
              >
                <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" />
                <div v-else class="flex flex-col items-center justify-center h-full gap-2">
                  <UIcon name="i-heroicons-photo" class="w-7 h-7 text-gray-300" />
                  <p class="text-xs text-gray-400">Click to upload</p>
                </div>
              </div>
              <input ref="imageInputRef" type="file" accept="image/*" class="hidden" @change="onImageChange" />
              <UButton
                v-if="imagePreview"
                label="Remove image"
                color="error"
                variant="ghost"
                size="xs"
                block
                @click="imagePreview = null; imageFile = null; removeImage = true"
              />
            </div>
          </UCard>

          <UButton block color="primary" icon="i-heroicons-check" :loading="saving" @click="save">
            Save Changes
          </UButton>
        </div>
      </div>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
const route = useRoute()
const router = useRouter()
const api = useApi()
const toast = useToast()
const { public: { apiBase } } = useRuntimeConfig()
const token = useCookie('auth_token')

const saving = ref(false)
const imageInputRef = ref<HTMLInputElement | null>(null)
const imageFile = ref<File | null>(null)
const imagePreview = ref<string | null>(null)
const removeImage = ref(false)

const form = reactive({
  title: '',
  excerpt: '',
  body: '',
  status: 'draft',
  category: '',
  published_at: '',
})

const statusOptions = [
  { label: 'Draft',     value: 'draft',     dot: 'bg-amber-400' },
  { label: 'Published', value: 'published', dot: 'bg-green-400' },
  { label: 'Archived',  value: 'archived',  dot: 'bg-gray-400' },
]

const { data: article, pending } = await useAsyncData(
  `news-${route.params.id}`,
  () => api.get<any>(`/admin/news/${route.params.id}`).then((r: any) => r.data)
)

watch(article, (a) => {
  if (!a) return
  form.title = a.title
  form.excerpt = a.excerpt ?? ''
  form.body = a.body ?? ''
  form.status = a.status
  form.category = a.category ?? ''
  form.published_at = a.published_at
    ? new Date(a.published_at).toISOString().slice(0, 16)
    : ''
  if (a.image) imagePreview.value = a.image
}, { immediate: true })

function onImageChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (!file) return
  imageFile.value = file
  imagePreview.value = URL.createObjectURL(file)
  removeImage.value = false
}

async function save() {
  if (!form.title.trim()) {
    toast.add({ title: 'Title is required', color: 'error', icon: 'i-heroicons-x-circle' })
    return
  }

  saving.value = true
  try {
    if (imageFile.value) {
      const fd = new FormData()
      fd.append('_method', 'PUT')
      fd.append('title', form.title)
      fd.append('excerpt', form.excerpt)
      fd.append('body', form.body)
      fd.append('status', form.status)
      fd.append('category', form.category)
      if (form.published_at) fd.append('published_at', form.published_at)
      fd.append('image', imageFile.value)

      await $fetch(`${apiBase}/admin/news/${route.params.id}`, {
        method: 'POST',
        headers: { Authorization: `Bearer ${token.value}` },
        body: fd,
      })
    } else {
      await api.put(`/admin/news/${route.params.id}`, {
        title: form.title,
        excerpt: form.excerpt,
        body: form.body,
        status: form.status,
        category: form.category,
        published_at: form.published_at || null,
      })
    }

    toast.add({ title: 'Article updated', color: 'success', icon: 'i-heroicons-check-circle' })
    router.push('/news')
  } catch (e: any) {
    toast.add({ title: e?.data?.message ?? 'Failed to update article', color: 'error', icon: 'i-heroicons-x-circle' })
  } finally {
    saving.value = false
  }
}
</script>