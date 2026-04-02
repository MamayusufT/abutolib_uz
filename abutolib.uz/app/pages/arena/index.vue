<!-- pages/arena.vue -->
<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 transition-colors duration-300">
    <div class="container mx-auto px-4 py-6">
      <div class="text-center mb-6">
        <h1 class="text-3xl font-bold text-blue-600 dark:text-blue-400">⚔️ ARENA ⚔️</h1>
        <p class="text-gray-600 dark:text-gray-400 text-sm">Botlar bilan intellektual jang</p>
      </div>

      <div class="max-w-5xl mx-auto">
        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl shadow-md p-5 mb-6">
          <h2 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3">⚙️ Sozlamalar</h2>
          <div class="grid grid-cols-3 gap-3">
            <div>
              <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">📝 Savollar</label>
              <select v-model="settings.questionCount" class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                <option :value="3">3 ta</option>
                <option :value="5">5 ta</option>
                <option :value="10">10 ta</option>
              </select>
            </div>
            <div>
              <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">⏱️ Vaqt</label>
              <select v-model="settings.timeLimit" class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                <option :value="15">15 soniya</option>
                <option :value="30">30 soniya</option>
                <option :value="60">60 soniya</option>
                <option :value="0">Cheksiz</option>
              </select>
            </div>
            <div>
              <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">🤖 Bot</label>
              <select v-model="settings.botLevel" class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                <option value="easy">🟢 Easy</option>
                <option value="medium">🟡 Medium</option>
                <option value="hard">🔴 Hard</option>
              </select>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-3 gap-3 mb-6">
          <div v-for="bot in bots" :key="bot.id" @click="selectBot(bot)" class="cursor-pointer rounded-xl p-3 transition-all duration-200 hover:scale-105" :class="[selectedBot?.id === bot.id ? 'ring-2 ring-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700']">
            <div class="text-center">
              <div class="text-3xl">{{ bot.avatar }}</div>
              <div class="font-bold text-sm text-gray-800 dark:text-gray-200">{{ bot.name }}</div>
              <div class="text-xs mt-1" :class="bot.badgeClass">{{ bot.level }}</div>
              <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">🎯 {{ bot.accuracy }}%</div>
            </div>
          </div>
        </div>

        <div v-if="selectedBot && !battleActive" class="text-center">
          <button @click="startBattle" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition shadow-md">🚀 Jangni boshlash</button>
        </div>

        <div v-if="battleActive" class="bg-gray-50 dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
          <div class="bg-blue-600 dark:bg-blue-700 px-4 py-3">
            <div class="flex justify-between items-center text-white">
              <div class="flex items-center gap-2">
                <span class="text-xl">👤</span>
                <span class="font-semibold">Siz</span>
                <span class="text-xl font-bold">{{ playerScore }}</span>
              </div>
              <div class="text-lg font-bold">VS</div>
              <div class="flex items-center gap-2">
                <span class="font-semibold">{{ selectedBot.name }}</span>
                <span class="text-xl">{{ selectedBot.avatar }}</span>
                <span class="text-xl font-bold">{{ botScore }}</span>
              </div>
            </div>
          </div>

          <div class="p-4">
            <div class="flex flex-wrap gap-2 mb-4 justify-center">
              <div v-for="(q, idx) in battleQuestions" :key="idx" @click="jumpToQuestion(idx)" class="cursor-pointer transition-all duration-200" :class="getQuestionIcon(idx)">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold shadow-sm" :class="getQuestionClass(idx)">
                  {{ getQuestionStatusIcon(idx) }}
                </div>
              </div>
            </div>

            <div class="mb-3 flex justify-between text-sm text-gray-600 dark:text-gray-400">
              <span>📋 Savol {{ currentIndex + 1 }} / {{ battleQuestions.length }}</span>
              <span v-if="settings.timeLimit > 0 && !answered && !battleFinished" class="font-mono">⏱️ {{ timeLeft }}s</span>
              <span v-if="answered && !battleFinished" class="text-green-600 dark:text-green-400">✅ Javob berildi</span>
            </div>

            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mb-4">
              <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-300" :style="{ width: `${((currentIndex + 1) / battleQuestions.length) * 100}%` }"></div>
            </div>

            <div v-if="!battleFinished">
              <div class="bg-white dark:bg-gray-900 rounded-lg p-4 mb-4 border-l-4 border-blue-500 shadow-sm">
                <p class="text-gray-800 dark:text-gray-200 font-medium">{{ currentQuestion.question }}</p>
              </div>

              <div class="space-y-2 mb-4">
                <button v-for="(opt, idx) in currentQuestion.options" :key="idx" @click="submitAnswer(opt)" :disabled="answered" class="w-full text-left p-3 rounded-lg border transition-all duration-200" :class="getOptionClass(opt)">
                  <div class="flex items-center gap-3">
                    <span class="w-7 h-7 flex items-center justify-center rounded-full text-sm font-medium" :class="getOptionIconClass(opt)">{{ getOptionLetter(idx) }}</span>
                    <span class="text-gray-800 dark:text-gray-200">{{ opt }}</span>
                    <span v-if="answered && opt === currentQuestion.correctAnswer" class="ml-auto text-green-500">✓</span>
                    <span v-if="answered && selectedAnswer === opt && opt !== currentQuestion.correctAnswer" class="ml-auto text-red-500">✗</span>
                  </div>
                </button>
              </div>

              <div v-if="answered" class="text-center pt-2 border-t border-gray-200 dark:border-gray-700">
                <button @click="nextQuestion" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">➡️ Keyingi savol</button>
              </div>
            </div>

            <div v-else class="text-center py-6">
              <div class="text-6xl mb-3">{{ getResultEmoji() }}</div>
              <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-2">{{ getResultText() }}</h3>
              <div class="inline-block bg-gray-200 dark:bg-gray-700 rounded-lg px-4 py-2 mb-4">
                <span class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ playerScore }}</span>
                <span class="text-gray-600 dark:text-gray-400 mx-2">:</span>
                <span class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ botScore }}</span>
              </div>
              <div class="flex gap-3 justify-center">
                <button @click="resetBattle" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">🔄 Qayta jang</button>
                <button @click="selectNewBot" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">🎯 Yangi bot</button>
              </div>
            </div>
          </div>
        </div>

        <div v-if="logs.length > 0" class="mt-5 bg-gray-50 dark:bg-gray-800 rounded-xl shadow-md p-4">
          <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-2 text-sm">📜 Jang jurnali</h3>
          <div class="space-y-1 max-h-32 overflow-y-auto">
            <div v-for="log in logs.slice(0, 8)" :key="log.id" class="text-xs py-1 border-b border-gray-200 dark:border-gray-700 last:border-0" :class="log.type === 'player' ? 'text-blue-600 dark:text-blue-400' : log.type === 'bot' ? 'text-gray-600 dark:text-gray-400' : 'text-gray-500 dark:text-gray-500'">{{ log.text }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onUnmounted } from 'vue'

const settings = ref({ questionCount: 5, timeLimit: 30, botLevel: 'medium' })

const bots = ref([
  { id: 1, name: 'Easy Bot', avatar: '🐣', level: 'Easy', accuracy: 70, wins: 45, badgeClass: 'text-green-600 dark:text-green-400', getAnswer: (q) => getBotAnswer(q, 0.7) },
  { id: 2, name: 'Medium Bot', avatar: '⚡', level: 'Medium', accuracy: 85, wins: 128, badgeClass: 'text-yellow-600 dark:text-yellow-400', getAnswer: (q) => getBotAnswer(q, 0.85) },
  { id: 3, name: 'Hard Bot', avatar: '🔥', level: 'Hard', accuracy: 95, wins: 267, badgeClass: 'text-red-600 dark:text-red-400', getAnswer: (q) => getBotAnswer(q, 0.95) }
])

const questionBank = [
  { question: 'Nuxt.js qaysi framework asosida ishlaydi?', options: ['React', 'Vue.js', 'Angular', 'Svelte'], correctAnswer: 'Vue.js' },
  { question: 'Tailwind CSS qanday turdagi framework?', options: ['CSS-in-JS', 'Utility-first', 'Component-based', 'Semantic CSS'], correctAnswer: 'Utility-first' },
  { question: 'JavaScript da "closure" nima?', options: ['Funksiya ichidagi funksiya', 'Loop operatori', 'Massiv usuli', 'DOM elementi'], correctAnswer: 'Funksiya ichidagi funksiya' },
  { question: 'Vue.js da "computed" qachon ishlatiladi?', options: ['Ma\'lumotlarni qayta hisoblash', 'Asinxron kod', 'DOM ga kirish', 'CSS stillar'], correctAnswer: 'Ma\'lumotlarni qayta hisoblash' },
  { question: 'Git da "git merge" nima vazifani bajaradi?', options: ['Branch o\'chiradi', 'O\'zgarishlarni birlashtiradi', 'Yangi branch yaratadi', 'Commit bekor qiladi'], correctAnswer: 'O\'zgarishlarni birlashtiradi' },
  { question: 'TypeScript da "interface" nima uchun?', options: ['Stil yozish', 'Ma\'lumotlar turini aniqlash', 'Funksiya yaratish', 'Massiv yaratish'], correctAnswer: 'Ma\'lumotlar turini aniqlash' },
  { question: 'REST API da GET so\'rovi nima uchun?', options: ['Ma\'lumot olish', 'Ma\'lumot yaratish', 'Ma\'lumot yangilash', 'Ma\'lumot o\'chirish'], correctAnswer: 'Ma\'lumot olish' },
  { question: 'MongoDB qanday turdagi ma\'lumotlar bazasi?', options: ['SQL', 'NoSQL', 'Graph', 'Key-Value'], correctAnswer: 'NoSQL' },
  { question: 'Docker nima uchun ishlatiladi?', options: ['Kod yozish', 'Konteynerlashtirish', 'Ma\'lumotlar bazasi', 'Dizayn qilish'], correctAnswer: 'Konteynerlashtirish' },
  { question: 'Python da "pip" nima?', options: ['Paket menejeri', 'Framework', 'IDE', 'Dastur'], correctAnswer: 'Paket menejeri' }
]

const getBotAnswer = (question, accuracy) => {
  if (Math.random() < accuracy) return question.correctAnswer
  const wrong = question.options.filter(opt => opt !== question.correctAnswer)
  return wrong[Math.floor(Math.random() * wrong.length)]
}

const selectedBot = ref(null)
const battleActive = ref(false)
const battleFinished = ref(false)
const battleQuestions = ref([])
const currentIndex = ref(0)
const playerScore = ref(0)
const botScore = ref(0)
const selectedAnswer = ref(null)
const answered = ref(false)
const logs = ref([])
const answersHistory = ref([])
let timerInterval = null
const timeLeft = ref(0)

const currentQuestion = computed(() => battleQuestions.value[currentIndex.value])

const getQuestionStatusIcon = (idx) => {
  const history = answersHistory.value[idx]
  if (!history) return (idx + 1).toString()
  if (history.isCorrect) return '✓'
  return '✗'
}

const getQuestionClass = (idx) => {
  const history = answersHistory.value[idx]
  if (idx === currentIndex.value && !answered && !battleFinished) return 'bg-blue-500 text-white ring-2 ring-blue-300'
  if (!history) return 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400'
  if (history.isCorrect) return 'bg-green-500 text-white'
  return 'bg-red-500 text-white'
}

const getQuestionIcon = (idx) => {
  const history = answersHistory.value[idx]
  if (idx === currentIndex.value && !answered && !battleFinished) return 'animate-pulse'
  return ''
}

const getOptionLetter = (idx) => {
  const letters = ['A', 'B', 'C', 'D']
  return letters[idx]
}

const getOptionIconClass = (opt) => {
  if (answered && opt === currentQuestion.value.correctAnswer) return 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
  if (answered && selectedAnswer.value === opt && opt !== currentQuestion.value.correctAnswer) return 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'
  return 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
}

const getOptionClass = (opt) => {
  if (answered && opt === currentQuestion.value.correctAnswer) return 'border-green-500 bg-green-50 dark:bg-green-900/20'
  if (answered && selectedAnswer.value === opt && opt !== currentQuestion.value.correctAnswer) return 'border-red-500 bg-red-50 dark:bg-red-900/20'
  return 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 hover:border-blue-500'
}

const getResultEmoji = () => {
  if (playerScore > botScore) return '🏆🎉🏅'
  if (playerScore === botScore) return '🤝😐'
  return '💀😢'
}

const getResultText = () => {
  if (playerScore > botScore) return 'G\'alaba!'
  if (playerScore === botScore) return 'Durang!'
  return 'Mag\'lubiyet!'
}

const addLog = (type, text) => {
  logs.value.unshift({ id: Date.now(), type, text })
  if (logs.value.length > 15) logs.value.pop()
}

const jumpToQuestion = (idx) => {
  if (answersHistory.value[idx] || idx === currentIndex.value) return
  if (idx < currentIndex.value && answersHistory.value[idx]) {
    currentIndex.value = idx
    selectedAnswer.value = null
    answered.value = false
    if (timerInterval) clearInterval(timerInterval)
    startTimer()
  }
}

const selectBot = (bot) => { if (!battleActive.value) selectedBot.value = bot }

const startBattle = () => {
  const shuffled = [...questionBank].sort(() => Math.random() - 0.5)
  battleQuestions.value = shuffled.slice(0, settings.value.questionCount)
  currentIndex.value = 0
  playerScore.value = 0
  botScore.value = 0
  selectedAnswer.value = null
  answered.value = false
  battleFinished.value = false
  battleActive.value = true
  answersHistory.value = []
  logs.value = []
  addLog('system', `⚔️ Jang boshlandi! Raqib: ${selectedBot.value.name}`)
  startTimer()
}

const startTimer = () => {
  if (timerInterval) clearInterval(timerInterval)
  if (settings.value.timeLimit > 0) {
    timeLeft.value = settings.value.timeLimit
    timerInterval = setInterval(() => {
      if (!battleActive.value || battleFinished.value || answered.value) return
      if (timeLeft.value > 0) {
        timeLeft.value--
      }
      if (timeLeft.value === 0 && !answered.value) {
        clearInterval(timerInterval)
        handleTimeout()
      }
    }, 1000)
  }
}

const handleTimeout = () => {
  if (answered.value || battleFinished.value) return
  answered.value = true
  answersHistory.value[currentIndex.value] = { isCorrect: false, selected: null }
  addLog('system', `⏰ Vaqt tugadi! Javob berilmadi.`)
  const botAns = selectedBot.value.getAnswer(currentQuestion.value)
  if (botAns === currentQuestion.value.correctAnswer) {
    botScore.value += 10
    addLog('bot', `🤖 ${selectedBot.value.name} to'g'ri javob berdi (+10)`)
  } else {
    addLog('bot', `🤖 ${selectedBot.value.name} xato javob berdi`)
  }
  addLog('system', `📖 To'g'ri javob: ${currentQuestion.value.correctAnswer}`)
}

const submitAnswer = (answer) => {
  if (answered.value || battleFinished.value) return
  if (timerInterval) clearInterval(timerInterval)
  selectedAnswer.value = answer
  answered.value = true
  const isCorrect = answer === currentQuestion.value.correctAnswer
  answersHistory.value[currentIndex.value] = { isCorrect, selected: answer }
  if (isCorrect) {
    playerScore.value += 10
    addLog('player', `✅ Siz to'g'ri javob berdingiz! (+10)`)
  } else {
    addLog('player', `❌ Siz xato javob berdingiz`)
  }
  const botAns = selectedBot.value.getAnswer(currentQuestion.value)
  if (botAns === currentQuestion.value.correctAnswer) {
    botScore.value += 10
    addLog('bot', `🤖 ${selectedBot.value.name} to'g'ri javob berdi (+10)`)
  } else {
    addLog('bot', `🤖 ${selectedBot.value.name} xato javob berdi`)
  }
  addLog('system', `📖 To'g'ri javob: ${currentQuestion.value.correctAnswer}`)
}

const nextQuestion = () => {
  if (currentIndex.value + 1 < battleQuestions.value.length) {
    currentIndex.value++
    selectedAnswer.value = null
    answered.value = false
    startTimer()
  } else {
    battleFinished.value = true
    battleActive.value = false
    if (timerInterval) clearInterval(timerInterval)
    addLog('system', `🏁 Jang yakunlandi! Hisob: ${playerScore} - ${botScore}`)
    if (playerScore > botScore) addLog('system', `🎉 Siz g'olib bo'ldingiz!`)
    else if (playerScore === botScore) addLog('system', `🤝 Durang!`)
    else addLog('system', `💀 Bot g'alaba qozondi!`)
  }
}

const resetBattle = () => {
  battleActive.value = false
  battleFinished.value = false
  startBattle()
}

const selectNewBot = () => {
  if (timerInterval) clearInterval(timerInterval)
  battleActive.value = false
  battleFinished.value = false
  selectedBot.value = null
  logs.value = []
  answersHistory.value = []
}

onUnmounted(() => { if (timerInterval) clearInterval(timerInterval) })
</script>

<style scoped>
@keyframes pulse {
  0%, 100% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.05); opacity: 0.9; }
}
.animate-pulse {
  animation: pulse 0.8s ease-in-out infinite;
}
</style>