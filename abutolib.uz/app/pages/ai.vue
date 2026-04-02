<!-- pages/index.vue -->
<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-gray-900 dark:via-gray-800 dark:to-slate-900 transition-colors duration-300">
    <!-- Hero Section -->
    <div class="relative overflow-hidden">
      <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>
      <div class="container mx-auto px-4 py-12 md:py-20 relative z-10">
        <div class="text-center max-w-4xl mx-auto">
          <div class="inline-flex items-center gap-2 bg-blue-100 dark:bg-blue-900/30 px-4 py-2 rounded-full mb-6 animate-pulse">
            <Sparkles class="w-4 h-4 text-blue-600 dark:text-blue-400" />
            <span class="text-sm font-medium text-blue-700 dark:text-blue-300">AI Powered Testing Platform</span>
          </div>
          <h1 class="text-5xl md:text-7xl font-bold mb-6 bg-gradient-to-r from-blue-700 via-indigo-600 to-purple-600 dark:from-blue-400 dark:via-indigo-400 dark:to-purple-400 bg-clip-text text-transparent animate-gradient">
            Intellekt Test
          </h1>
          <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-2xl mx-auto">
            PDF kitoblardan avtomatik testlar yarating. Sun'iy intellekt yordamida tezkor va sifatli.
          </p>
          <div class="flex flex-wrap gap-4 justify-center">
            <div class="flex items-center gap-2 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm px-4 py-2 rounded-full">
              <FileText class="w-4 h-4 text-blue-600" />
              <span class="text-sm">PDF yuklash</span>
            </div>
            <div class="flex items-center gap-2 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm px-4 py-2 rounded-full">
              <Clock class="w-4 h-4 text-green-600" />
              <span class="text-sm">Vaqt boshqaruvi</span>
            </div>
            <div class="flex items-center gap-2 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm px-4 py-2 rounded-full">
              <BarChart3 class="w-4 h-4 text-purple-600" />
              <span class="text-sm">Batafsil natijalar</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="container mx-auto px-4 py-8 max-w-5xl">
      <!-- Test Form -->
      <div v-if="!testActive && !testCompleted" class="animate-fadeIn">
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl p-6 md:p-8 border border-white/20 dark:border-gray-700/50">
          <!-- Progress Steps -->
          <div class="flex justify-between mb-8">
            <div v-for="(step, idx) in steps" :key="idx" class="flex-1 text-center relative">
              <div class="relative z-10">
                <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center transition-all duration-300"
                  :class="currentStep >= idx ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg' : 'bg-gray-200 dark:bg-gray-700 text-gray-500'">
                  {{ idx + 1 }}
                </div>
                <div class="text-xs mt-2 text-gray-600 dark:text-gray-400 hidden sm:block">{{ step }}</div>
              </div>
              <div v-if="idx < steps.length - 1" class="absolute top-5 left-1/2 w-full h-0.5 -mt-2"
                :class="currentStep > idx ? 'bg-gradient-to-r from-blue-600 to-indigo-600' : 'bg-gray-200 dark:bg-gray-700'">
              </div>
            </div>
          </div>

          <!-- Step 1: File Upload -->
          <div v-show="currentStep === 0" class="space-y-6 animate-slideRight">
            <div class="text-center mb-8">
              <Upload class="w-16 h-16 mx-auto text-blue-600 mb-4" />
              <h3 class="text-2xl font-bold text-gray-800 dark:text-white">PDF fayl yuklang</h3>
              <p class="text-gray-500 dark:text-gray-400 mt-2">Test yaratish uchun kitob yoki hujjatni yuklang</p>
            </div>
            <div class="relative">
              <input
                type="file"
                accept=".pdf"
                @change="handleFileUpload"
                class="hidden"
                ref="fileInput"
              />
              <div @click="$refs.fileInput.click()"
                class="border-3 border-dashed rounded-2xl p-12 text-center cursor-pointer transition-all hover:border-blue-500 hover:bg-blue-50/50 dark:hover:bg-blue-900/20"
                :class="form.pdf_file ? 'border-green-500 bg-green-50/50 dark:bg-green-900/20' : 'border-gray-300 dark:border-gray-600'">
                <FileUp v-if="!form.pdf_file" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                <FileCheck v-else class="w-12 h-12 mx-auto text-green-500 mb-4" />
                <p class="text-gray-600 dark:text-gray-300">
                  {{ form.pdf_file ? form.pdf_file.name : 'PDF faylni tanlang yoki shu yerga tashlang' }}
                </p>
                <p class="text-sm text-gray-400 mt-2">Maksimal hajm: 50MB</p>
              </div>
            </div>
          </div>

          <!-- Step 2: Page Settings -->
          <div v-show="currentStep === 1" class="space-y-6 animate-slideRight">
            <div class="text-center mb-8">
              <Settings class="w-16 h-16 mx-auto text-indigo-600 mb-4" />
              <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Sahifa sozlamalari</h3>
              <p class="text-gray-500 dark:text-gray-400 mt-2">Test olinadigan sahifalarni belgilang</p>
            </div>
            <div class="grid md:grid-cols-2 gap-6">
              <div class="relative group">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Boshlash sahifasi</label>
                <input type="number" v-model.number="form.start_page" min="1"
                  class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all">
                <div class="absolute right-3 top-9 text-gray-400">📄</div>
              </div>
              <div class="relative group">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tugash sahifasi</label>
                <input type="number" v-model.number="form.end_page" min="1"
                  class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all">
                <div class="absolute right-3 top-9 text-gray-400">📚</div>
              </div>
            </div>
          </div>

          <!-- Step 3: Test Configuration -->
          <div v-show="currentStep === 2" class="space-y-6 animate-slideRight">
            <div class="text-center mb-8">
              <Sliders class="w-16 h-16 mx-auto text-purple-600 mb-4" />
              <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Test sozlamalari</h3>
              <p class="text-gray-500 dark:text-gray-400 mt-2">Qiyinchilik va vaqtni belgilang</p>
            </div>
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Til</label>
                <select v-model="form.language"
                  class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                  <option value="uz">🇺🇿 O'zbek tili</option>
                  <option value="en">🇬🇧 English</option>
                  <option value="ru">🇷🇺 Русский</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Qiyinchilik</label>
                <select v-model="form.difficulty"
                  class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                  <option value="oson">🌱 Oson</option>
                  <option value="o'rta">📘 O'rta</option>
                  <option value="qiyin">🎯 Qiyin</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Savollar soni</label>
                <input type="number" v-model.number="form.questions_count" min="1" max="50"
                  class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-blue-500 dark:bg-gray-700 dark:text-white">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Vaqt (daqiqa)</label>
                <input type="number" v-model.number="form.time_minutes" min="1" max="180"
                  class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-blue-500 dark:bg-gray-700 dark:text-white">
              </div>
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="flex justify-between mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <button v-if="currentStep > 0" @click="currentStep--"
              class="px-6 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all flex items-center gap-2">
              <ChevronLeft class="w-4 h-4" /> Orqaga
            </button>
            <button v-if="currentStep < 2" @click="currentStep++"
              :disabled="currentStep === 0 && !form.pdf_file"
              class="ml-auto px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:shadow-lg transition-all flex items-center gap-2 disabled:opacity-50">
              Keyingi <ChevronRight class="w-4 h-4" />
            </button>
            <button v-if="currentStep === 2" @click="generateQuiz" :disabled="loading || !form.pdf_file"
              class="ml-auto px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:shadow-lg transition-all flex items-center gap-2 disabled:opacity-50 font-semibold">
              <Zap v-if="!loading" class="w-4 h-4" />
              <Loader2 v-else class="w-4 h-4 animate-spin" />
              {{ loading ? 'Yaratilmoqda...' : 'Testni yaratish' }}
            </button>
          </div>
        </div>

        <!-- Features Section -->
        <div class="mt-12 grid md:grid-cols-3 gap-6">
          <div class="bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 text-center hover:scale-105 transition-transform">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center mx-auto mb-4">
              <Brain class="w-6 h-6 text-blue-600" />
            </div>
            <h4 class="font-semibold text-gray-800 dark:text-white mb-2">AI Powered</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400">Sun'iy intellekt yordamida avtomatik test yaratish</p>
          </div>
          <div class="bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 text-center hover:scale-105 transition-transform">
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/50 rounded-xl flex items-center justify-center mx-auto mb-4">
              <Clock class="w-6 h-6 text-green-600" />
            </div>
            <h4 class="font-semibold text-gray-800 dark:text-white mb-2">Vaqt boshqaruvi</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400">Test vaqtini belgilash va avtomatik tugatish</p>
          </div>
          <div class="bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 text-center hover:scale-105 transition-transform">
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/50 rounded-xl flex items-center justify-center mx-auto mb-4">
              <Award class="w-6 h-6 text-purple-600" />
            </div>
            <h4 class="font-semibold text-gray-800 dark:text-white mb-2">Batafsil natijalar</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400">Har bir savol bo'yicha to'liq tahlil</p>
          </div>
        </div>
      </div>

      <!-- Active Test -->
      <div v-if="testActive && !testCompleted" class="animate-slideUp">
        <!-- Timer & Progress Header -->
        <div class="sticky top-4 z-20 mb-6">
          <div class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-md rounded-2xl shadow-xl p-4 border border-gray-200/50 dark:border-gray-700/50">
            <div class="flex flex-wrap justify-between items-center gap-3">
              <div class="flex items-center gap-3">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl px-4 py-2 text-white">
                  <div class="text-xs opacity-80">Savol</div>
                  <div class="text-xl font-bold">{{ currentQuestionIndex + 1 }} / {{ questions.length }}</div>
                </div>
                <div class="bg-gray-100 dark:bg-gray-700 rounded-xl px-4 py-2">
                  <div class="text-xs text-gray-500 dark:text-gray-400">Qolgan vaqt</div>
                  <div class="text-xl font-mono font-bold" :class="timeLeft < 60 ? 'text-red-600 animate-pulse' : 'text-green-600'">
                    {{ formatTime(timeLeft) }}
                  </div>
                </div>
              </div>
              <button @click="confirmEndTest"
                class="px-5 py-2 bg-red-500/10 hover:bg-red-500/20 text-red-600 dark:text-red-400 rounded-xl transition-all flex items-center gap-2">
                <LogOut class="w-4 h-4" /> Tugatish
              </button>
            </div>
            <div class="mt-3 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
              <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 transition-all duration-300"
                :style="{ width: `${((currentQuestionIndex + 1) / questions.length) * 100}%` }"></div>
            </div>
          </div>
        </div>

        <!-- Question Card -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-6 md:p-8 border border-gray-100 dark:border-gray-700">
          <div class="flex flex-wrap gap-3 mb-6">
            <span class="px-4 py-1.5 bg-gradient-to-r from-blue-100 to-indigo-100 dark:from-blue-900/50 dark:to-indigo-900/50 text-blue-700 dark:text-blue-300 rounded-full text-sm font-medium flex items-center gap-2">
              <Tag class="w-3.5 h-3.5" /> {{ questions[currentQuestionIndex]?.topic || 'Mavzu' }}
            </span>
            <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-full text-xs flex items-center gap-1">
              <Activity class="w-3 h-3" /> {{ questions[currentQuestionIndex]?.difficulty }}
            </span>
          </div>

          <p class="text-xl md:text-2xl text-gray-800 dark:text-white leading-relaxed mb-8 font-medium">
            {{ questions[currentQuestionIndex]?.question_text }}
          </p>

          <div class="space-y-3">
            <div v-for="(option, key) in questions[currentQuestionIndex]?.options" :key="key"
              @click="selectAnswer(key)"
              class="group flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all duration-200 hover:scale-[1.01] hover:shadow-md"
              :class="[
                selectedAnswer === key
                  ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/30 shadow-md'
                  : 'border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-700 hover:bg-gray-50 dark:hover:bg-gray-700/50'
              ]">
              <div class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 font-bold mr-4 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/50 transition-colors">
                {{ key.toUpperCase() }}
              </div>
              <span class="text-gray-700 dark:text-gray-200 flex-1">{{ option }}</span>
              <Check v-if="selectedAnswer === key" class="w-5 h-5 text-blue-500" />
            </div>
          </div>

          <div class="flex justify-between mt-8 gap-4">
            <button @click="prevQuestion" :disabled="currentQuestionIndex === 0"
              class="px-6 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all disabled:opacity-50 font-medium flex items-center gap-2">
              <ChevronLeft class="w-4 h-4" /> Oldingi
            </button>
            <button @click="nextQuestion"
              class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl transition-all font-medium flex items-center gap-2 shadow-md">
              {{ currentQuestionIndex === questions.length - 1 ? 'Tugatish' : 'Keyingi' }}
              <ChevronRight class="w-4 h-4" />
            </button>
          </div>
        </div>

        <!-- Question Navigator -->
        <div class="mt-6 bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm rounded-2xl shadow-lg p-4">
          <div class="text-sm text-gray-500 dark:text-gray-400 mb-3 flex items-center gap-2">
            <Grid class="w-4 h-4" /> Savollar navigatori
          </div>
          <div class="flex flex-wrap gap-2 max-h-32 overflow-y-auto pb-2">
            <button v-for="(q, idx) in questions" :key="idx" @click="goToQuestion(idx)"
              class="w-10 h-10 rounded-xl font-semibold transition-all duration-200 hover:scale-110"
              :class="[
                idx === currentQuestionIndex
                  ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md'
                  : userAnswers[idx]
                  ? 'bg-green-500 text-white'
                  : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'
              ]">
              {{ idx + 1 }}
            </button>
          </div>
        </div>
      </div>

      <!-- Results -->
      <div v-if="testCompleted" class="animate-scaleUp">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-6 md:p-8">
          <div class="text-center mb-8">
            <div class="text-8xl mb-4 animate-bounce">{{ resultEmoji }}</div>
            <h2 class="text-3xl md:text-4xl font-bold bg-gradient-to-r from-blue-700 to-indigo-600 bg-clip-text text-transparent mb-3">
              Test yakunlandi!
            </h2>
            <p class="text-gray-600 dark:text-gray-400">Sizning natijangiz</p>

            <!-- Score Circle -->
            <div class="relative inline-flex mt-6">
              <svg class="w-44 h-44 transform -rotate-90">
                <circle class="text-gray-200 dark:bg-gray-700" stroke-width="12" stroke="currentColor" fill="none" r="76" cx="88" cy="88"/>
                <circle class="text-blue-600 dark:text-blue-400 transition-all duration-1000 ease-out"
                  stroke-width="12" stroke-linecap="round" stroke="currentColor" fill="none" r="76" cx="88" cy="88"
                  :stroke-dasharray="circumference" :stroke-dashoffset="circumference - (scorePercentage / 100) * circumference"/>
              </svg>
              <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-center">
                <div class="text-4xl font-bold text-gray-800 dark:text-white">{{ scorePercentage }}%</div>
                <div class="text-sm text-gray-500">{{ score }}/{{ questions.length }}</div>
              </div>
            </div>
          </div>

          <div class="space-y-3 max-h-96 overflow-y-auto mb-8 pr-2">
            <div v-for="(q, idx) in questions" :key="idx"
              class="p-4 rounded-xl border-l-4 transition-all hover:shadow-md cursor-pointer"
              :class="userAnswers[idx] === q.correct_answer ? 'border-green-500 bg-green-50/30 dark:bg-green-900/20' : 'border-red-500 bg-red-50/30 dark:bg-red-900/20'">
              <div class="flex items-start gap-3">
                <div class="flex-shrink-0 mt-0.5">
                  <div class="w-6 h-6 rounded-full flex items-center justify-center"
                    :class="userAnswers[idx] === q.correct_answer ? 'bg-green-500' : 'bg-red-500'">
                    <Check v-if="userAnswers[idx] === q.correct_answer" class="w-3.5 h-3.5 text-white" />
                    <X v-else class="w-3.5 h-3.5 text-white" />
                  </div>
                </div>
                <div class="flex-1">
                  <p class="text-gray-800 dark:text-white font-medium text-sm md:text-base">{{ q.question_text }}</p>
                  <div class="mt-2 text-sm">
                    <p class="text-green-600 dark:text-green-400">✅ To'g'ri javob: {{ q.options[q.correct_answer] }}</p>
                    <p v-if="userAnswers[idx] !== q.correct_answer && userAnswers[idx]" class="text-red-600 dark:text-red-400 mt-1">
                      ❌ Sizning javob: {{ q.options[userAnswers[idx]] }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="flex gap-4 flex-col sm:flex-row">
            <button @click="resetTest"
              class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg flex items-center justify-center gap-2">
              <RefreshCw class="w-4 h-4" /> Yangi test
            </button>
            <button @click="downloadResults"
              class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all flex items-center justify-center gap-2">
              <Download class="w-4 h-4" /> Natijalarni yuklash
            </button>
          </div>
        </div>
      </div>

      <!-- Loading Overlay -->
      <div v-if="loading && !testActive" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 text-center shadow-2xl max-w-md mx-4">
          <div class="relative w-32 h-32 mx-auto mb-6">
            <div class="absolute inset-0 border-4 border-blue-200 dark:border-blue-900 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-blue-600 rounded-full animate-spin border-t-transparent"></div>
            <div class="absolute inset-0 flex items-center justify-center">
              <Brain class="w-14 h-14 text-blue-600 animate-pulse" />
            </div>
          </div>
          <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Test tayyorlanmoqda</h3>
          <p class="text-gray-500 dark:text-gray-400">AI savollarni tahlil qilmoqda...</p>
          <div class="mt-4 flex justify-center gap-1">
            <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0s"></div>
            <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-8 text-gray-500 dark:text-gray-400 text-sm border-t border-gray-200 dark:border-gray-800 mt-12">
      <p>© 2025 Intellekt Test. AI Powered Platform</p>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import {
  Sparkles, FileText, Clock, BarChart3, Upload, FileUp, FileCheck,
  Settings, Sliders, ChevronLeft, ChevronRight, Zap, Loader2, LogOut,
  Tag, Activity, Check, X, Grid, RefreshCw, Download, Brain, Award
} from 'lucide-vue-next'

// SEO Meta tags
useHead({
  title: 'Intellekt Test - AI Powered Test Platform',
  meta: [
    { name: 'description', content: "PDF kitoblardan avtomatik testlar yarating. Sun'iy intellekt yordamida tezkor va sifatli test tuzish platformasi." },
    { name: 'keywords', content: 'test, quiz, ai, artificial intelligence, education, learning, test tuzish, online test' },
    { name: 'author', content: 'Intellekt Test' },
    { property: 'og:title', content: 'Intellekt Test - AI Powered Test Platform' },
    { property: 'og:description', content: "PDF kitoblardan avtomatik testlar yarating. Sun'iy intellekt yordamida tezkor va sifatli." },
    { property: 'og:type', content: 'website' },
    { property: 'og:url', content: 'https://ai.abutolib.uz' },
    { name: 'twitter:card', content: 'summary_large_image' },
    { name: 'twitter:title', content: 'Intellekt Test - AI Powered Test Platform' },
    { name: 'twitter:description', content: 'PDF kitoblardan avtomatik testlar yarating.' }
  ],
  link: [
    { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
    { rel: 'canonical', href: 'https://ai.abutolib.uz' }
  ]
})

// State
const loading = ref(false)
const testActive = ref(false)
const testCompleted = ref(false)
const currentStep = ref(0)
const fileInput = ref(null)

const steps = ['PDF yuklash', 'Sahifa sozlamalari', 'Test sozlamalari']

const form = ref({
  pdf_file: null,
  start_page: 1,
  end_page: 3,
  language: 'uz',
  difficulty: "o'rta",
  questions_count: 15,
  time_minutes: 30
})

const questions = ref([])
const userAnswers = ref([])
const currentQuestionIndex = ref(0)
const selectedAnswer = ref(null)
const timeLeft = ref(0)
let timerInterval = null

const score = ref(0)
const scorePercentage = ref(0)

// Computed
const circumference = computed(() => 2 * Math.PI * 76)
const resultEmoji = computed(() => {
  if (scorePercentage.value >= 80) return '🏆'
  if (scorePercentage.value >= 60) return '🎉'
  if (scorePercentage.value >= 40) return '👍'
  return '📚'
})

const API_URL = 'https://ai.abutolib.uz/api/quiz/generate/'

// Methods
const handleFileUpload = (event) => {
  form.value.pdf_file = event.target.files[0]
}

const generateQuiz = async () => {
  if (!form.value.pdf_file) {
    alert('Iltimos, PDF fayl tanlang!')
    return
  }

  loading.value = true

  // FormData ni tuzamiz — binary fayl yuborish uchun
  const formData = new FormData()
  formData.append('pdf_file', form.value.pdf_file)
  formData.append('start_page', form.value.start_page.toString())
  formData.append('end_page', form.value.end_page.toString())
  formData.append('language', form.value.language)
  formData.append('difficulty', form.value.difficulty)
  formData.append('questions_count', form.value.questions_count.toString())

  try {
    const response = await generatePost(API_URL, {
      method: 'POST',
      body: formData
    })

    if (!response.ok) {
      const errText = await response.text()
      throw new Error(`Server xatolik ${response.status}: ${errText}`)
    }

    const result = await response.json()

    if (result.status === 'success' && result.data && result.data.length > 0) {
      questions.value = result.data
      userAnswers.value = new Array(result.data.length).fill(null)
      timeLeft.value = form.value.time_minutes * 60
      testActive.value = true
      testCompleted.value = false
      currentQuestionIndex.value = 0
      selectedAnswer.value = null
      startTimer()
      saveToSession()
    } else {
      alert('Hech qanday savol topilmadi. PDF faylni tekshiring yoki boshqa sahifalarni tanlang.')
    }
  } catch (error) {
    console.error('Xatolik:', error)
    alert(`Xatolik yuz berdi: ${error.message}\n\nInternet aloqasini tekshiring yoki qaytadan urinib ko'ring.`)
  } finally {
    loading.value = false
  }
}

const saveToSession = () => {
  const expiresAt = Date.now() + (timeLeft.value * 1000)
  sessionStorage.setItem('activeTest', JSON.stringify({
    questions: questions.value,
    userAnswers: userAnswers.value,
    currentQuestionIndex: currentQuestionIndex.value,
    timeLeft: timeLeft.value,
    expiresAt
  }))
}

const startTimer = () => {
  if (timerInterval) clearInterval(timerInterval)
  timerInterval = setInterval(() => {
    if (timeLeft.value > 0 && testActive.value && !testCompleted.value) {
      timeLeft.value--
      saveToSession()
      if (timeLeft.value === 0) endTest()
    }
  }, 1000)
}

const selectAnswer = (answer) => {
  if (testCompleted.value) return
  selectedAnswer.value = answer
  userAnswers.value[currentQuestionIndex.value] = answer
  saveToSession()
}

const nextQuestion = () => {
  if (currentQuestionIndex.value < questions.value.length - 1) {
    currentQuestionIndex.value++
    selectedAnswer.value = userAnswers.value[currentQuestionIndex.value]
    saveToSession()
  } else {
    endTest()
  }
}

const prevQuestion = () => {
  if (currentQuestionIndex.value > 0) {
    currentQuestionIndex.value--
    selectedAnswer.value = userAnswers.value[currentQuestionIndex.value]
    saveToSession()
  }
}

const goToQuestion = (index) => {
  currentQuestionIndex.value = index
  selectedAnswer.value = userAnswers.value[index]
}

const endTest = () => {
  if (timerInterval) clearInterval(timerInterval)
  let correct = 0
  questions.value.forEach((q, idx) => {
    if (userAnswers.value[idx] === q.correct_answer) correct++
  })
  score.value = correct
  scorePercentage.value = Math.round((correct / questions.value.length) * 100)
  testActive.value = false
  testCompleted.value = true
  sessionStorage.removeItem('activeTest')
}

const confirmEndTest = () => {
  if (confirm('Testni tugatmoqchimisiz?')) endTest()
}

const resetTest = () => {
  testCompleted.value = false
  testActive.value = false
  questions.value = []
  userAnswers.value = []
  currentQuestionIndex.value = 0
  selectedAnswer.value = null
  timeLeft.value = 0
  score.value = 0
  scorePercentage.value = 0
  form.value.pdf_file = null
  form.value.start_page = 1
  form.value.end_page = 3
  form.value.questions_count = 15
  form.value.time_minutes = 30
  currentStep.value = 0
  if (fileInput.value) fileInput.value.value = ''
}

const downloadResults = () => {
  const results = {
    date: new Date().toISOString(),
    total_questions: questions.value.length,
    correct_answers: score.value,
    percentage: scorePercentage.value,
    answers: questions.value.map((q, idx) => ({
      question: q.question_text,
      topic: q.topic,
      user_answer: q.options[userAnswers.value[idx]],
      correct_answer: q.options[q.correct_answer],
      is_correct: userAnswers.value[idx] === q.correct_answer
    }))
  }
  const blob = new Blob([JSON.stringify(results, null, 2)], { type: 'application/json' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `test_natijalari_${Date.now()}.json`
  a.click()
  URL.revokeObjectURL(url)
}

const formatTime = (seconds) => {
  const mins = Math.floor(seconds / 60)
  const secs = seconds % 60
  return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`
}

// Lifecycle
onMounted(() => {
  const savedTest = sessionStorage.getItem('activeTest')
  if (savedTest) {
    try {
      const testData = JSON.parse(savedTest)
      if (testData.expiresAt > Date.now()) {
        questions.value = testData.questions
        userAnswers.value = testData.userAnswers
        currentQuestionIndex.value = testData.currentQuestionIndex
        timeLeft.value = testData.timeLeft
        testActive.value = true
        startTimer()
      } else {
        sessionStorage.removeItem('activeTest')
      }
    } catch {
      sessionStorage.removeItem('activeTest')
    }
  }
})

onUnmounted(() => {
  if (timerInterval) clearInterval(timerInterval)
})
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes slideUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes scaleUp {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
@keyframes slideRight {
  from { opacity: 0; transform: translateX(-20px); }
  to { opacity: 1; transform: translateX(0); }
}
@keyframes gradient {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
.animate-fadeIn { animation: fadeIn 0.5s ease-out; }
.animate-slideUp { animation: slideUp 0.5s ease-out; }
.animate-scaleUp { animation: scaleUp 0.4s ease-out; }
.animate-slideRight { animation: slideRight 0.4s ease-out; }
.animate-gradient { background-size: 200% auto; animation: gradient 3s linear infinite; }
</style>