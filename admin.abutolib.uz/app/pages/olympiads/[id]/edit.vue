<template>
  <NuxtLayout name="admin">
    <div v-if="pending" class="flex flex-col items-center justify-center h-96 gap-4">
      <UIcon name="i-heroicons-arrow-path" class="w-10 h-10 text-primary-500 animate-spin" />
    </div>

    <div v-else-if="olympiad" class="max-w-6xl mx-auto pb-20 px-4">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
        <div class="flex items-center gap-4">
          <NuxtLink to="/olympiads">
            <UButton icon="i-heroicons-arrow-left" color="neutral" variant="ghost" class="rounded-full" />
          </NuxtLink>
          <div>
            <div class="flex items-center gap-2">
              <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Tahrirlash</h1>
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider shadow-sm" :class="statusClass(olympiad.status)">
                <span class="w-1.5 h-1.5 rounded-full" :class="statusDot(olympiad.status)" />
                {{ olympiad.status }}
              </span>
            </div>
            <p class="text-sm text-gray-500 truncate max-w-md">{{ olympiad.name_uz }}</p>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <UButton variant="soft" color="neutral" label="Bekor qilish" to="/olympiads" />
          <UButton color="primary" icon="i-heroicons-check-badge" :loading="saving" size="lg" class="shadow-lg shadow-primary-500/20" @click="save">
            Saqlash
          </UButton>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-8 space-y-8">
          <UCard class="border-none shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 rounded-2xl">
            <template #header>
              <div class="flex items-center gap-2">
                <UIcon name="i-heroicons-document-text" class="w-5 h-5 text-primary-500" />
                <span class="font-bold">Asosiy ma'lumotlar</span>
              </div>
            </template>
            <div class="space-y-6">
              <UFormField label="Olimpiada nomi" required>
                <UInput v-model="form.name_uz" size="xl" />
              </UFormField>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <UFormField label="Turi">
                  <USelectMenu v-model="form.type" :options="typeOptions" value-attribute="value" option-attribute="label" size="lg">
                    <template #leading>
                      <UIcon :name="typeOptions.find(t => t.value === form.type)?.icon || 'i-heroicons-question-mark-circle'" class="w-4 h-4" />
                    </template>
                    <template #option="{ option }">
                      <div class="flex items-center gap-2">
                        <UIcon :name="option.icon" class="w-4 h-4 text-gray-400" />
                        <span>{{ option.label }}</span>
                      </div>
                    </template>
                  </USelectMenu>
                </UFormField>
                <UFormField label="Tili">
                  <div class="flex p-1 bg-gray-100 dark:bg-gray-800 rounded-xl">
                    <button v-for="l in ['uz', 'ru', 'en']" :key="l" type="button" class="flex-1 py-2 rounded-lg text-xs font-bold uppercase transition-all" :class="form.lang === l ? 'bg-white dark:bg-gray-700 shadow-sm text-primary-600' : 'text-gray-400'" @click="form.lang = l">
                      {{ l }}
                    </button>
                  </div>
                </UFormField>
              </div>
              <UFormField label="Tavsif">
                <RichEditor v-model="form.description_uz" class="min-h-[150px]" />
              </UFormField>
            </div>
          </UCard>

          <UCard class="border-none shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 rounded-2xl">
            <template #header>
              <div class="flex items-center gap-2">
                <UIcon name="i-heroicons-clock" class="w-5 h-5 text-blue-500" />
                <span class="font-bold">Vaqt sozlamalari</span>
              </div>
            </template>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <UFormField label="Boshlanish vaqti">
                <UInput v-model="form.starts_at" type="datetime-local" size="md" />
              </UFormField>
              <UFormField label="Tugash vaqti">
                <UInput v-model="form.ends_at" type="datetime-local" size="md" />
              </UFormField>
              <UFormField label="Davomiyligi (daqiqa)">
                <UInput v-model.number="form.duration" type="number" />
              </UFormField>
              <UFormField label="Urinishlar soni">
                <UInput v-model.number="form.max_attempts" type="number" />
              </UFormField>
            </div>
          </UCard>

          <div class="space-y-4">
            <div class="flex items-center justify-between px-2">
              <h3 class="text-xl font-bold flex items-center gap-2">Savollar <UBadge :label="questions.length" color="neutral" variant="subtle" class="rounded-full" /></h3>
              <div class="flex gap-2">
                <UButton icon="i-heroicons-cloud-arrow-up" label="Import" variant="soft" color="neutral" size="sm" @click="importModal = true" />
                <div class="relative" ref="addQWrapRef">
                  <UButton icon="i-heroicons-plus" label="Qo'shish" color="primary" size="sm" @click="addQOpen = !addQOpen" />
                  <Transition name="dd">
                    <div v-if="addQOpen" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-2xl z-50 py-1">
                      <button v-for="qt in questionTypes" :key="qt.value" @click="addQuestion(qt.value); addQOpen = false" class="w-full flex items-center gap-3 px-4 py-3 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        <UIcon :name="qt.icon" class="w-4 h-4 text-gray-400" /> {{ qt.label }}
                      </button>
                    </div>
                  </Transition>
                </div>
              </div>
            </div>

            <div v-for="(q, qi) in questions" :key="q.id ?? qi" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl overflow-hidden shadow-sm">
              <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-4">
                  <span class="w-8 h-8 rounded-xl bg-primary-600 text-white flex items-center justify-center font-bold text-sm shadow-md shadow-primary-500/20">{{ qi + 1 }}</span>
                  <div class="flex p-0.5 bg-gray-200/50 dark:bg-gray-800 rounded-lg">
                    <button v-for="qt in questionTypes" :key="qt.value" @click="q.type = qt.value" class="px-3 py-1 text-[10px] font-bold uppercase rounded-md transition-all" :class="q.type === qt.value ? 'bg-white dark:bg-gray-700 text-primary-600 shadow-sm' : 'text-gray-500'">
                      {{ qt.label }}
                    </button>
                  </div>
                </div>
                <div class="flex items-center gap-3">
                  <select v-model="q.difficulty" class="text-xs font-medium bg-transparent border-none focus:ring-0 text-gray-500">
                    <option value="easy">Easy</option>
                    <option value="medium">Medium</option>
                    <option value="hard">Hard</option>
                  </select>
                  <UButton icon="i-heroicons-trash" color="error" variant="ghost" size="xs" @click="removeQuestion(qi)" />
                </div>
              </div>

              <div class="p-6 space-y-6">
                <UFormField label="Savol matni">
                  <RichEditor v-model="q.question" />
                </UFormField>
                <div v-if="q.type !== 'open'" class="space-y-3">
                  <div class="flex items-center justify-between px-1">
                    <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Javoblar</span>
                    <UButton label="Variant qo'shish" variant="link" size="xs" @click="addAnswer(qi)" />
                  </div>
                  <div v-for="(a, ai) in q.answers" :key="a.id ?? ai" class="flex items-center gap-3">
                    <button type="button" class="w-6 h-6 shrink-0 flex items-center justify-center border-2 transition-all shadow-sm" :class="[q.type === 'multiple' ? 'rounded-md' : 'rounded-full', a.is_correct ? 'bg-primary-500 border-primary-500 text-white ring-2 ring-primary-500/20' : 'border-gray-200 dark:border-gray-700']" @click="q.type === 'multiple' ? toggleCorrectMultiple(qi, ai) : setCorrectSingle(qi, ai)">
                      <UIcon v-if="a.is_correct" name="i-heroicons-check" class="w-4 h-4" />
                    </button>
                    <UInput v-model="a.answer" class="flex-1" />
                    <UButton v-if="q.answers.length > 2" icon="i-heroicons-trash" color="neutral" variant="ghost" size="xs" @click="removeAnswer(qi, ai)" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="lg:col-span-4 space-y-6">
          <UCard class="sticky top-6 border-none shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 rounded-2xl">
            <template #header>
              <div class="flex items-center gap-2">
                <UIcon name="i-heroicons-cog-8-tooth" class="w-5 h-5 text-gray-500" />
                <span class="font-bold">Sozlamalar</span>
              </div>
            </template>
            <div class="space-y-6">
              <div class="space-y-4">
                <div v-for="opt in statusSwitches" :key="opt.key" class="flex items-center justify-between p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                  <div class="flex flex-col">
                    <span class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ opt.label }}</span>
                    <span class="text-[10px] text-gray-500 leading-tight">{{ opt.hint }}</span>
                  </div>
                  <USwitch v-model="(form as any)[opt.key]" size="md" color="primary" />
                </div>
              </div>

              <div class="p-4 rounded-2xl border border-gray-100 dark:border-gray-800 space-y-3">
                <div class="flex justify-between items-center">
                  <span class="text-xs font-bold text-gray-400 uppercase">O'tish bali</span>
                  <span class="text-sm font-black text-primary-600 px-2 py-0.5 bg-primary-50 dark:bg-primary-900/20 rounded-lg">{{ form.pass_score }}%</span>
                </div>
                <input v-model.number="form.pass_score" type="range" min="0" max="100" step="5" class="w-full h-1.5 rounded-full accent-primary-500 bg-gray-200 dark:bg-gray-700" />
              </div>

              <UFormField label="Savollar soni">
                <UInput v-model.number="form.questions_count" type="number" placeholder="Hammasi" />
              </UFormField>

              <div class="space-y-3 pt-2">
                <span class="text-xs font-bold text-gray-400 uppercase px-1">Natijalar ko'rinishi</span>
                <div class="grid grid-cols-1 gap-2">
                  <button v-for="status in showAnswerStatuses" :key="status.value" @click="form.show_answers = status.value" class="flex items-center justify-between p-3 rounded-xl border text-left transition-all" :class="form.show_answers === status.value ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-500 ring-1 ring-primary-500 text-primary-700 dark:text-primary-400' : 'border-gray-200 dark:border-gray-800'">
                    <div class="flex flex-col">
                      <span class="text-xs font-bold">{{ status.label }}</span>
                      <span class="text-[9px] opacity-70">{{ status.desc }}</span>
                    </div>
                    <UIcon v-if="form.show_answers === status.value" name="i-heroicons-check-circle-solid" class="w-4 h-4" />
                  </button>
                </div>
              </div>
            </div>
          </UCard>
        </div>
      </div>
    </div>

    <UModal v-model:open="importModal">
      <UCard class="border-none">
        <template #header><h3 class="font-bold">Fayldan import</h3></template>
        <div class="space-y-4">
          <div @click="importInputRef?.click()" class="border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-2xl p-10 text-center cursor-pointer">
            <input ref="importInputRef" type="file" class="hidden" @change="onImportFile" />
            <UIcon :name="importFile ? 'i-heroicons-document-check' : 'i-heroicons-arrow-up-tray'" class="w-12 h-12 mx-auto mb-4 text-gray-300" />
            <p class="text-sm font-bold text-gray-700">{{ importFile ? importFile.name : 'Faylni tanlang' }}</p>
          </div>
        </div>
        <template #footer>
          <div class="flex justify-end gap-2">
            <UButton label="Yopish" variant="ghost" @click="importModal = false" />
            <UButton label="Import" color="primary" :loading="importing" :disabled="!importFile" @click="doImport" />
          </div>
        </template>
      </UCard>
    </UModal>
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
const importing = ref(false)
const importModal = ref(false)
const importFile = ref<File | null>(null)
const importResult = ref<{ success: boolean; message: string } | null>(null)
const importInputRef = ref<HTMLInputElement | null>(null)
const addQOpen = ref(false)
const addQWrapRef = ref<HTMLElement | null>(null)

const typeOptions = [
  { label: 'Quiz', value: 'quiz', icon: 'i-heroicons-academic-cap' },
  { label: 'Olimpiada', value: 'olympiad', icon: 'i-heroicons-trophy' },
  { label: 'Imtihon', value: 'exam', icon: 'i-heroicons-clipboard-document-check' },
]

const questionTypes = [
  { label: 'Single', value: 'single', icon: 'i-heroicons-check-circle' },
  { label: 'Multiple', value: 'multiple', icon: 'i-heroicons-check-badge' },
  { label: 'Open', value: 'open', icon: 'i-heroicons-pencil-square' },
]

const statusSwitches = [
  { key: 'is_active', label: 'Public Access', hint: 'Olimpiada hamma uchun ochiq' },
  { key: 'shuffle_questions', label: 'Shuffle Questions', hint: 'Savollar tartibini aralashtirish' },
  { key: 'shuffle_options', label: 'Shuffle Options', hint: 'Javoblar tartibini aralashtirish' },
]

const showAnswerStatuses = [
  { label: 'Hech qachon', value: 'never', desc: 'To\'g\'ri javoblar ko\'rsatilmaydi' },
  { label: 'Topshirgach', value: 'after_submission', desc: 'Urinish yakunlanishi bilan' },
  { label: 'Yakunlangach', value: 'after_finish', desc: 'Olimpiada vaqti tugagach' },
]

function statusDot(s: string) { return { upcoming: 'bg-blue-400', live: 'bg-green-500', finished: 'bg-gray-400' }[s] ?? 'bg-gray-300' }
function statusClass(s: string) {
  return {
    upcoming: 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
    live: 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400',
    finished: 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400',
  }[s] ?? ''
}
function fmtDate(d: string) { return new Date(d).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) }

const { form, questions, addQuestion, removeQuestion, addAnswer, removeAnswer, setCorrectSingle, toggleCorrectMultiple, buildPayload } = useOlympiadForm()

const { data: olympiad, pending } = await useAsyncData(`olympiad-${route.params.id}`, () => api.get<any>(`/admin/olympiads/${route.params.id}`).then((r: any) => r.data))

watch(olympiad, (o) => {
  if (!o) return
  form.name_uz = o.name_uz
  form.type = o.type
  form.lang = o.lang ?? 'uz'
  form.description_uz = o.description_uz ?? ''
  form.starts_at = o.starts_at ? new Date(o.starts_at).toISOString().slice(0, 16) : ''
  form.ends_at = o.ends_at ? new Date(o.ends_at).toISOString().slice(0, 16) : ''
  form.duration = o.duration
  form.pass_score = o.pass_score ?? 60
  form.max_attempts = o.max_attempts ?? 1
  form.questions_count = o.questions_count ?? null
  form.show_answers = o.show_answers
  form.shuffle_questions = o.shuffle_questions
  form.shuffle_options = o.shuffle_options
  form.is_active = o.is_active
  questions.value = (o.questions ?? []).map((q: any) => ({
    id: q.id, question: q.question, type: q.type ?? 'single',
    difficulty: q.difficulty ?? 'medium', order: q.order ?? 0,
    answers: (q.answers ?? []).map((a: any) => ({ id: a.id, answer: a.answer, is_correct: a.is_correct })),
  }))
}, { immediate: true })

function onImportFile(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (file) { importFile.value = file; importResult.value = null }
}

async function doImport() {
  if (!importFile.value) return
  importing.value = true
  try {
    const fd = new FormData()
    fd.append('file', importFile.value)
    const res: any = await $fetch(`${apiBase}/admin/olympiads/${route.params.id}/import-questions`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token.value}` },
      body: fd,
    })
    toast.add({ title: 'Muvaffaqiyatli', color: 'success' })
    importModal.value = false
    const refreshed: any = await api.get(`/admin/olympiads/${route.params.id}`)
    questions.value = refreshed.data.questions
  } catch (e: any) {
    toast.add({ title: 'Xatolik', color: 'error' })
  } finally { importing.value = false }
}

async function save() {
  saving.value = true
  try {
    await api.put(`/admin/olympiads/${route.params.id}`, buildPayload())
    toast.add({ title: 'Saqlandi', color: 'success' })
    router.push('/olympiads')
  } catch (e: any) {
    toast.add({ title: 'Xatolik', color: 'error' })
  } finally { saving.value = false }
}

const outside = (e: MouseEvent) => { if (addQWrapRef.value && !addQWrapRef.value.contains(e.target as Node)) addQOpen.value = false }
onMounted(() => document.addEventListener('click', outside))
onBeforeUnmount(() => document.removeEventListener('click', outside))
</script>

<style scoped>
.dd-enter-active, .dd-leave-active { transition: all 0.15s ease; }
.dd-enter-from, .dd-leave-to { opacity: 0; transform: translateY(-4px); }
</style>