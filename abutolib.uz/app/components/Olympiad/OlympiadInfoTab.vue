<script setup lang="ts">
import {
  Play, Lock, Globe, Users,
  UserCheck, UserPlus, UserMinus, CheckCircle2,
  Triangle
} from 'lucide-vue-next'

defineProps<{
  olympiad:       any
  myResult:       any
  canStart:       boolean
  isRegistered:   boolean
  regLoading:     boolean
  status:         'upcoming' | 'active' | 'ended'
  countdownUnits: { value: string; label: string }[]
  formatDate:     (s: string) => string
}>()

const emit = defineEmits<{
  (e: 'start'): void
  (e: 'register'): void
  (e: 'unregister'): void
}>()

</script>

<template>
  <div class="space-y-4">
    <!-- Countdown -->
    <div v-if="status !== 'ended'" class="rounded-2xl overflow-hidden">
      <div class="p-6 text-white text-center"
        :class="status === 'upcoming'
          ? 'bg-gradient-to-br from-blue-600 to-indigo-600'
          : 'bg-gradient-to-br from-emerald-600 to-teal-600'">
        <p class="text-xs font-bold uppercase tracking-widest mb-4 opacity-75">
          {{ status === 'upcoming' ? 'Boshlanishiga' : 'Tugashiga' }}
        </p>
        <div class="flex items-end justify-center gap-2">
          <template v-for="(unit, i) in countdownUnits" :key="i">
            <div class="text-center">
              <div class="bg-white/20 backdrop-blur-sm rounded-2xl px-5 py-3 min-w-[68px]">
                <div class="text-4xl font-black tabular-nums leading-none">{{ unit.value }}</div>
              </div>
              <div class="text-[11px] font-bold uppercase tracking-widest mt-2 opacity-70">{{ unit.label }}</div>
            </div>
            <div v-if="i < countdownUnits.length - 1" class="text-3xl font-black opacity-40 mb-8">:</div>
          </template>
        </div>
      </div>
    </div>
    <div v-else class="bg-gray-100 dark:bg-slate-800 rounded-2xl p-5 text-center">
      <p class="text-slate-400 font-bold">Olimpiada tugagan</p>
    </div>

    <!-- Info table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
      <table class="w-full">
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">

          <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
            <td class="px-5 py-3.5 w-44 text-sm font-black text-slate-500 dark:text-slate-400 whitespace-nowrap">Holati</td>
            <td class="px-5 py-3.5">
              <span class="text-sm font-bold"
                :class="{
                  'text-emerald-600 dark:text-emerald-400': status === 'active',
                  'text-blue-600 dark:text-blue-400':       status === 'upcoming',
                  'text-red-500 dark:text-red-400':         status === 'ended',
                }">
                {{ { active: '🟢 Faol', upcoming: '🔵 Kutilmoqda', ended: '🔴 Tugagan' }[status] }}
              </span>
            </td>
          </tr>

          <tr v-if="olympiad.starts_at" class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
            <td class="px-5 py-3.5 text-sm font-black text-slate-500 dark:text-slate-400">Boshlanish vaqti</td>
            <td class="px-5 py-3.5 text-sm font-semibold text-slate-700 dark:text-slate-200">{{ formatDate(olympiad.starts_at) }}</td>
          </tr>

          <tr v-if="olympiad.ends_at" class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
            <td class="px-5 py-3.5 text-sm font-black text-slate-500 dark:text-slate-400">Tugash vaqti</td>
            <td class="px-5 py-3.5 text-sm font-semibold text-slate-700 dark:text-slate-200">{{ formatDate(olympiad.ends_at) }}</td>
          </tr>

          <tr v-if="olympiad.duration" class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
            <td class="px-5 py-3.5 text-sm font-black text-slate-500 dark:text-slate-400">Davomiyligi</td>
            <td class="px-5 py-3.5 text-sm font-semibold text-slate-700 dark:text-slate-200">
              {{ String(Math.floor(olympiad.duration / 60)).padStart(2, '0') }}:{{ String(olympiad.duration % 60).padStart(2, '0') }}:00
            </td>
          </tr>

          <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
            <td class="px-5 py-3.5 text-sm font-black text-slate-500 dark:text-slate-400">Olimpiada turi</td>
            <td class="px-5 py-3.5">
              <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-700 dark:text-slate-200">
                <Lock v-if="olympiad.type === 'private'" class="w-3.5 h-3.5 text-violet-500" />
                <Globe v-else class="w-3.5 h-3.5 text-sky-500" />
                {{ olympiad.type === 'private' ? 'Yopiq (Private)' : 'Ochiq (Public)' }}
              </span>
            </td>
          </tr>

          <tr v-if="olympiad.pass_score" class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
            <td class="px-5 py-3.5 text-sm font-black text-slate-500 dark:text-slate-400">O'tish bali</td>
            <td class="px-5 py-3.5 text-sm font-semibold text-slate-700 dark:text-slate-200">{{ olympiad.pass_score }}%</td>
          </tr>

          <tr v-if="olympiad.max_attempts" class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
            <td class="px-5 py-3.5 text-sm font-black text-slate-500 dark:text-slate-400">Urinishlar soni</td>
            <td class="px-5 py-3.5 text-sm font-semibold text-slate-700 dark:text-slate-200">{{ olympiad.max_attempts }} ta</td>
          </tr>

          <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
            <td class="px-5 py-3.5 text-sm font-black text-slate-500 dark:text-slate-400">Savollar soni</td>
            <td class="px-5 py-3.5 text-sm font-semibold text-slate-700 dark:text-slate-200">{{ olympiad.questions_count }} ta</td>
          </tr>

          <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
            <td class="px-5 py-3.5 text-sm font-black text-slate-500 dark:text-slate-400">Qatnashuvchilar</td>
            <td class="px-5 py-3.5">
              <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-700 dark:text-slate-200">
                <Users class="w-4 h-4 text-slate-400" />
                {{ olympiad.participants_count ?? 0 }} kishi
              </span>
            </td>
          </tr>

          <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
            <td class="px-5 py-3.5 text-sm font-black text-slate-500 dark:text-slate-400 align-top pt-4">Sozlamalar</td>
            <td class="px-5 py-3.5">
              <div class="flex flex-wrap gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold"
                  :class="olympiad.shuffle_questions ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600' : 'bg-gray-100 dark:bg-slate-800 text-slate-400'">
                  {{ olympiad.shuffle_questions ? '✓' : '✗' }} Savollar aralashtiriladi
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold"
                  :class="olympiad.shuffle_options ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600' : 'bg-gray-100 dark:bg-slate-800 text-slate-400'">
                  {{ olympiad.shuffle_options ? '✓' : '✗' }} Variantlar aralashtiriladi
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold"
                  :class="olympiad.show_answers ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600' : 'bg-gray-100 dark:bg-slate-800 text-slate-400'">
                  {{ olympiad.show_answers ? '✓' : '✗' }} Javoblar ko'rsatiladi
                </span>
              </div>
            </td>
          </tr>

        </tbody>
      </table>
    </div>

    <!-- CTA -->
    <template v-if="status === 'upcoming'">
      <button v-if="!isRegistered"
        :disabled="regLoading"
        class="w-1/4 py-3.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white rounded-2xl font-black text-base transition-all active:scale-[0.98] flex items-center justify-center gap-2 shadow-lg shadow-blue-600/20"
        @click="emit('register')">
        <svg v-if="regLoading" class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        <UserPlus v-else class="w-5 h-5" />
        Ro'yhatdan o'tish
      </button>
      <div v-else class="space-y-3">
        <div class="w-full py-3.5 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-900/50 text-emerald-700 dark:text-emerald-400 rounded-2xl font-black text-base text-center flex items-center justify-center gap-2">
          <CheckCircle2 class="w-5 h-5" /> Siz ro'yhatdan o'tgansiz
        </div>
        <button
          :disabled="regLoading"
          class="w-1/4 py-2.5 bg-gray-100 dark:bg-slate-800 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 text-slate-500 dark:text-slate-400 disabled:opacity-60 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2"
          @click="emit('unregister')"
        >
          <UserMinus class="w-4 h-4" /> Ro'yhatdan chiqish
        </button>
      </div>
    </template>

    <template v-else-if="status === 'active'">
      <button v-if="canStart"
        class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-base transition-all active:scale-[0.98] flex items-center justify-center gap-2 shadow-lg shadow-blue-600/20"
        @click="emit('start')">
        <Play class="w-5 h-5" /> Olimpiadani boshlash
      </button>
      <div v-else-if="!isRegistered"
        class="w-full py-4 px-6 border border-slate-200 dark:border-slate-800 rounded-2xl flex items-center justify-center gap-3 bg-transparent">
        
        <Triangle class="w-5 h-5 text-slate-400 dark:text-slate-500" />
        
        <span class="font-medium text-sm text-slate-600 dark:text-slate-300">
          Ro'yxatdan o'tmagansiz — testda ishtirok etib bo'lmaydi
        </span>
      </div>
      <div v-else-if="myResult?.status === 'completed'"
        class="w-full py-3.5 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-900/50 text-emerald-600 dark:text-emerald-400 rounded-2xl font-black text-base text-center flex items-center justify-center gap-2">
        <CheckCircle2 class="w-5 h-5" /> Siz ishtirok etdingiz — {{ myResult.score_percent }}%
      </div>
    </template>

    <div v-else class="w-full py-3.5 bg-gray-100 dark:bg-slate-800 text-slate-400 rounded-2xl font-bold text-sm text-center">
      Olimpiada tugagan
    </div>

  </div>
</template>