<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
    <div class="container mx-auto px-4 py-8">
      <!-- SEO Header -->
      <Head>
        <Title>Turnir jadvallari | Sport va intellektual musobaqalar</Title>
        <Meta name="description" content="16 ishtirokchi uchun nokaut tizimidagi turnir jadvallari" />
      </Head>

      <div class="text-center mb-12">
        <h1 class="text-5xl font-bold mb-4 bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
          Turnir Jadvallari
        </h1>
        <p class="text-gray-300 text-lg">16 ishtirokchi | Nokaut tizimi | 5 ta savoldan test</p>
      </div>

      <!-- Turnirlar Tabs -->
      <div class="flex justify-center gap-4 mb-8 flex-wrap">
        <button
          v-for="tab in tournaments"
          :key="tab.id"
          @click="activeTournament = tab.id"
          class="px-6 py-3 rounded-lg font-semibold transition-all duration-300"
          :class="activeTournament === tab.id 
            ? 'bg-gradient-to-r from-blue-500 to-purple-500 shadow-lg scale-105 text-white' 
            : 'bg-gray-700 hover:bg-gray-600 text-gray-200'"
        >
          {{ tab.name }}
        </button>
      </div>

      <!-- Turnir Setkasi -->
      <div class="bg-gray-800/30 rounded-2xl p-8 border border-gray-700">
        <div v-for="tournament in filteredTournaments" :key="tournament.id">
          <!-- Turnir Info -->
          <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-700">
            <div>
              <h2 class="text-2xl font-bold text-white">{{ tournament.name }}</h2>
              <div class="flex gap-4 mt-2">
                <span class="text-sm text-gray-400">📅 {{ tournament.date }}</span>
                <span class="text-sm px-2 py-1 rounded-full" :class="getStatusClass(tournament.status)">
                  {{ tournament.status }}
                </span>
              </div>
            </div>
            <div class="text-right">
              <div class="text-2xl font-bold text-yellow-400">{{ tournament.prize }}</div>
              <div class="text-sm text-gray-400">Sovrin jamg'armasi</div>
            </div>
          </div>

          <!-- Turnir Setkasi -->
          <div class="overflow-x-auto">
            <div class="min-w-[1200px]">
              <!-- 1/8 final -->
              <div class="mb-12">
                <div class="flex items-center gap-2 mb-4">
                  <div class="w-2 h-6 bg-blue-500 rounded-full"></div>
                  <h3 class="text-xl font-bold text-blue-400">1/8 FINAL</h3>
                </div>
                <div class="grid grid-cols-4 gap-4">
                  <div v-for="(match, idx) in tournament.round16" :key="idx" class="bracket-match">
                    <div class="match-card">
                      <div class="player border-b border-gray-600" :class="{ winner: match.winner === 'left' }">
                        <span class="player-name">{{ match.left.name }}</span>
                        <span class="player-score">{{ match.left.score }}</span>
                      </div>
                      <div class="player" :class="{ winner: match.winner === 'right' }">
                        <span class="player-name">{{ match.right.name }}</span>
                        <span class="player-score">{{ match.right.score }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Chorak final -->
              <div class="mb-12">
                <div class="flex items-center gap-2 mb-4">
                  <div class="w-2 h-6 bg-purple-500 rounded-full"></div>
                  <h3 class="text-xl font-bold text-purple-400">CHORAK FINAL</h3>
                </div>
                <div class="grid grid-cols-2 gap-8">
                  <div v-for="(match, idx) in tournament.quarterFinals" :key="idx" class="bracket-match">
                    <div class="match-card">
                      <div class="player border-b border-gray-600" :class="{ winner: match.winner === 'left' }">
                        <span class="player-name">{{ match.left.name }}</span>
                        <span class="player-score">{{ match.left.score }}</span>
                      </div>
                      <div class="player" :class="{ winner: match.winner === 'right' }">
                        <span class="player-name">{{ match.right.name }}</span>
                        <span class="player-score">{{ match.right.score }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Yarim final -->
              <div class="mb-12">
                <div class="flex items-center gap-2 mb-4">
                  <div class="w-2 h-6 bg-green-500 rounded-full"></div>
                  <h3 class="text-xl font-bold text-green-400">YARIM FINAL</h3>
                </div>
                <div class="grid grid-cols-2 gap-8">
                  <div v-for="(match, idx) in tournament.semiFinals" :key="idx" class="bracket-match">
                    <div class="match-card">
                      <div class="player border-b border-gray-600" :class="{ winner: match.winner === 'left' }">
                        <span class="player-name">{{ match.left.name }}</span>
                        <span class="player-score">{{ match.left.score }}</span>
                      </div>
                      <div class="player" :class="{ winner: match.winner === 'right' }">
                        <span class="player-name">{{ match.right.name }}</span>
                        <span class="player-score">{{ match.right.score }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Final -->
              <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                  <div class="w-2 h-6 bg-yellow-500 rounded-full"></div>
                  <h3 class="text-xl font-bold text-yellow-400">FINAL</h3>
                </div>
                <div class="max-w-md mx-auto">
                  <div class="final-match">
                    <div class="player" :class="{ winner: tournament.final.winner === 'left' }">
                      <span class="player-name">{{ tournament.final.left.name }}</span>
                      <span class="player-score">{{ tournament.final.left.score }}</span>
                    </div>
                    <div class="vs-divider">VS</div>
                    <div class="player" :class="{ winner: tournament.final.winner === 'right' }">
                      <span class="player-name">{{ tournament.final.right.name }}</span>
                      <span class="player-score">{{ tournament.final.right.score }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Chempion -->
              <div v-if="tournament.final.winner" class="text-center mt-8 pt-6 border-t border-gray-700">
                <div class="inline-block px-8 py-4 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-2xl shadow-xl">
                  <div class="text-sm uppercase tracking-wider">🏆 Chempion 🏆</div>
                  <div class="text-2xl font-bold mt-1 text-white">{{ tournament.final[tournament.final.winner].name }}</div>
                </div>
              </div>

              <!-- Test Tugmasi -->
              <div class="text-center mt-8">
                <button 
                  @click="startTest(tournament.id)"
                  class="px-8 py-3 bg-gradient-to-r from-blue-500 to-purple-500 rounded-xl font-bold hover:shadow-2xl transform hover:scale-105 transition-all duration-300 text-white"
                >
                  🎯 Testni boshlash
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Test Modal -->
    <div v-if="activeTest" class="fixed inset-0 bg-black/90 flex items-center justify-center z-50 p-4" @click.self="closeTest">
      <div class="bg-gray-800 rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gray-800 p-6 border-b border-gray-700 rounded-t-2xl">
          <div class="flex justify-between items-center">
            <div>
              <h3 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                Intellektual test
              </h3>
              <p class="text-gray-400 text-sm mt-1">Savol {{ currentQuestionIndex + 1 }} / {{ testQuestions.length }}</p>
            </div>
            <button @click="closeTest" class="text-gray-400 hover:text-white text-3xl">&times;</button>
          </div>
        </div>

        <div class="p-6">
          <div v-if="!testFinished">
            <!-- Progress bar -->
            <div class="mb-6">
              <div class="w-full bg-gray-700 rounded-full h-3 overflow-hidden">
                <div 
                  class="bg-gradient-to-r from-blue-500 to-purple-500 h-3 rounded-full transition-all duration-500" 
                  :style="{ width: `${((currentQuestionIndex + 1) / testQuestions.length) * 100}%` }"
                ></div>
              </div>
            </div>

            <!-- Savol -->
            <div class="mb-8">
              <p class="text-xl font-semibold mb-6 text-white">{{ currentQuestion.question }}</p>
              <div class="space-y-3">
                <button
                  v-for="(option, idx) in currentQuestion.options"
                  :key="idx"
                  @click="selectAnswer(option)"
                  class="w-full text-left p-4 rounded-xl transition-all duration-300"
                  :class="[
                    selectedAnswer === option 
                      ? (answerStatus === 'correct' ? 'bg-green-600' : answerStatus === 'wrong' ? 'bg-red-600' : 'bg-blue-600')
                      : 'bg-gray-700 hover:bg-gray-600',
                    showResult ? 'cursor-default' : 'cursor-pointer'
                  ]"
                  :disabled="showResult"
                >
                  <div class="flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full bg-gray-600 flex items-center justify-center text-sm font-bold text-white">
                      {{ String.fromCharCode(65 + idx) }}
                    </span>
                    <span class="flex-1 text-white">{{ option }}</span>
                  </div>
                </button>
              </div>
            </div>

            <!-- Natija -->
            <div v-if="showResult" class="mb-6 p-4 rounded-xl" :class="answerStatus === 'correct' ? 'bg-green-600/20 border border-green-600' : 'bg-red-600/20 border border-red-600'">
              <div class="flex items-center gap-3">
                <span class="text-2xl">{{ answerStatus === 'correct' ? '✅' : '❌' }}</span>
                <div>
                  <p class="font-semibold text-white">{{ answerStatus === 'correct' ? 'To\'g\'ri javob!' : 'Xato javob' }}</p>
                </div>
              </div>
            </div>

            <!-- Keyingi tugma -->
            <div class="flex justify-end">
              <button
                v-if="showResult"
                @click="nextQuestion"
                class="px-6 py-2 bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg font-semibold text-white hover:shadow-lg transition-all"
              >
                {{ currentQuestionIndex + 1 === testQuestions.length ? '🏁 Natijani ko\'rish' : '➡️ Keyingi savol' }}
              </button>
            </div>
          </div>

          <!-- Natijalar -->
          <div v-else class="text-center">
            <div class="text-7xl mb-4">{{ scorePercentage >= 80 ? '🏆' : scorePercentage >= 60 ? '🎉' : scorePercentage >= 40 ? '👍' : '💪' }}</div>
            <h3 class="text-3xl font-bold mb-2 text-white">Test yakunlandi!</h3>
            <p class="text-gray-400 mb-6">Siz {{ correctAnswers }} ta to\'g\'ri javob berdingiz</p>
            
            <div class="relative w-48 h-48 mx-auto mb-6">
              <svg class="w-full h-full transform -rotate-90">
                <circle cx="96" cy="96" r="88" stroke="#374151" stroke-width="12" fill="none"/>
                <circle 
                  cx="96" cy="96" r="88" 
                  stroke="url(#gradient)" 
                  stroke-width="12" 
                  fill="none"
                  :stroke-dasharray="553" 
                  :stroke-dashoffset="553 - (553 * scorePercentage / 100)"
                  class="transition-all duration-1000"
                />
                <defs>
                  <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#3b82f6"/>
                    <stop offset="100%" stop-color="#a855f7"/>
                  </linearGradient>
                </defs>
              </svg>
              <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-4xl font-bold text-white">{{ scorePercentage }}%</span>
              </div>
            </div>

            <button
              @click="closeTest"
              class="px-8 py-3 bg-gradient-to-r from-blue-500 to-purple-500 rounded-xl font-bold text-white hover:shadow-xl transition-all"
            >
              Yopish
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const tournaments = ref([
  {
    id: 1,
    name: "🏆 Qishki Turnir 2024",
    date: "15.12.2024",
    status: "Davom etmoqda",
    prize: "5,000,000 so'm",
    round16: [
      { left: { name: "MDSPro", score: 600 }, right: { name: "mardonbekhazratov", score: 0 }, winner: "left" },
      { left: { name: "Ismoilmirzouz", score: 1000 }, right: { name: "Husanboy", score: 500 }, winner: "left" },
      { left: { name: "khba", score: 200 }, right: { name: "Darkest_Knight", score: 400 }, winner: "right" },
      { left: { name: "Timosh", score: 800 }, right: { name: "Master_Shox", score: 300 }, winner: "left" }
    ],
    quarterFinals: [
      { left: { name: "MDSPro", score: 1400 }, right: { name: "Ismoilmirzouz", score: 100 }, winner: "left" },
      { left: { name: "Darkest_Knight", score: 600 }, right: { name: "Timosh", score: 800 }, winner: "right" }
    ],
    semiFinals: [
      { left: { name: "MDSPro", score: 0 }, right: { name: "Timosh", score: 0 }, winner: null }
    ],
    final: {
      left: { name: "MDSPro", score: 0 },
      right: { name: "Timosh", score: 0 },
      winner: null
    }
  },
  {
    id: 2,
    name: "🌸 Bahor Kubogi 2025",
    date: "10.03.2025",
    status: "Rejadagi",
    prize: "3,000,000 so'm",
    round16: [
      { left: { name: "Alpha_Pro", score: 750 }, right: { name: "Beta_Warrior", score: 450 }, winner: "left" },
      { left: { name: "Gamma_Star", score: 620 }, right: { name: "Delta_Fox", score: 380 }, winner: "left" },
      { left: { name: "Epsilon_King", score: 510 }, right: { name: "Zeta_Queen", score: 490 }, winner: "left" },
      { left: { name: "Eta_Shark", score: 840 }, right: { name: "Theta_Eagle", score: 320 }, winner: "left" }
    ],
    quarterFinals: [
      { left: { name: "Alpha_Pro", score: 0 }, right: { name: "Gamma_Star", score: 0 }, winner: null },
      { left: { name: "Epsilon_King", score: 0 }, right: { name: "Eta_Shark", score: 0 }, winner: null }
    ],
    semiFinals: [
      { left: { name: "TBD", score: 0 }, right: { name: "TBD", score: 0 }, winner: null }
    ],
    final: {
      left: { name: "TBD", score: 0 },
      right: { name: "TBD", score: 0 },
      winner: null
    }
  },
  {
    id: 3,
    name: "🍂 Kuzgi Marafon 2024",
    date: "05.11.2024",
    status: "Tugagan",
    prize: "2,500,000 so'm",
    round16: [
      { left: { name: "Sherzod_Elite", score: 950 }, right: { name: "Jasur_Knight", score: 300 }, winner: "left" },
      { left: { name: "Otabek_Master", score: 880 }, right: { name: "Farrux_Legend", score: 420 }, winner: "left" },
      { left: { name: "Doston_Phoenix", score: 760 }, right: { name: "Sardor_Tiger", score: 540 }, winner: "left" },
      { left: { name: "Bobur_Dragon", score: 910 }, right: { name: "Alisher_Wolf", score: 390 }, winner: "left" }
    ],
    quarterFinals: [
      { left: { name: "Sherzod_Elite", score: 1200 }, right: { name: "Otabek_Master", score: 800 }, winner: "left" },
      { left: { name: "Doston_Phoenix", score: 950 }, right: { name: "Bobur_Dragon", score: 1050 }, winner: "right" }
    ],
    semiFinals: [
      { left: { name: "Sherzod_Elite", score: 1500 }, right: { name: "Bobur_Dragon", score: 1300 }, winner: "left" }
    ],
    final: {
      left: { name: "Sherzod_Elite", score: 2000 },
      right: { name: "Bobur_Dragon", score: 1800 },
      winner: "left"
    }
  }
])

const activeTournament = ref(1)

const filteredTournaments = computed(() => {
  return tournaments.value.filter(t => t.id === activeTournament.value)
})

const getStatusClass = (status) => {
  if (status === "Davom etmoqda") return "bg-green-600/30 text-green-400 border border-green-600"
  if (status === "Rejadagi") return "bg-blue-600/30 text-blue-400 border border-blue-600"
  return "bg-gray-600/30 text-gray-400 border border-gray-600"
}

// Test savollari
const testQuestions = [
  {
    question: "Nuxt.js qaysi framework asosida ishlaydi?",
    options: ["React", "Vue.js", "Angular", "Svelte"],
    correctAnswer: "Vue.js"
  },
  {
    question: "Tailwind CSS qanday turdagi framework?",
    options: ["CSS-in-JS", "Utility-first", "Component-based", "Semantic CSS"],
    correctAnswer: "Utility-first"
  },
  {
    question: "Git versiya boshqarish tizimida 'git merge' nima vazifani bajaradi?",
    options: ["Branch ni o'chiradi", "O'zgarishlarni birlashtiradi", "Yangi branch yaratadi", "Commit ni bekor qiladi"],
    correctAnswer: "O'zgarishlarni birlashtiradi"
  },
  {
    question: "JavaScript da 'closure' nima?",
    options: ["Funksiya ichidagi funksiya", "Loop operatori", "Massiv usuli", "DOM elementi"],
    correctAnswer: "Funksiya ichidagi funksiya"
  },
  {
    question: "Vue.js da 'computed' xususiyati qachon ishlatiladi?",
    options: ["Ma'lumotlarni qayta hisoblash", "Asinxron kod uchun", "DOM ga to'g'ridan-to'g'ri kirish", "CSS stillar uchun"],
    correctAnswer: "Ma'lumotlarni qayta hisoblash"
  }
]

// Test state
const activeTest = ref(null)
const currentQuestionIndex = ref(0)
const selectedAnswer = ref(null)
const showResult = ref(false)
const answerStatus = ref(null)
const correctAnswers = ref(0)
const testFinished = ref(false)

const currentQuestion = computed(() => testQuestions[currentQuestionIndex.value])
const scorePercentage = computed(() => Math.round((correctAnswers.value / testQuestions.length) * 100))

const startTest = (tournamentId) => {
  activeTest.value = tournamentId
  currentQuestionIndex.value = 0
  selectedAnswer.value = null
  showResult.value = false
  answerStatus.value = null
  correctAnswers.value = 0
  testFinished.value = false
}

const closeTest = () => {
  activeTest.value = null
}

const selectAnswer = (answer) => {
  if (showResult.value) return
  
  selectedAnswer.value = answer
  showResult.value = true
  
  if (answer === currentQuestion.value.correctAnswer) {
    answerStatus.value = 'correct'
    correctAnswers.value++
  } else {
    answerStatus.value = 'wrong'
  }
}

const nextQuestion = () => {
  if (currentQuestionIndex.value + 1 < testQuestions.length) {
    currentQuestionIndex.value++
    selectedAnswer.value = null
    showResult.value = false
    answerStatus.value = null
  } else {
    testFinished.value = true
  }
}
</script>

<style scoped>
.bracket-match {
  position: relative;
}

.match-card {
  background-color: #374151;
  border-radius: 12px;
  overflow: hidden;
  border: 1px solid #4b5563;
}

.final-match {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px;
  background: linear-gradient(to right, #374151, #1f2937);
  border-radius: 12px;
  border: 1px solid #4b5563;
}

.player {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  transition: all 0.3s ease;
}

.winner {
  background: linear-gradient(to right, rgba(22, 163, 74, 0.3), rgba(22, 163, 74, 0.1));
  border-left: 4px solid #22c55e;
}

.player-name {
  font-weight: 500;
  color: white;
}

.player-score {
  font-weight: bold;
  font-size: 1.125rem;
}

.winner .player-score {
  color: #4ade80;
}

.vs-divider {
  padding: 8px 16px;
  background-color: #4b5563;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: bold;
  color: #9ca3af;
}

/* Scrollbar */
::-webkit-scrollbar {
  width: 10px;
  height: 10px;
}

::-webkit-scrollbar-track {
  background: #1f2937;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(to bottom, #3b82f6, #a855f7);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(to bottom, #2563eb, #9333ea);
}
</style>