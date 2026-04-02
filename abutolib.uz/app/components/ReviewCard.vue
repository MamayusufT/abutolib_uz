<template>
  <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 transition-colors hover:border-gray-700">

    <!-- Top row -->
    <div class="flex items-start justify-between gap-3">
      <div class="flex items-center gap-3">
        <!-- Avatar -->
        <div class="w-9 h-9 rounded-full bg-indigo-900/60 border border-indigo-800 flex items-center justify-center shrink-0">
          <span class="text-indigo-300 text-sm font-semibold">{{ initials }}</span>
        </div>

        <div>
          <p class="text-white text-sm font-medium leading-none mb-1">
            {{ review.user?.name || 'Foydalanuvchi' }}
          </p>
          <!-- Stars -->
          <div class="flex items-center gap-0.5">
            <Star
              v-for="i in 5"
              :key="i"
              :size="11"
              :class="i <= review.rating ? 'text-amber-400' : 'text-gray-700'"
              :fill="i <= review.rating ? 'currentColor' : 'none'"
            />
            <span class="text-gray-500 text-xs ml-1">{{ review.rating }}/5</span>
          </div>
        </div>
      </div>

      <!-- Right: date + actions -->
      <div class="flex items-center gap-1 shrink-0">
        <span class="text-gray-600 text-xs mr-1">{{ formatDate(review.created_at) }}</span>

        <template v-if="canEdit">
          <button
            class="p-1.5 text-gray-600 hover:text-indigo-400 rounded transition-colors"
            @click="$emit('edit', review)"
          >
            <Pencil :size="13" />
          </button>
        </template>

        <template v-if="canDelete">
          <button
            class="p-1.5 text-gray-600 hover:text-rose-400 rounded transition-colors"
            @click="$emit('delete', review)"
          >
            <Trash2 :size="13" />
          </button>
        </template>
      </div>
    </div>

    <!-- Comment -->
    <p v-if="review.comment" class="text-gray-400 text-sm leading-relaxed mt-3">
      {{ review.comment }}
    </p>
    <p v-else class="text-gray-600 text-xs italic mt-3">Izoh qoldirilmagan</p>

    <!-- Footer -->
    <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-800">
      <button
        :class="[
          'flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-lg border transition-colors',
          markedHelpful
            ? 'bg-indigo-900/40 text-indigo-400 border-indigo-800'
            : 'bg-transparent text-gray-500 border-gray-800 hover:text-indigo-400 hover:border-indigo-800'
        ]"
        @click="onHelpful"
      >
        <ThumbsUp :size="12" :fill="markedHelpful ? 'currentColor' : 'none'" />
        Foydali
        <span class="text-gray-500 ml-0.5">({{ localHelpfulCount }})</span>
      </button>

      <!-- Verified badge if owner -->
      <span v-if="isOwner" class="flex items-center gap-1 text-xs text-emerald-500">
        <BadgeCheck :size="13" />
        Sizning sharhingiz
      </span>
    </div>

  </div>
</template>

<script setup>
import { Star, Pencil, Trash2, ThumbsUp, BadgeCheck } from 'lucide-vue-next'

const props = defineProps({
  review: {
    type: Object,
    required: true,
  },
  currentUserId: {
    type: [Number, String],
    default: null,
  },
  isAdmin: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['edit', 'delete', 'helpful'])

const markedHelpful = ref(false)
const localHelpfulCount = ref(props.review.helpful_count || 0)

const isOwner = computed(() => props.currentUserId && props.review.user?.id === props.currentUserId)
const canEdit = computed(() => isOwner.value)
const canDelete = computed(() => isOwner.value || props.isAdmin)

const initials = computed(() => {
  const name = props.review.user?.name || 'U'
  return name.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase()
})

function onHelpful() {
  if (markedHelpful.value) return
  markedHelpful.value = true
  localHelpfulCount.value++
  emit('helpful', props.review)
}

function formatDate(d) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('uz-UZ', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}
</script>