<template>
  <NuxtLayout name="admin">
    <div class="max-w-6xl mx-auto pb-20 px-4">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
        <div class="flex items-center gap-4">
          <NuxtLink to="/olympiads">
            <UButton icon="i-heroicons-arrow-left" color="neutral" variant="ghost" class="rounded-full hover:bg-gray-100 dark:hover:bg-gray-800" />
          </NuxtLink>
          <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Olimpiada yaratish</h1>
            <p class="text-sm text-gray-500">Sozlamalar, vaqt va savollarni boshqarish</p>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <UButton variant="soft" color="neutral" label="Bekor qilish" to="/olympiads" />
          <UButton color="primary" icon="i-heroicons-paper-airplane" :loading="saving" size="lg" class="shadow-lg shadow-primary-500/20" @click="save">
            Saqlash va chop etish
          </UButton>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-8 space-y-8">

          <!-- Asosiy ma'lumotlar -->
          <UCard class="border-none shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 rounded-2xl">
            <template #header>
              <div class="flex items-center gap-2">
                <UIcon name="i-heroicons-document-text" class="w-5 h-5 text-primary-500" />
                <span class="font-bold text-gray-900 dark:text-white">Asosiy ma'lumotlar</span>
              </div>
            </template>
            <div class="space-y-6">
              <UFormField label="Olimpiada nomi" required>
                <UInput v-model="form.name_uz" placeholder="Masalan: Matematika fanidan viloyat bosqichi" size="xl" class="w-full" />
              </UFormField>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <UFormField label="Turi">
                  <USelectMenu
                    v-model="form.type"
                    :options="typeOptions"
                    value-attribute="value"
                    option-attribute="label"
                    size="lg"
                    class="w-full"
                  >
                    <template #leading>
                      <UIcon
                        :name="typeOptions.find(t => t.value === form.type)?.icon || 'i-heroicons-question-mark-circle'"
                        class="w-4 h-4"
                      />
                    </template>
                    <template #option="{ option }">
                      <div class="flex items-center gap-2">
                        <UIcon :name="option.icon" class="w-4 h-4 text-gray-400" />
                        <span class="truncate">{{ option.label }}</span>
                      </div>
                    </template>
                  </USelectMenu>
                </UFormField>

                <UFormField label="Tili">
                  <div class="flex p-1 bg-gray-100 dark:bg-gray-800 rounded-xl">
                    <button v-for="l in ['uz', 'ru', 'en']" :key="l" type="button"
                      class="flex-1 py-2 rounded-lg text-xs font-bold uppercase transition-all"
                      :class="form.lang === l ? 'bg-white dark:bg-gray-700 shadow-sm text-primary-600' : 'text-gray-400'"
                      @click="form.lang = l">
                      {{ l }}
                    </button>
                  </div>
                </UFormField>
              </div>

              <UFormField label="Tavsif (Qoidalar va talablar)">
                <RichEditor v-model="form.description_uz" class="min-h-[150px]" />
              </UFormField>
            </div>
          </UCard>

          <!-- Vaqt va davomiylik -->
          <UCard class="border-none shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 rounded-2xl">
            <template #header>
              <div class="flex items-center gap-2">
                <UIcon name="i-heroicons-calendar" class="w-5 h-5 text-blue-500" />
                <span class="font-bold text-gray-900 dark:text-white">Vaqt va davomiylik</span>
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
                <UInput v-model.number="form.duration" type="number" icon="i-heroicons-clock" />
              </UFormField>
              <UFormField label="Urinishlar soni">
                <UInput v-model.number="form.max_attempts" type="number" icon="i-heroicons-arrow-path" />
              </UFormField>
              <UFormField label="Random savollar soni" hint="Bo'sh qoldirilsa — barcha savollar ko'rsatiladi">
                <UInput
                  v-model.number="form.questions_count"
                  type="number"
                  icon="i-heroicons-sparkles"
                  placeholder="Masalan: 20"
                  :min="1"
                />
              </UFormField>
            </div>
          </UCard>

          <!-- Savollar -->
          <div class="space-y-4">
            <div class="flex items-center justify-between px-2">
              <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                Savollar
                <UBadge :label="questions.length" color="neutral" variant="subtle" class="rounded-full" />
              </h3>
              <div class="flex gap-2">
                <UButton icon="i-heroicons-cloud-arrow-up" label="Import" variant="soft" color="neutral" @click="importModal = true" />
                <div class="relative" ref="addQWrapRef">
                  <UButton icon="i-heroicons-plus" label="Savol qo'shish" color="primary" @click="addQOpen = !addQOpen" />
                  <Transition name="dd">
                    <div v-if="addQOpen" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-2xl z-50 py-1">
                      <button v-for="qt in questionTypes" :key="qt.value" @click="addQuestion(qt.value); addQOpen = false"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        <UIcon :name="qt.icon" class="w-4 h-4 text-gray-400" /> {{ qt.label }}
                      </button>
                    </div>
                  </Transition>
                </div>
              </div>
            </div>

            <div v-for="(q, qi) in questions" :key="qi" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl overflow-hidden shadow-sm">
              <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-4">
                  <span class="w-8 h-8 rounded-xl bg-primary-600 text-white flex items-center justify-center font-bold text-sm shadow-md shadow-primary-500/20">
                    {{ qi + 1 }}
                  </span>
                  <div class="flex p-0.5 bg-gray-200/50 dark:bg-gray-800 rounded-lg">
                    <button v-for="qt in questionTypes" :key="qt.value" @click="q.type = qt.value"
                      class="px-3 py-1 text-[10px] font-bold uppercase rounded-md transition-all"
                      :class="q.type === qt.value ? 'bg-white dark:bg-gray-700 text-primary-600 shadow-sm' : 'text-gray-500'">
                      {{ qt.label }}
                    </button>
                  </div>
                </div>
                <UButton icon="i-heroicons-trash" color="error" variant="ghost" size="sm" class="rounded-full" @click="removeQuestion(qi)" />
              </div>

              <div class="p-6 space-y-6">
                <UFormField label="Savol sharti" required>
                  <RichEditor v-model="q.question" placeholder="Savol matnini bu yerga yozing..." />
                </UFormField>

                <div v-if="q.type !== 'open'" class="space-y-3">
                  <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Javob variantlari</span>
                    <UButton label="Variant qo'shish" variant="link" size="xs" icon="i-heroicons-plus-circle" @click="addAnswer(qi)" />
                  </div>
                  <div v-for="(a, ai) in q.answers" :key="ai" class="flex items-center gap-3">
                    <button type="button"
                      class="w-6 h-6 shrink-0 flex items-center justify-center border-2 transition-all"
                      :class="[q.type === 'multiple' ? 'rounded-md' : 'rounded-full', a.is_correct ? 'bg-primary-500 border-primary-500 text-white shadow-md' : 'border-gray-200 dark:border-gray-700']"
                      @click="q.type === 'multiple' ? toggleCorrectMultiple(qi, ai) : setCorrectSingle(qi, ai)">
                      <UIcon v-if="a.is_correct" name="i-heroicons-check" class="w-4 h-4" />
                    </button>
                    <UInput v-model="a.answer" placeholder="Javob..." class="flex-1" size="md" />
                    <UButton v-if="q.answers.length > 2" icon="i-heroicons-x-mark" color="neutral" variant="ghost" size="xs" @click="removeAnswer(qi, ai)" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- O'ng panel: Sozlamalar -->
        <div class="lg:col-span-4 space-y-6">
          <UCard class="sticky top-6 border-none shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 rounded-2xl">
            <template #header>
              <div class="flex items-center gap-2">
                <UIcon name="i-heroicons-adjustments-horizontal" class="w-5 h-5 text-gray-500" />
                <span class="font-bold text-gray-900 dark:text-white">Sozlamalar</span>
              </div>
            </template>
            <div class="space-y-6">
              <div class="space-y-4">
                <div v-for="opt in statusSwitches" :key="opt.key"
                  class="flex items-center justify-between p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 hover:border-primary-200 transition-colors">
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
                  <span class="text-sm font-black text-primary-600">{{ form.pass_score }}%</span>
                </div>
                <input v-model.number="form.pass_score" type="range" min="0" max="100" step="5" class="w-full h-1.5 rounded-full accent-primary-500 bg-gray-200 dark:bg-gray-700" />
              </div>

              <div class="space-y-3">
                <span class="text-xs font-bold text-gray-400 uppercase px-1">Natijalarni ko'rsatish</span>
                <div class="grid grid-cols-1 gap-2">
                  <button v-for="status in showAnswerStatuses" :key="status.value" @click="form.show_answers = status.value"
                    class="flex items-center justify-between p-3 rounded-xl border text-left transition-all group"
                    :class="form.show_answers === status.value ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-500 ring-1 ring-primary-500' : 'border-gray-200 dark:border-gray-800'">
                    <div class="flex flex-col">
                      <span class="text-xs font-bold" :class="form.show_answers === status.value ? 'text-primary-700' : 'text-gray-600'">{{ status.label }}</span>
                      <span class="text-[9px] text-gray-500">{{ status.desc }}</span>
                    </div>
                    <UIcon v-if="form.show_answers === status.value" name="i-heroicons-check-circle-solid" class="w-4 h-4 text-primary-600" />
                  </button>
                </div>
              </div>
            </div>
          </UCard>
        </div>
      </div>
    </div>

    <!-- Import modal -->
    <UModal v-model:open="importModal">
      <UCard class="border-none">
        <template #header>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center">
              <UIcon name="i-heroicons-document-arrow-up" class="w-6 h-6 text-primary-500" />
            </div>
            <h3 class="font-bold">Fayldan import qilish</h3>
          </div>
        </template>
        <div class="space-y-4">
          <div @click="importInputRef?.click()"
            class="border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-2xl p-10 text-center hover:border-primary-500 hover:bg-primary-50/50 transition-all cursor-pointer">
            <input ref="importInputRef" type="file" accept=".xlsx,.xls,.csv,.txt" class="hidden" @change="onImportFile" />
            <UIcon :name="importFile ? 'i-heroicons-check-badge' : 'i-heroicons-arrow-up-on-square-stack'" class="w-12 h-12 mx-auto mb-4" :class="importFile ? 'text-green-500' : 'text-gray-300'" />
            <p class="text-sm font-bold text-gray-700">{{ importFile ? importFile.name : 'Faylni tanlang' }}</p>
            <p class="text-xs text-gray-400 mt-1">Excel yoki CSV formatlari qo'llab-quvvatlanadi</p>
          </div>
        </div>
        <template #footer>
          <div class="flex justify-end gap-2">
            <UButton label="Bekor qilish" variant="ghost" @click="importModal = false" />
            <UButton label="Yuklash" icon="i-heroicons-cloud-arrow-up" :disabled="!importFile" block @click="previewImport" />
          </div>
        </template>
      </UCard>
    </UModal>
  </NuxtLayout>
</template>

<script setup lang="ts">
const api = useApi()
const toast = useToast()
const router = useRouter()
const config = useRuntimeConfig()

const saving = ref(false)
const importModal = ref(false)
const importFile = ref<File | null>(null)
const importInputRef = ref<HTMLInputElement | null>(null)
const addQOpen = ref(false)
const addQWrapRef = ref<HTMLElement | null>(null)

const typeOptions = [
  { label: 'Quiz (Oddiy test)', value: 'quiz', icon: 'i-heroicons-academic-cap' },
  { label: 'Olimpiada', value: 'olympiad', icon: 'i-heroicons-trophy' },
  { label: 'Imtihon', value: 'exam', icon: 'i-heroicons-clipboard-document-check' },
]

const questionTypes = [
  { label: 'Yagona tanlov', value: 'single', icon: 'i-heroicons-check-circle' },
  { label: 'Ko\'p tanlovli', value: 'multiple', icon: 'i-heroicons-check-badge' },
  { label: 'Ochiq savol', value: 'open', icon: 'i-heroicons-pencil-square' },
]

const statusSwitches = [
  { key: 'is_active', label: 'Public Access', hint: 'Olimpiadani hamma ko\'ra oladi' },
  { key: 'shuffle_questions', label: 'Shuffle Questions', hint: 'Savollar tartibini aralashtirish' },
  { key: 'shuffle_options', label: 'Shuffle Options', hint: 'Javoblar tartibini aralashtirish' },
]

const showAnswerStatuses = [
  { label: 'Ko\'rsatmaslik', value: 'never', desc: 'To\'g\'ri javoblar hech qachon chiqmaydi' },
  { label: 'Darhol', value: 'after_submission', desc: 'Test tugashi bilan javoblar chiqadi' },
  { label: 'Olimpiada yakunida', value: 'after_finish', desc: 'Olimpiada umumiy vaqti tugagach chiqadi' },
]

const {
  form, questions, addQuestion, removeQuestion, addAnswer, removeAnswer,
  setCorrectSingle, toggleCorrectMultiple, buildPayload,
} = useOlympiadForm()

function onImportFile(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (file) importFile.value = file
}

function previewImport() {
  importModal.value = false
  toast.add({ title: 'Fayl biriktirildi', description: 'Saqlash tugmasini bosganingizda import boshlanadi', color: 'info' })
}

async function save() {
  // 1. Asosiy maydonlar tekshiruvi
  if (!form.name_uz.trim()) {
    toast.add({ title: 'Xatolik', description: 'Olimpiada nomini kiriting', color: 'error' })
    return
  }
  if (!form.starts_at || !form.ends_at) {
    toast.add({ title: 'Xatolik', description: 'Boshlanish va tugash vaqtlarini kiriting', color: 'error' })
    return
  }
  if (new Date(form.ends_at) <= new Date(form.starts_at)) {
    toast.add({ title: 'Xatolik', description: 'Tugash vaqti boshlanish vaqtidan keyin bo\'lishi kerak', color: 'error' })
    return
  }
  if (!form.duration || form.duration < 1) {
    toast.add({ title: 'Xatolik', description: 'Davomiylikni kiriting (daqiqada)', color: 'error' })
    return
  }

  // 2. Savollar tekshiruvi
  for (let i = 0; i < questions.value.length; i++) {
    const q = questions.value[i]
    const questionText = q.question?.replace(/<[^>]*>/g, '').trim()

    if (!questionText) {
      toast.add({ title: `Savol ${i + 1} bo'sh`, description: 'Savol matnini kiriting', color: 'error' })
      return
    }

    if (q.type !== 'open') {
      const filledAnswers = q.answers.filter(a => a.answer?.trim())
      if (filledAnswers.length < 2) {
        toast.add({ title: `Savol ${i + 1}`, description: 'Kamida 2 ta javob varianti bo\'lishi kerak', color: 'error' })
        return
      }
      const hasCorrect = q.answers.some(a => a.is_correct)
      if (!hasCorrect) {
        toast.add({ title: `Savol ${i + 1}`, description: 'Kamida bitta to\'g\'ri javob belgilang', color: 'error' })
        return
      }
    }
  }

  // 3. questions_count tekshiruvi
  if (form.questions_count && form.questions_count > questions.value.length) {
    toast.add({
      title: 'Xatolik',
      description: `Random savollar soni (${form.questions_count}) umumiy savollar sonidan (${questions.value.length}) ko'p bo'lishi mumkin emas`,
      color: 'error',
    })
    return
  }

  saving.value = true
  try {
    const res: any = await api.post('/admin/olympiads', buildPayload())
    const id = res.id || res.data?.id

    if (importFile.value && id) {
      const fd = new FormData()
      fd.append('file', importFile.value)
      const token = useCookie('auth_token')
      await $fetch(`${config.public.apiBase}/admin/olympiads/${id}/import-questions`, {
        method: 'POST',
        headers: { Authorization: `Bearer ${token.value}` },
        body: fd,
      })
    }

    toast.add({ title: 'Muvaffaqiyatli saqlandi!', color: 'success', icon: 'i-heroicons-check-circle' })
    router.push('/olympiads')
  } catch (e: any) {
    const errors = e?.data?.errors
    if (errors) {
      const firstError = Object.values(errors)[0]
      const message = Array.isArray(firstError) ? firstError[0] : String(firstError)
      toast.add({ title: 'Xatolik', description: message, color: 'error' })
    } else {
      toast.add({ title: 'Xatolik yuz berdi', description: e?.data?.message || 'Serverda xatolik', color: 'error' })
    }
  } finally {
    saving.value = false
  }
}

const outside = (e: MouseEvent) => {
  if (addQWrapRef.value && !addQWrapRef.value.contains(e.target as Node)) addQOpen.value = false
}
onMounted(() => document.addEventListener('click', outside))
onBeforeUnmount(() => document.removeEventListener('click', outside))
</script>

<style scoped>
.dd-enter-active, .dd-leave-active { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
.dd-enter-from, .dd-leave-to { opacity: 0; transform: translateY(-8px) scale(0.98); }
</style>