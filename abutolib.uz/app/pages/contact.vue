<script setup lang="ts">
const config = useRuntimeConfig()


useSeoMeta({
  title: 'Biz bilan bog‘lanish',
  ogTitle: 'Biz bilan bog‘lanish | Test Platformasi',
  description: 'Savollaringiz bormi? Biz bilan telefon, Telegram yoki elektron pochta orqali bog‘laning. Bizning jamoamiz sizga yordam berishga tayyor.',
  ogDescription: 'Sizni qiziqtirgan barcha savollar bo‘yicha biz bilan aloqaga chiqing.',
  ogImage: `${config.public.siteUrl}/og-contact.jpg`,
  ogType: 'website',
  twitterCard: 'summary'
})

useHead({
  link: [
    { rel: 'canonical', href: () => `${config.public.siteUrl}/contact` }
  ],
  script: [
    {
      type: 'application/ld+json',
      children: JSON.stringify({
        "@context": "https://schema.org",
        "@type": "ContactPage",
        "name": "Bog'lanish sahifasi",
        "description": "Test platformasi ma'muriyati bilan bog'lanish uchun aloqa ma'lumotlari.",
        "mainEntity": {
          "@type": "Organization",
          "name": config.public.siteName,
          "url": config.public.siteUrl,
          "logo": `${config.public.siteUrl}/logo.png`,
          "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+998-XX-XXX-XX-XX",
            "contactType": "customer service",
            "areaServed": "UZ",
            "availableLanguage": ["Uzbek", "Russian", "English"]
          },
          "sameAs": [
            "https://t.me/username",
            "https://instagram.com/username",
            "https://facebook.com/username"
          ]
        }
      })
    }
  ]
})

const isSending = ref(false)
const isSuccess = ref(false)
const errorMsg  = ref<string | null>(null)

const form = reactive({
  name:    '',
  email:   '',
  phone:   '',
  subject: '',
  message: '',
})

const subjects = [
  'Umumiy savol',
  'Texnik yordam',
  'Hamkorlik taklifi',
  'Shikoyat',
  'Boshqa',
]

async function submit() {
  isSending.value = true
  errorMsg.value  = null
  try {
    await $fetch(`${config.public.apiBase}/contact`, {
      method: 'POST',
      body: { ...form },
      headers: { Accept: 'application/json' },
    })
    isSuccess.value = true
    Object.assign(form, { name: '', email: '', phone: '', subject: '', message: '' })
  } catch (err: any) {
    const errors = err?.data?.errors
    if (errors) {
      const first = Object.values(errors)[0] as string[]
      errorMsg.value = first[0]
    } else {
      errorMsg.value = err?.data?.message || 'Xabar yuborishda xatolik yuz berdi'
    }
  } finally {
    isSending.value = false
  }
}
</script>

<template>
  <div class="max-w-7xl mx-auto py-8 px-4">
    <!-- Page header -->
    <div class="mx-auto text-center mb-10">
      <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-600 rounded-2xl shadow-lg shadow-blue-200 dark:shadow-none mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
        </svg>
      </div>
      <h1 class="text-3xl font-black text-slate-800 dark:text-white">Bog'lanish</h1>
      <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">Savol yoki takliflaringiz bo'lsa, bizga yozing. 24 soat ichida javob beramiz.</p>
    </div>

    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">

      <!-- Contact info -->
      <div class="space-y-4">

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-3">
          <h2 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider mb-4">Aloqa ma'lumotlari</h2>
          <div class="space-y-4">

            <div class="flex items-start gap-3">
              <div class="w-9 h-9 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
              </div>
              <div>
                <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide">Email</div>
                <a href="mailto:info@abutolib.uz" class="text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">info@abutolib.uz</a>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <div class="w-9 h-9 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                </svg>
              </div>
              <div>
                <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide">Telefon</div>
                <a href="tel:+998901234567" class="text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">+998 90 123 45 67</a>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <div class="w-9 h-9 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                </svg>
              </div>
              <div>
                <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide">Manzil</div>
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">Toshkent, O'zbekiston</span>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <div class="w-9 h-9 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div>
                <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide">Ish vaqti</div>
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">Dush–Juma, 9:00–18:00</span>
              </div>
            </div>

          </div>
        </div>

        <!-- Social links -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-3">
          <h2 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider mb-4">Ijtimoiy tarmoqlar</h2>
          <div class="flex flex-wrap gap-2">
            <a href="#" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold bg-sky-50 dark:bg-sky-900/20 text-sky-600 dark:text-sky-400 hover:bg-sky-100 dark:hover:bg-sky-900/30 transition-colors">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
              Telegram
            </a>
            <a href="#" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold bg-pink-50 dark:bg-pink-900/20 text-pink-600 dark:text-pink-400 hover:bg-pink-100 dark:hover:bg-pink-900/30 transition-colors">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>
              Instagram
            </a>
            <a href="#" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
              LinkedIn
            </a>
          </div>
        </div>

      </div>

      <!-- Form -->
      <div class="lg:col-span-2">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">

          <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 p-3">
            <h2 class="text-base font-black text-slate-800 dark:text-white">Xabar yuborish</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Barcha maydonlarni to'ldiring</p>
          </div>

          <!-- Success -->
          <Transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100">
            <div v-if="isSuccess" class="m-6 p-5 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-2xl text-center">
              <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600 dark:text-green-400" viewBox="0 0 24 24" fill="currentColor">
                  <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/>
                </svg>
              </div>
              <h3 class="text-base font-black text-green-700 dark:text-green-400">Xabar yuborildi!</h3>
              <p class="text-sm text-green-600 dark:text-green-500 mt-1">Tez orada siz bilan bog'lanamiz.</p>
              <button class="mt-4 px-4 py-2 text-sm font-semibold text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/30 rounded-xl hover:bg-green-200 dark:hover:bg-green-900/40 transition-colors" @click="isSuccess = false">
                Yana yuborish
              </button>
            </div>
          </Transition>

          <form v-if="!isSuccess" class="p-6 space-y-4" @submit.prevent="submit">

            <!-- Error -->
            <div v-if="errorMsg" class="flex items-center gap-2 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-600 dark:text-red-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd"/>
              </svg>
              {{ errorMsg }}
            </div>

            <!-- Name & Email -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                  Ism Familiya <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.name"
                  type="text"
                  required
                  placeholder="Abdulloh Karimov"
                  class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800 text-slate-800 dark:text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                />
              </div>
              <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                  Email <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.email"
                  type="email"
                  required
                  placeholder="example@mail.com"
                  class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800 text-slate-800 dark:text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                />
              </div>
            </div>

            <!-- Phone & Subject -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                  Telefon <span class="text-slate-400 font-normal">(ixtiyoriy)</span>
                </label>
                <input
                  v-model="form.phone"
                  type="tel"
                  placeholder="+998 90 000 00 00"
                  class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800 text-slate-800 dark:text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                />
              </div>
              <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                  Mavzu <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="form.subject"
                  required
                  class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition appearance-none cursor-pointer"
                >
                  <option value="" disabled>Mavzuni tanlang</option>
                  <option v-for="s in subjects" :key="s" :value="s">{{ s }}</option>
                </select>
              </div>
            </div>

            <!-- Message -->
            <div>
              <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                Xabar <span class="text-red-500">*</span>
              </label>
              <textarea
                v-model="form.message"
                required
                rows="5"
                minlength="10"
                maxlength="2000"
                placeholder="Xabaringizni yozing..."
                class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800 text-slate-800 dark:text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
              />
              <div class="flex justify-end mt-1">
                <span class="text-xs text-slate-400">{{ form.message.length }}/2000</span>
              </div>
            </div>

            <!-- Submit -->
            <button
              type="submit"
              :disabled="isSending"
              class="w-full py-3 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed text-white rounded-xl font-bold text-sm shadow-md shadow-blue-200 dark:shadow-none transition-all active:scale-95 flex items-center justify-center gap-2"
            >
              <svg v-if="isSending" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
              </svg>
              {{ isSending ? 'Yuborilmoqda...' : 'Xabar yuborish' }}
            </button>

          </form>
        </div>
      </div>

    </div>
  </div>
</template>