<template>
  <NuxtLayout name="admin">
    <div class="max-w-4xl space-y-6">
      <div class="flex items-center gap-3">
        <NuxtLink to="/subjects">
          <UButton icon="i-heroicons-arrow-left" color="neutral" variant="ghost" size="sm" />
        </NuxtLink>
        <div>
          <h2 class="text-base font-semibold text-gray-900 dark:text-white">New Subject</h2>
          <p class="text-xs text-gray-400">Fill in the details to create a new subject</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-5">
          <UCard>
            <template #header>
              <p class="text-sm font-semibold text-gray-900 dark:text-white">General Info</p>
            </template>
            <div class="space-y-4">
              <UFormField label="Name" name="name" required>
                <UInput v-model="form.name" placeholder="Subject name" class="w-full" />
              </UFormField>

              <UFormField label="Description" name="description">
                <RichEditor v-model="form.description" />
              </UFormField>
            </div>
          </UCard>

          <UCard>
            <template #header>
              <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                  Topics
                  <UBadge :label="String(topics.length)" color="primary" variant="soft" size="xs" class="ml-2" />
                </p>
                <UButton
                  icon="i-heroicons-plus"
                  size="xs"
                  color="primary"
                  variant="soft"
                  label="Add Topic"
                  @click="addTopic"
                />
              </div>
            </template>

            <div v-if="!topics.length" class="py-8 text-center text-gray-400 text-sm">
              <UIcon name="i-heroicons-document-text" class="w-8 h-8 mx-auto mb-2 opacity-50" />
              No topics yet. Click "Add Topic" to begin.
            </div>

            <div v-else class="divide-y divide-gray-100 dark:divide-gray-800">
              <div
                v-for="(topic, i) in topics"
                :key="i"
                class="py-4 first:pt-0 last:pb-0 space-y-3"
              >
                <div class="flex items-center gap-2">
                  <span class="text-xs font-semibold text-gray-400 w-5">{{ i + 1 }}</span>
                  <UInput
                    v-model="topic.name"
                    :placeholder="`Topic ${i + 1} name`"
                    size="sm"
                    class="flex-1"
                  />
                  <UToggle v-model="topic.is_active" size="sm" />
                  <UButton
                    icon="i-heroicons-trash"
                    color="error"
                    variant="ghost"
                    size="xs"
                    @click="removeTopic(i)"
                  />
                </div>
                <div class="pl-7 grid grid-cols-2 gap-3">
                  <UFormField label="Time limit (min)" size="sm">
                    <UInput
                      v-model.number="topic.time_limit"
                      type="number"
                      placeholder="No limit"
                      size="sm"
                      class="w-full"
                    />
                  </UFormField>
                  <UFormField label="Order" size="sm">
                    <UInput
                      v-model.number="topic.order"
                      type="number"
                      size="sm"
                      class="w-full"
                    />
                  </UFormField>
                  <div class="col-span-2">
                    <UFormField label="Description" size="sm">
                      <UTextarea
                        v-model="topic.description"
                        placeholder="Optional description"
                        :rows="2"
                        size="sm"
                        class="w-full"
                      />
                    </UFormField>
                  </div>
                </div>
              </div>
            </div>
          </UCard>

          <UCard>
            <template #header>
              <p class="text-sm font-semibold text-gray-900 dark:text-white">Attachments</p>
            </template>
            <FileUpload
              @update:new-files="newFiles = $event"
              @update:deleted-ids="deletedFileIds = $event"
            />
          </UCard>
        </div>

        <div class="space-y-5">
          <UCard>
            <template #header>
              <p class="text-sm font-semibold text-gray-900 dark:text-white">Settings</p>
            </template>
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</p>
                  <p class="text-xs text-gray-400">Visible to users</p>
                </div>
                <UToggle v-model="form.is_active" />
              </div>

              <UFormField label="Display Order">
                <UInput v-model.number="form.order" type="number" class="w-full" />
              </UFormField>

              <UFormField label="Color">
                <div class="flex items-center gap-3">
                  <input
                    v-model="form.color"
                    type="color"
                    class="w-9 h-9 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer p-0.5"
                  />
                  <UInput v-model="form.color" placeholder="#16a34a" class="flex-1" size="sm" />
                </div>
              </UFormField>
            </div>
          </UCard>

          <UCard>
            <template #header>
              <p class="text-sm font-semibold text-gray-900 dark:text-white">Cover Image</p>
            </template>
            <div class="space-y-3">
              <div
                class="relative border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden cursor-pointer hover:border-primary-300 transition-colors"
                style="height: 140px"
                @click="imageInputRef?.click()"
              >
                <img
                  v-if="imagePreview"
                  :src="imagePreview"
                  class="w-full h-full object-cover"
                />
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
                @click="imagePreview = null; imageFile = null"
              />
            </div>
          </UCard>

          <UButton
            block
            color="primary"
            icon="i-heroicons-check"
            :loading="saving"
            @click="save"
          >
            Create Subject
          </UButton>
        </div>
      </div>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
const api = useApi()
const toast = useToast()
const router = useRouter()

const imageInputRef = ref<HTMLInputElement | null>(null)
const saving = ref(false)

const {
  form, topics, newFiles, deletedFileIds,
  imageFile, imagePreview,
  addTopic, removeTopic, onImageChange, buildFormData,
} = useSubjectForm()

async function save() {
  if (!form.name.trim()) {
    toast.add({ title: 'Name is required', color: 'error', icon: 'i-heroicons-x-circle' })
    return
  }
  saving.value = true
  try {
    const fd = buildFormData()
    await $fetch(`${useRuntimeConfig().public.apiBase}/admin/subjects`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${useCookie('auth_token').value}` },
      body: fd,
    })
    toast.add({ title: 'Subject created', color: 'success', icon: 'i-heroicons-check-circle' })
    router.push('/subjects')
  } catch (e: any) {
    const msg = e?.data?.message ?? 'Failed to create subject'
    toast.add({ title: msg, color: 'error', icon: 'i-heroicons-x-circle' })
  } finally {
    saving.value = false
  }
}
</script>