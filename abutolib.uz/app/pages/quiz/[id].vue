<!-- pages/quiz/[id].vue -->
<template>
  <div class="quiz-wrapper">
    <!-- WAITING STATE -->
    <div v-if="quiz?.status === 'waiting'" class="state-screen fade-in">
      <div class="container" style="max-width:500px;padding-top:4rem">
        <div class="queue-card">
          <div class="queue-icon-big">⏳</div>
          <h2 class="queue-title">Navbat kutilmoqda</h2>
          <div class="queue-number">{{ quiz.queue_position }}</div>
          <div class="queue-label">-navbatdasiz</div>
          <p class="queue-sub">
            Bot hozir boshqa foydalanuvchi bilan o'yin o'ynamoqda.<br>
            O'yin tugagach, sizning navbatingiz boshlanadi.
          </p>
          <div class="queue-bot-info">
            <div class="qbot-avatar">{{ quiz.bot?.name[0] }}</div>
            <div>
              <div class="qbot-name">{{ quiz.bot?.name }}</div>
              <span :class="`level-badge level-${quiz.bot?.level}`">{{ levelLabel(quiz.bot?.level) }}</span>
            </div>
          </div>
          <div class="queue-refresh">
            <div class="refresh-dot"></div>
            Avtomatik yangilanmoqda...
          </div>
        </div>
        <button class="btn btn-secondary" style="width:100%;margin-top:1rem" @click="cancelQuiz">
          Bekor qilish
        </button>
      </div>
    </div>

    <!-- ACTIVE GAME STATE -->
    <div v-else-if="quiz?.status === 'active'" class="game-screen fade-in">
      <div class="container" style="max-width:760px;padding-top:2rem">

        <!-- Top bar: progress + scores -->
        <div class="game-top">
          <div class="progress-info">
            <span class="q-count">{{ quiz.current_question + 1 }} / {{ quiz.total_questions }}</span>
            <div class="progress-bar" style="flex:1">
              <div class="progress-fill" :style="{ width: progressPct + '%' }"></div>
            </div>
          </div>
          <div class="score-display">
            <div class="score-side">
              <div class="score-name">SEN</div>
              <div class="score-value" style="color:var(--accent)">{{ quiz.user_score }}</div>
            </div>
            <div class="score-vs">VS</div>
            <div class="score-side">
              <div class="score-name">{{ quiz.bot?.name }}</div>
              <div class="score-value" style="color:var(--accent3)">{{ quiz.bot_score }}</div>
            </div>
          </div>
        </div>

        <!-- Question card -->
        <div v-if="quiz.current_round && !roundResult" class="question-card scale-in" :key="quiz.current_question">
          <div class="question-meta">
            <span class="topic-tag">{{ quiz.current_round.topic }}</span>
            <span :class="`diff-tag diff-${quiz.current_round.difficulty}`">
              {{ diffLabel(quiz.current_round.difficulty) }}
            </span>
            <span class="points-tag">+{{ quiz.current_round.points }} ball</span>
          </div>

          <!-- Timer + Question -->
          <div class="question-top">
            <div class="timer-ring">
              <svg width="80" height="80" viewBox="0 0 80 80">
                <circle cx="40" cy="40" r="34" fill="none" stroke="var(--border)" stroke-width="6"/>
                <circle cx="40" cy="40" r="34" fill="none"
                  :stroke="timerColor"
                  stroke-width="6"
                  stroke-linecap="round"
                  :stroke-dasharray="timerDash"
                  :stroke-dashoffset="timerOffset"
                  style="transition: stroke-dashoffset 1s linear, stroke 0.5s"
                />
              </svg>
              <div class="timer-text" :style="{ color: timerColor }">{{ timeLeft }}</div>
            </div>

            <div class="question-text">{{ quiz.current_round.question }}</div>
          </div>

          <!-- Answers -->
          <div class="answer-grid">
            <button
              v-for="(opt, idx) in quiz.current_round.options"
              :key="idx"
              class="answer-btn"
              :class="{ selected: selectedAnswer === optLetter(idx), disabled: !!selectedAnswer }"
              :disabled="!!selectedAnswer || submitLoading"
              @click="selectAnswer(optLetter(idx), opt)"
            >
              <span class="opt-letter">{{ optLetter(idx) }}</span>
              {{ opt.substring(3) }}
            </button>
          </div>
        </div>

        <!-- Round result -->
        <div v-if="roundResult" class="round-result scale-in">
          <div class="result-row">
            <!-- User result -->
            <div class="result-side" :class="roundResult.user_correct ? 'correct' : 'wrong'">
              <div class="result-icon">{{ roundResult.user_correct ? '✅' : '❌' }}</div>
              <div class="result-label">SEN</div>
              <div class="result-answer">{{ roundResult.user_answer }}</div>
              <div class="result-pts">{{ roundResult.user_correct ? '+' + roundResult.user_points : '0' }} ball</div>
            </div>

            <div class="result-divider">
              <div class="correct-answer-box">
                <div class="ca-label">TO'G'RI JAVOB</div>
                <div class="ca-value">{{ roundResult.correct_answer }}</div>
              </div>
            </div>

            <!-- Bot result -->
            <div class="result-side" :class="roundResult.bot_correct ? 'correct' : 'wrong'">
              <div class="result-icon">{{ roundResult.bot_correct ? '✅' : '❌' }}</div>
              <div class="result-label">{{ quiz.bot?.name }}</div>
              <div class="result-answer">{{ roundResult.bot_answer }}</div>
              <div class="result-pts">{{ roundResult.bot_correct ? '+' + roundResult.bot_points : '0' }} ball</div>
            </div>
          </div>

          <div v-if="roundResult.explanation" class="explanation">
            💡 {{ roundResult.explanation }}
          </div>

          <div v-if="!isLastQuestion" class="next-countdown">
            Keyingi savol: {{ nextCountdown }}s
          </div>
        </div>
      </div>
    </div>

    <!-- FINISHED STATE -->
    <div v-else-if="quiz?.status === 'finished'" class="state-screen fade-in">
      <div class="container" style="max-width:540px;padding-top:3rem">
        <div class="result-card">
          <div class="winner-banner" :class="quiz.winner">
            <div class="winner-icon">{{ winnerIcon }}</div>
            <div class="winner-title">{{ winnerTitle }}</div>
          </div>

          <div class="score-display" style="margin:1.5rem 0">
            <div class="score-side">
              <div class="score-name">SEN</div>
              <div class="score-value" style="color:var(--accent)">{{ quiz.user_score }}</div>
            </div>
            <div class="score-vs">:</div>
            <div class="score-side">
              <div class="score-name">{{ quiz.bot?.name }}</div>
              <div class="score-value" style="color:var(--accent3)">{{ quiz.bot_score }}</div>
            </div>
          </div>

          <div class="result-actions">
            <NuxtLink to="/bots" class="btn btn-primary" style="flex:1">🔄 Qayta o'ynash</NuxtLink>
            <NuxtLink :to="`/quiz/${quiz.id}/result`" class="btn btn-secondary" style="flex:1">📊 Batafsil</NuxtLink>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ middleware: 'auth' })

const route = useRoute()
const { fetchQuiz, submitAnswer, quiz, pollQuizStatus } = useQuiz()
const { post } = useApi()

const selectedAnswer = ref(null)
const roundResult = ref(null)
const submitLoading = ref(false)
const isLastQuestion = ref(false)
const nextCountdown = ref(3)
const timeLeft = ref(30)
const timerInterval = ref(null)
const pollStop = ref(null)

// Computed
const progressPct = computed(() => {
  if (!quiz.value) return 0
  return (quiz.value.current_question / quiz.value.total_questions) * 100
})

const timerDash = computed(() => 2 * Math.PI * 34)
const timerOffset = computed(() => {
  const pct = timeLeft.value / (quiz.value?.current_round?.time_limit || 30)
  return timerDash.value * (1 - pct)
})
const timerColor = computed(() => {
  if (timeLeft.value > 15) return 'var(--accent)'
  if (timeLeft.value > 7) return 'var(--gold)'
  return 'var(--danger)'
})

const winnerIcon = computed(() => ({ user: '🏆', bot: '🤖', draw: '🤝' }[quiz.value?.winner] || ''))
const winnerTitle = computed(() => ({
  user: 'TABRIKLAYMIZ! YUTDINGIZ!', bot: 'BOT YUTDI!', draw: 'DURRANG!'
}[quiz.value?.winner] || ''))

// Helpers
const optLetter = (i) => ['A', 'B', 'C', 'D'][i]
const levelLabel = (l) => ({ beginner: 'Boshlang\'ich', easy: 'Yengil', medium: 'O\'rta', hard: 'Qiyin', expert: 'Ekspert' }[l] || l)
const diffLabel = (d) => ({ easy: 'Oson', medium: 'O\'rta', hard: 'Qiyin' }[d] || d)

// Timer
const startTimer = () => {
  clearInterval(timerInterval.value)
  timeLeft.value = quiz.value?.current_round?.time_limit || 30
  timerInterval.value = setInterval(() => {
    timeLeft.value--
    if (timeLeft.value <= 0) {
      clearInterval(timerInterval.value)
      if (!selectedAnswer.value) selectAnswer('A', '', true) // auto-submit
    }
  }, 1000)
}

const selectAnswer = async (letter, _, timedOut = false) => {
  if (selectedAnswer.value || submitLoading.value) return
  selectedAnswer.value = letter
  clearInterval(timerInterval.value)
  submitLoading.value = true

  const timeTaken = ((quiz.value?.current_round?.time_limit || 30) - timeLeft.value) * 1000

  try {
    const data = await submitAnswer(quiz.value.id, letter, timeTaken)
    roundResult.value = data.round
    isLastQuestion.value = data.quiz.is_finished

    if (!data.quiz.is_finished) {
      // Next question countdown
      nextCountdown.value = 3
      const cdInterval = setInterval(() => {
        nextCountdown.value--
        if (nextCountdown.value <= 0) {
          clearInterval(cdInterval)
          roundResult.value = null
          selectedAnswer.value = null
          // Refresh quiz for next round
          fetchQuiz(quiz.value.id).then(() => startTimer())
        }
      }, 1000)
    } else {
      await fetchQuiz(quiz.value.id)
    }
  } finally {
    submitLoading.value = false
  }
}

const cancelQuiz = async () => {
  await post(`/quiz/${quiz.value.id}/cancel`, {})
  navigateTo('/bots')
}

onMounted(async () => {
  await fetchQuiz(Number(route.params.id))

  if (quiz.value?.status === 'waiting') {
    // Poll for status change
    pollStop.value = pollQuizStatus(quiz.value.id, (data) => {
      if (data.status === 'active') {
        startTimer()
      }
    })
  } else if (quiz.value?.status === 'active') {
    startTimer()
  }
})

onUnmounted(() => {
  clearInterval(timerInterval.value)
  pollStop.value?.()
})
</script>

<style scoped>
.quiz-wrapper { min-height: calc(100vh - 70px); }

.state-screen { padding-bottom: 3rem; }

.queue-card {
  background: var(--surface);
  border: 1px solid rgba(255,215,0,0.3);
  border-radius: 16px;
  padding: 2.5rem;
  text-align: center;
}

.queue-icon-big { font-size: 3rem; margin-bottom: 1rem; }
.queue-title { font-family: 'Orbitron', monospace; font-size: 1.25rem; margin-bottom: 1.5rem; color: var(--text-muted); }
.queue-number { font-family: 'Orbitron', monospace; font-size: 6rem; font-weight: 900; color: var(--gold); line-height: 1; text-shadow: 0 0 40px rgba(255,215,0,0.5); }
.queue-label { font-family: 'Orbitron', monospace; font-size: 1rem; color: var(--gold); margin-bottom: 1.5rem; }
.queue-sub { color: var(--text-muted); line-height: 1.7; margin-bottom: 1.5rem; }
.queue-bot-info { display: flex; align-items: center; gap: 1rem; background: var(--surface2); border-radius: 10px; padding: 1rem; margin-bottom: 1.5rem; }
.qbot-avatar { width: 44px; height: 44px; border-radius: 10px; background: linear-gradient(135deg, var(--accent2), var(--accent)); display: flex; align-items: center; justify-content: center; font-family: 'Orbitron', monospace; font-size: 1.25rem; font-weight: 900; color: #fff; }
.qbot-name { font-family: 'Orbitron', monospace; font-weight: 700; margin-bottom: 0.25rem; }
.queue-refresh { display: flex; align-items: center; justify-content: center; gap: 0.5rem; color: var(--text-muted); font-size: 0.85rem; }
.refresh-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--accent); animation: pulse 1.5s infinite; }

/* Game screen */
.game-top { margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 1rem; }
.progress-info { display: flex; align-items: center; gap: 1rem; }
.q-count { font-family: 'Orbitron', monospace; font-size: 0.85rem; font-weight: 700; color: var(--text-muted); white-space: nowrap; }

.question-card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 1.75rem; }
.question-meta { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.25rem; flex-wrap: wrap; }
.diff-tag { padding: 0.2rem 0.65rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.05em; }
.diff-easy { background: rgba(0,230,118,0.15); color: var(--success); }
.diff-medium { background: rgba(255,215,0,0.15); color: var(--gold); }
.diff-hard { background: rgba(255,61,110,0.15); color: var(--danger); }
.points-tag { margin-left: auto; font-family: 'Orbitron', monospace; font-size: 0.8rem; color: var(--accent); font-weight: 700; }

.question-top { display: flex; align-items: flex-start; gap: 1.25rem; margin-bottom: 1.5rem; }
.question-text { font-size: 1.2rem; font-weight: 600; line-height: 1.6; flex: 1; }

.timer-ring { position: relative; width: 80px; height: 80px; flex-shrink: 0; }
.timer-ring svg { transform: rotate(-90deg); }
.timer-text { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-family: 'Orbitron', monospace; font-size: 1.4rem; font-weight: 900; }

.opt-letter { display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 6px; background: rgba(255,255,255,0.08); font-family: 'Orbitron', monospace; font-size: 0.75rem; font-weight: 700; flex-shrink: 0; }

/* Round result */
.round-result { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 1.75rem; }
.result-row { display: grid; grid-template-columns: 1fr auto 1fr; gap: 1rem; align-items: center; margin-bottom: 1.25rem; }
.result-side { text-align: center; padding: 1.25rem; border-radius: 12px; border: 2px solid var(--border); }
.result-side.correct { border-color: var(--success); background: rgba(0,230,118,0.08); }
.result-side.wrong { border-color: var(--danger); background: rgba(255,61,110,0.08); }
.result-icon { font-size: 2rem; margin-bottom: 0.5rem; }
.result-label { font-size: 0.75rem; color: var(--text-muted); letter-spacing: 0.1em; font-weight: 700; margin-bottom: 0.5rem; }
.result-answer { font-family: 'Orbitron', monospace; font-size: 1.5rem; font-weight: 900; margin-bottom: 0.25rem; }
.result-pts { font-size: 0.85rem; font-weight: 700; color: var(--accent); }

.result-divider { display: flex; flex-direction: column; align-items: center; gap: 0.5rem; }
.correct-answer-box { background: var(--surface2); border: 1px solid var(--border); border-radius: 10px; padding: 1rem; text-align: center; }
.ca-label { font-size: 0.65rem; color: var(--text-muted); letter-spacing: 0.15em; margin-bottom: 0.4rem; }
.ca-value { font-family: 'Orbitron', monospace; font-size: 1.75rem; font-weight: 900; color: var(--gold); }

.explanation { background: rgba(0,245,212,0.06); border: 1px solid rgba(0,245,212,0.15); border-radius: 10px; padding: 1rem; font-size: 0.95rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1rem; }

.next-countdown { text-align: center; font-family: 'Orbitron', monospace; font-size: 0.9rem; color: var(--text-muted); }

/* Final result */
.result-card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; }
.winner-banner { padding: 2.5rem; text-align: center; }
.winner-banner.user { background: linear-gradient(135deg, rgba(0,245,212,0.15), rgba(123,47,255,0.15)); }
.winner-banner.bot { background: linear-gradient(135deg, rgba(255,61,110,0.1), transparent); }
.winner-banner.draw { background: linear-gradient(135deg, rgba(255,215,0,0.1), transparent); }
.winner-icon { font-size: 4rem; margin-bottom: 1rem; }
.winner-title { font-family: 'Orbitron', monospace; font-size: 1.5rem; font-weight: 900; }
.result-actions { display: flex; gap: 1rem; padding: 1.5rem; border-top: 1px solid var(--border); }
</style>