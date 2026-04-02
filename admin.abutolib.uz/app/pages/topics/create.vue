<template>
  <NuxtLayout name="admin">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 space-y-6">

      <div class="flex items-center gap-3">
        <NuxtLink to="/topics">
          <button
            type="button"
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 transition-all"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
        </NuxtLink>
        <div>
          <h2 class="text-base font-semibold text-gray-900 dark:text-white">New Topic</h2>
          <p class="text-xs text-gray-400 dark:text-gray-500">Fill in the details to create a new topic</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-5">

          <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/60">
              <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">General Info</p>
            </div>
            <div class="p-5 space-y-5">

              <div class="space-y-1.5">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">
                  Subject <span class="text-red-500">*</span>
                </label>
                <SubjectSelect v-model="form.subject_id" class="w-full" />
              </div>

              <div class="space-y-1.5">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">
                  Name <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.name"
                  type="text"
                  placeholder="Topic name"
                  class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                />
              </div>

              <div class="space-y-1.5">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">
                  Description
                </label>
                <RichEditor v-model="form.description" />
              </div>

            </div>
          </div>

          <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/60 flex items-center justify-between">
              <div class="flex items-center gap-2">
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Questions</p>
                <span
                  class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400"
                >
                  {{ questions.length }}
                </span>
              </div>
              <button
                type="button"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400 hover:bg-primary-100 dark:hover:bg-primary-900/50 transition-colors"
                @click="addQuestion"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add Question
              </button>
            </div>

            <div class="p-5">
              <div v-if="!questions.length" class="py-12 text-center">
                <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3">
                  <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                  </svg>
                </div>
                <p class="text-sm text-gray-400 dark:text-gray-500">No questions yet.</p>
                <p class="text-xs text-gray-300 dark:text-gray-600 mt-1">Click "Add Question" to begin.</p>
              </div>

              <div v-else class="space-y-4">
                <QuestionCard
                  v-for="(q, qi) in questions"
                  :key="qi"
                  :q="q"
                  :index="qi"
                  @remove="removeQuestion"
                  @type-change="onTypeChange"
                  @add-answer="addAnswer"
                  @remove-answer="removeAnswer"
                  @set-correct="setCorrect"
                  @add-pair="addMatchPair"
                  @remove-pair="removeMatchPair"
                />
              </div>
            </div>
          </div>

        </div>

        <div class="space-y-5">

          <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/60">
              <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Settings</p>
            </div>
            <div class="p-5 space-y-5">

              <div class="flex items-center justify-between gap-4">
                <div>
                  <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</p>
                  <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Visible to users</p>
                </div>
                <button
                  type="button"
                  role="switch"
                  :aria-checked="form.is_active"
                  class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                  :class="form.is_active ? 'bg-primary-500' : 'bg-gray-200 dark:bg-gray-700'"
                  @click="form.is_active = !form.is_active"
                >
                  <span
                    class="pointer-events-none inline-block h-4 w-4 rounded-full bg-white shadow transform transition-transform"
                    :class="form.is_active ? 'translate-x-4' : 'translate-x-0'"
                  />
                </button>
              </div>

              <div class="space-y-1.5">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">
                  Display Order
                </label>
                <input
                  v-model.number="form.order"
                  type="number"
                  class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                />
              </div>

              <div class="space-y-1.5">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">
                  Time Limit (min)
                </label>
                <input
                  v-model.number="form.time_limit"
                  type="number"
                  placeholder="No limit"
                  class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                />
              </div>

            </div>
          </div>

          <button
            type="button"
            :disabled="saving"
            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-primary-600 hover:bg-primary-700 disabled:opacity-60 disabled:cursor-not-allowed text-white shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
            @click="save"
          >
            <svg v-if="!saving" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
            <svg v-else class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
            </svg>
            {{ saving ? 'Creating…' : 'Create Topic' }}
          </button>

        </div>
      </div>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
const api = useApi()
const toast = useToast()
const router = useRouter()
const saving = ref(false)

const {
  form, questions,
  addQuestion, removeQuestion, onTypeChange,
  addAnswer, removeAnswer, setCorrect,
  addMatchPair, removeMatchPair,
  buildPayload,
} = useTopicForm()

async function save() {
  if (!form.subject_id) {
    toast.add({ title: 'Please select a subject', color: 'error', icon: 'i-heroicons-x-circle' })
    return
  }
  if (!form.name.trim()) {
    toast.add({ title: 'Name is required', color: 'error', icon: 'i-heroicons-x-circle' })
    return
  }
  saving.value = true
  try {
    await api.post('/admin/topics', buildPayload())
    toast.add({ title: 'Topic created', color: 'success', icon: 'i-heroicons-check-circle' })
    router.push('/topics')
  } catch (e: any) {
    toast.add({ title: e?.data?.message ?? 'Failed to create topic', color: 'error', icon: 'i-heroicons-x-circle' })
  } finally {
    saving.value = false
  }
}
</script>