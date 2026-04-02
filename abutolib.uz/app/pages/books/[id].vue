<template>
  <div class="min-h-screen text-slate-900 bg-slate-50 dark:bg-slate-950 dark:text-slate-100 transition-colors duration-300">
    
    <div v-if="loading" class="max-w-6xl mx-auto px-4 py-10 animate-pulse">
      <div class="flex flex-col md:flex-row gap-10">
        <div class="w-56 h-72 bg-slate-200 dark:bg-slate-800 rounded-2xl shrink-0" />
        <div class="flex-1 space-y-4">
          <div class="h-10 bg-slate-200 dark:bg-slate-800 rounded-xl w-2/3" />
          <div class="h-6 bg-slate-200 dark:bg-slate-800 rounded-lg w-1/3" />
          <div class="h-32 bg-slate-200 dark:bg-slate-800 rounded-2xl" />
        </div>
      </div>
    </div>

    <div v-else-if="book" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

      <NuxtLink to="/books" class="inline-flex items-center gap-2 text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 text-sm mb-8 font-medium transition-colors group">
        <ArrowLeft :size="18" class="group-hover:-translate-x-1 transition-transform" />
        Orqaga qaytish
      </NuxtLink>

      <div class="flex flex-col md:flex-row gap-10 mb-12">

        <div class="shrink-0 flex flex-col items-center md:items-start">
          <div class="relative w-56 h-72 rounded-2xl overflow-hidden bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-none">
            <img
              v-if="book.cover_image"
              :src="book.cover_image"
              :alt="book.title"
              class="w-full h-full object-cover"
            />
            <div v-else class="w-full h-full flex items-center justify-center bg-slate-100 dark:bg-slate-800">
              <BookOpen :size="64" class="text-slate-300 dark:text-slate-600" />
            </div>
          </div>

          <div class="mt-6 grid grid-cols-2 gap-3 w-full">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 text-center shadow-sm">
              <Eye :size="18" class="mx-auto text-blue-500 mb-1" />
              <p class="text-slate-900 dark:text-white font-bold text-sm">{{ formatNum(book.view_count) }}</p>
              <p class="text-slate-500 text-xs">Ko'rishlar</p>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 text-center shadow-sm">
              <Download :size="18" class="mx-auto text-blue-500 mb-1" />
              <p class="text-slate-900 dark:text-white font-bold text-sm">{{ formatNum(book.download_count) }}</p>
              <p class="text-slate-500 text-xs">Yuklamalar</p>
            </div>
          </div>
        </div>

        <div class="flex-1">
          <div class="flex items-start justify-between gap-4">
            <div>
              <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white leading-tight mb-2">
                {{ book.title }}
              </h1>
              <p class="text-blue-600 dark:text-blue-400 text-lg font-semibold">{{ book.author }}</p>
            </div>
            <button
              class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm transition-all active:scale-95"
              :class="inWishlist ? 'text-rose-500 border-rose-100 dark:border-rose-900 bg-rose-50 dark:bg-rose-950' : 'text-slate-400 hover:text-rose-500'"
              @click="toggleWishlist"
            >
              <Heart :size="24" :fill="inWishlist ? 'currentColor' : 'none'" />
            </button>
          </div>

          <div class="flex items-center gap-2 mt-4 mb-6">
            <div class="flex items-center gap-0.5">
              <Star
                v-for="i in 5"
                :key="i"
                :size="18"
                :class="i <= Math.round(book.average_rating) ? 'text-amber-400' : 'text-slate-300 dark:text-slate-700'"
                :fill="i <= Math.round(book.average_rating) ? 'currentColor' : 'none'"
              />
            </div>
            <span class="text-slate-900 dark:text-white font-bold">{{ book.average_rating }}</span>
            <span class="text-slate-500 text-sm">({{ book.review_count }} sharh)</span>
          </div>

          <div class="flex flex-wrap gap-2 mb-8">
            <span v-if="book.language" class="flex items-center gap-1.5 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-full px-4 py-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400">
              <Globe :size="14" />
              {{ languageLabel(book.language) }}
            </span>
            <span v-if="book.pages" class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full px-4 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-400">
              <FileText :size="14" />
              {{ book.pages }} bet
            </span>
            <span v-if="book.isbn" class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full px-4 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-400">
              <Hash :size="14" />
              {{ book.isbn }}
            </span>
          </div>

          <div class="prose prose-slate dark:prose-invert max-w-none mb-10">
            <h3 class="text-lg font-bold mb-2">Kitob haqida</h3>
            <p class="text-slate-600 dark:text-slate-400 leading-relaxed" v-html="book.description" />
          </div>

          <div class="flex flex-wrap items-center gap-4 p-6 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex flex-col">
              <span class="text-slate-500 text-xs uppercase font-bold tracking-wider">Narxi</span>
              <span v-if="book.price > 0" class="text-2xl font-black text-slate-900 dark:text-white">
                {{ formatPrice(book.price) }}
              </span>
              <span v-else class="text-2xl font-black text-emerald-500">BEPUL</span>
            </div>

            <div class="flex flex-1 gap-3 justify-end">
              <button
                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-blue-200 dark:shadow-none transition-all active:scale-95"
                @click="downloadBook"
              >
                <Download :size="20" />
                Yuklab olish
              </button>

              <template v-if="isAdmin">
                <NuxtLink
                  :to="`/books/${book.id}/edit`"
                  class="p-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl hover:bg-slate-200 transition-colors"
                >
                  <Pencil :size="20" />
                </NuxtLink>
                <button
                  class="p-3 bg-rose-50 dark:bg-rose-900/20 text-rose-500 rounded-xl hover:bg-rose-100 transition-colors"
                  @click="deleteBook"
                >
                  <Trash2 :size="20" />
                </button>
              </template>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-16 border-t border-slate-200 dark:border-slate-800 pt-12">
        <div class="flex items-center justify-between mb-8">
          <h2 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
            <MessageSquare :size="24" class="text-blue-600" />
            Kitobxonlar fikri
            <span class="text-slate-400 text-lg font-medium">({{ book.review_count }})</span>
          </h2>
          <button
            v-if="authStore.isLoggedIn && !userReview"
            class="flex items-center gap-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-blue-500 text-slate-700 dark:text-slate-300 px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm transition-all"
            @click="showReviewForm = true"
          >
            <Plus :size="18" />
            Fikr qoldirish
          </button>
        </div>

        <div v-if="showReviewForm" class="bg-blue-50 dark:bg-slate-900/50 border border-blue-100 dark:border-blue-900 rounded-2xl p-6 mb-10 shadow-sm">
          <h3 class="text-slate-900 dark:text-white font-bold mb-4">Sizning bahoingiz</h3>
          <div class="flex items-center gap-1.5 mb-6">
            <button
              v-for="i in 5"
              :key="i"
              @click="newReview.rating = i"
              class="transition-transform hover:scale-125"
            >
              <Star
                :size="32"
                :class="i <= newReview.rating ? 'text-amber-400' : 'text-slate-300 dark:text-slate-700'"
                :fill="i <= newReview.rating ? 'currentColor' : 'none'"
              />
            </button>
          </div>
          <textarea
            v-model="newReview.comment"
            placeholder="Kitob haqida fikringizni yozing..."
            rows="4"
            class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none resize-none transition-all mb-4"
          />
          <div class="flex gap-3 justify-end">
            <button
              class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors"
              @click="showReviewForm = false"
            >
              Bekor qilish
            </button>
            <button
              class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-md transition-all active:scale-95"
              @click="submitReview"
            >
              Yuborish
            </button>
          </div>
        </div>

        <div v-if="reviewsLoading" class="space-y-4">
          <div v-for="i in 3" :key="i" class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-6 animate-pulse">
            <div class="h-12 bg-slate-100 dark:bg-slate-800 rounded-xl w-full" />
          </div>
        </div>

        <div v-else-if="reviews.length === 0" class="flex flex-col items-center py-20 text-center">
          <MessageSquare :size="48" class="text-slate-200 dark:text-slate-800 mb-4" />
          <p class="text-slate-500 dark:text-slate-400 font-medium">Ushbu kitobga hali sharh yozilmagan.</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div
            v-for="review in reviews"
            :key="review.id"
            class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm hover:border-blue-200 dark:hover:border-blue-900 transition-colors"
          >
            <div class="flex items-start justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center font-bold text-blue-600">
                  {{ review.user?.name?.charAt(0) || 'U' }}
                </div>
                <div>
                  <p class="text-slate-900 dark:text-white text-sm font-bold">{{ review.user?.name || 'Foydalanuvchi' }}</p>
                  <div class="flex items-center gap-0.5 mt-0.5">
                    <Star
                      v-for="i in 5"
                      :key="i"
                      :size="12"
                      :class="i <= review.rating ? 'text-amber-400' : 'text-slate-200 dark:text-slate-700'"
                      :fill="i <= review.rating ? 'currentColor' : 'none'"
                    />
                  </div>
                </div>
              </div>
              <span class="text-slate-400 text-xs font-medium">{{ formatDate(review.created_at) }}</span>
            </div>
            <p class="text-slate-600 dark:text-slate-400 text-sm mt-4 leading-relaxed italic">
              "{{ review.comment }}"
            </p>
            <div class="flex items-center justify-between mt-5 pt-4 border-t border-slate-50 dark:border-slate-800">
              <button class="flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-blue-600 transition-colors">
                <ThumbsUp :size="14" />
                Foydali
              </button>
              <button v-if="isAdmin" @click="deleteReview(review.id)" class="text-rose-400 hover:text-rose-600 transition-colors">
                <Trash2 :size="16" />
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>

    <div v-else class="flex flex-col items-center justify-center min-h-screen text-center px-4">
      <div class="w-24 h-24 bg-slate-100 dark:bg-slate-900 rounded-full flex items-center justify-center mb-6 text-slate-300">
        <BookX :size="48" />
      </div>
      <h2 class="text-2xl font-black text-slate-800 dark:text-white">Kitob topilmadi</h2>
      <p class="text-slate-500 mt-2 mb-8">Ushbu kitob o'chirilgan yoki manzilda xatolik bor.</p>
      <NuxtLink to="/books" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-blue-200 dark:shadow-none">
        Kutubxonaga qaytish
      </NuxtLink>
    </div>

  </div>
</template>

<script setup>
import {
  ArrowLeft, BookOpen, Eye, Download, Heart, Star, Globe,
  FileText, Hash, Pencil, Trash2, MessageSquare, Plus, User,
  ThumbsUp, BookX
} from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const { $api } = useApi()
const authStore = useAuthStore()

const book = ref(null)
const loading = ref(true)
const reviews = ref([])
const reviewsLoading = ref(false)
const inWishlist = ref(false)
const userReview = ref(null)
const showReviewForm = ref(false)
const isAdmin = computed(() => authStore.user?.role === 'admin')
const newReview = reactive({ rating: 0, comment: '' })

// SEO Meta
watchEffect(() => {
  if (book.value) {
    useSeoMeta({
      title: `${book.value.title} - ${book.value.author} | Abutolib Kutubxonasi`,
      ogTitle: book.value.title,
      description: book.value.description?.substring(0, 160),
      ogImage: book.value.cover_image,
      twitterCard: 'summary_large_image',
    })
  }
})

async function fetchBook() {
  loading.value = true
  try {
    book.value = await $api(`/books/${route.params.id}`).then(res => res.data)
  } catch {
    book.value = null
  } finally {
    loading.value = false
  }
}

async function fetchReviews() {
  reviewsLoading.value = true
  try {
    const data = await $api(`/books/${route.params.id}/reviews`)
    reviews.value = data.data || data
    if (authStore.isLoggedIn) {
      userReview.value = await $api(`/books/${route.params.id}/reviews/user`,
        {
          method: 'POST',
        }
      ).catch(() => null)
    }
  } catch {}
  reviewsLoading.value = false
}

async function checkWishlist() {
  if (!authStore.isLoggedIn) return
  try {
    const data = await $api(`/wishlists/${route.params.id}/check`)
    inWishlist.value = data.in_wishlist
  } catch {}
}

async function toggleWishlist() {
  if (!authStore.isLoggedIn) return navigateTo('/login')
  try {
    if (inWishlist.value) {
      await $api(`/wishlists/${route.params.id}`, { method: 'DELETE' })
      inWishlist.value = false
    } else {
      await $api('/wishlists', { method: 'POST', body: { book_id: route.params.id } })
      inWishlist.value = true
    }
  } catch {}
}

async function downloadBook() {
  if (!authStore.isLoggedIn) return navigateTo('/login')
  try {
    const data = await $api(`/books/${route.params.id}/download`)
    if (data?.url) window.open(data.url, '_blank')
  } catch {}
}

async function submitReview() {
  if (newReview.rating === 0) return
  try {
    await $api(`/books/${route.params.id}/reviews`, {
      method: 'POST',
      body: { rating: newReview.rating, comment: newReview.comment },
    })
    showReviewForm.value = false
    newReview.rating = 0
    newReview.comment = ''
    fetchReviews()
    fetchBook()
  } catch {}
}

async function deleteReview(id) {
  if(!confirm('Sharhni o\'chirmoqchimisiz?')) return
  try {
    await $api(`/reviews/${id}`, { method: 'DELETE' })
    fetchReviews()
    fetchBook()
  } catch {}
}

async function deleteBook() {
  if (!confirm('Ushbu kitobni butunlay o\'chirib tashlaysizmi?')) return
  try {
    await $api(`/books/${route.params.id}`, { method: 'DELETE' })
    router.push('/books')
  } catch {}
}

const formatNum = (n) => n >= 1000 ? (n / 1000).toFixed(1) + 'K' : n || 0
const formatPrice = (p) => new Intl.NumberFormat('uz-UZ').format(p) + " so'm"
const formatDate = (d) => d ? new Date(d).toLocaleDateString('uz-UZ', { year: 'numeric', month: 'short', day: 'numeric' }) : ''
const languageLabel = (l) => ({ uz: "O'zbekcha", ru: 'Ruscha', en: 'Inglizcha' }[l] || l)

onMounted(() => {
  fetchBook()
  fetchReviews()
  checkWishlist()
})
</script>