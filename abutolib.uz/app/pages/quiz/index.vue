<!-- pages/bots/index.vue -->
<template>
  <div class="container" style="padding-top: 3rem; padding-bottom: 3rem;">
    <div class="page-header">
      <h1>🤖 Botlar</h1>
      <p>Raqib tanlang va o'yin boshlang</p>
    </div>

    <!-- Loading -->
    <div v-if="pending" class="loading-state">
      <div class="spinner"></div>
      <p>Botlar yuklanmoqda...</p>
    </div>

    <!-- Bots grid -->
    <div v-else class="bots-grid fade-in">
      <div
        v-for="bot in bots"
        :key="bot.id"
        class="bot-card card"
      >
        <!-- Status bar -->
        <div class="bot-status-bar" :class="bot.is_busy ? 'busy' : 'free'">
          <span class="status-dot-inline"></span>
          {{ bot.is_busy ? 'Band' : 'Bo\'sh' }}
          <span v-if="bot.queue_count > 0" class="queue-badge">
            {{ bot.queue_count }} navbatda
          </span>
        </div>

        <div class="bot-card-body">
          <!-- Avatar + Level -->
          <div class="bot-header">
            <div class="bot-avatar">{{ bot.name[0] }}</div>
            <div>
              <div class="bot-name">{{ bot.name }}</div>
              <span :class="`level-badge level-${bot.level}`">{{ levelLabel(bot.level) }}</span>
            </div>
            <div class="bot-rating">
              <div class="rating-val">{{ bot.rating }}</div>
              <div class="rating-lbl">rating</div>
            </div>
          </div>

          <!-- Description -->
          <p class="bot-desc">{{ bot.description }}</p>

          <!-- Topics -->
          <div class="bot-topics">
            <span v-for="t in bot.topics" :key="t" class="topic-tag">{{ t }}</span>
          </div>

          <!-- Action -->
          <div class="bot-footer">
            <div v-if="bot.user_queue_position" class="user-in-queue">
              ⏳ Siz {{ bot.user_queue_position }}-navbatdasiz
            </div>
            <template v-else>
              <button
                class="btn btn-primary"
                style="width:100%"
                @click="openChallenge(bot)"
                :disabled="!isLoggedIn"
              >
                {{ bot.is_busy ? '⏳ Navbatga turish' : '⚔️ Bellashuv' }}
              </button>
              <p v-if="!isLoggedIn" class="login-hint">
                O'yin uchun <NuxtLink to="/auth/login">kiring</NuxtLink>
              </p>
            </template>
          </div>
        </div>
      </div>
    </div>

    <!-- Challenge Modal -->
    <div v-if="challengeModal" class="modal-overlay" @click.self="challengeModal = null">
      <div class="modal scale-in">
        <div class="modal-header">
          <h2>⚔️ {{ challengeModal.name }} bilan bellashuv</h2>
          <button class="modal-close" @click="challengeModal = null">✕</button>
        </div>

        <div class="modal-body">
          <div class="modal-bot-info">
            <div class="modal-bot-avatar">{{ challengeModal.name[0] }}</div>
            <div>
              <div class="modal-bot-name">{{ challengeModal.name }}</div>
              <span :class="`level-badge level-${challengeModal.level}`">{{ levelLabel(challengeModal.level) }}</span>
            </div>
          </div>

          <div v-if="challengeModal.is_busy" class="queue-info">
            <div class="queue-icon">⏳</div>
            <div>
              <div class="queue-title">Bot hozir band</div>
              <div class="queue-sub">{{ challengeModal.queue_count }} nafar navbatda. Siz {{ challengeModal.queue_count + 1 }}-o'rinda bo'lasiz.</div>
            </div>
          </div>

          <!-- Settings -->
          <div class="settings-group">
            <label class="form-label">Savollar soni</label>
            <div class="options-row">
              <button
                v-for="n in [5, 10, 15, 20]"
                :key="n"
                class="opt-btn"
                :class="{ active: totalQ === n }"
                @click="totalQ = n"
              >{{ n }}</button>
            </div>
          </div>

          <div class="settings-group">
            <label class="form-label">Har bir savol vaqti (soniya)</label>
            <div class="options-row">
              <button
                v-for="t in [15, 30, 45, 60]"
                :key="t"
                class="opt-btn"
                :class="{ active: timeQ === t }"
                @click="timeQ = t"
              >{{ t }}s</button>
            </div>
          </div>

          <div class="alert alert-error" v-if="error">{{ error }}</div>

          <button
            class="btn btn-primary"
            style="width:100%; margin-top:1rem; font-size:1rem; padding:0.9rem"
            @click="startChallenge"
            :disabled="loading"
          >
            {{ loading ? 'Yuklanmoqda...' : challengeModal.is_busy ? '⏳ Navbatga qo\'shilish' : '🚀 Boshlash' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const { isLoggedIn } = useAuth()
const { challenge, loading } = useQuiz()
const { get } = useApi()

const bots = ref([])
const pending = ref(true)
const challengeModal = ref(null)
const totalQ = ref(10)
const timeQ = ref(30)
const error = ref('')

const levelLabel = (l) => ({
  beginner: 'Boshlang\'ich', easy: 'Yengil', medium: 'O\'rta',
  hard: 'Qiyin', expert: 'Ekspert'
}[l] || l)

onMounted(async () => {
  try {
    const data = await get('/bots')
    bots.value = data.bots
  } finally {
    pending.value = false
  }
})

const openChallenge = (bot) => {
  error.value = ''
  challengeModal.value = bot
}

const startChallenge = async () => {
  error.value = ''
  try {
    const data = await challenge(challengeModal.value.id, {
      total_questions: totalQ.value,
      time_per_question: timeQ.value,
    })
    navigateTo(`/quiz/${data.quiz.id}`)
  } catch (e) {
    error.value = e?.data?.message || 'Xatolik yuz berdi'
  }
}
</script>

<style scoped>
.page-header { margin-bottom: 2.5rem; }
.page-header h1 { font-family: 'Orbitron', monospace; font-size: 2rem; margin-bottom: 0.5rem; }
.page-header p { color: var(--text-muted); font-size: 1.1rem; }

.loading-state {
  display: flex; flex-direction: column; align-items: center; gap: 1rem;
  padding: 4rem; color: var(--text-muted);
}

.spinner {
  width: 40px; height: 40px;
  border: 3px solid var(--border);
  border-top-color: var(--accent);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

.bot-card { cursor: default; }

.bot-status-bar {
  display: flex; align-items: center; gap: 0.5rem;
  padding: 0.5rem 1rem;
  font-size: 0.8rem; font-weight: 700; letter-spacing: 0.1em;
}

.bot-status-bar.busy { background: rgba(255,61,110,0.1); color: var(--danger); }
.bot-status-bar.free { background: rgba(0,230,118,0.1); color: var(--success); }

.status-dot-inline {
  width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0;
  background: currentColor;
}

.busy .status-dot-inline { animation: pulse 1.5s infinite; }

.queue-badge {
  margin-left: auto;
  background: rgba(255,215,0,0.15);
  color: var(--gold);
  border-radius: 20px;
  padding: 0.15rem 0.6rem;
  font-size: 0.75rem;
}

.bot-card-body { padding: 1.25rem; }

.bot-header {
  display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem;
}

.bot-avatar {
  width: 48px; height: 48px; border-radius: 12px;
  background: linear-gradient(135deg, var(--accent2), var(--accent));
  display: flex; align-items: center; justify-content: center;
  font-family: 'Orbitron', monospace; font-size: 1.25rem; font-weight: 900; color: #fff;
  flex-shrink: 0;
}

.bot-name { font-family: 'Orbitron', monospace; font-weight: 700; font-size: 1.1rem; margin-bottom: 0.25rem; }

.bot-rating { margin-left: auto; text-align: right; }
.rating-val { font-family: 'Orbitron', monospace; font-size: 1.25rem; font-weight: 900; color: var(--gold); }
.rating-lbl { font-size: 0.7rem; color: var(--text-muted); letter-spacing: 0.1em; }

.bot-desc { color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 0.75rem; }

.bot-topics { display: flex; flex-wrap: wrap; gap: 0.4rem; margin-bottom: 1rem; }

.topic-tag {
  padding: 0.2rem 0.6rem;
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 20px;
  font-size: 0.75rem;
  color: var(--text-muted);
}

.bot-footer { display: flex; flex-direction: column; gap: 0.5rem; }

.login-hint { font-size: 0.85rem; color: var(--text-muted); text-align: center; }
.login-hint a { color: var(--accent); }

.user-in-queue {
  background: rgba(255,215,0,0.1);
  border: 1px solid rgba(255,215,0,0.3);
  color: var(--gold);
  padding: 0.75rem;
  border-radius: 8px;
  font-weight: 700;
  text-align: center;
}

/* Modal */
.modal-overlay {
  position: fixed; inset: 0;
  background: rgba(0,0,0,0.8);
  backdrop-filter: blur(4px);
  display: flex; align-items: center; justify-content: center;
  z-index: 1000; padding: 1rem;
}

.modal {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 16px;
  width: 100%; max-width: 480px;
  max-height: 90vh; overflow-y: auto;
}

.modal-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 1.5rem; border-bottom: 1px solid var(--border);
}

.modal-header h2 { font-family: 'Orbitron', monospace; font-size: 1.1rem; }

.modal-close {
  background: none; border: none; color: var(--text-muted);
  font-size: 1.25rem; cursor: pointer; padding: 0.25rem;
  transition: color 0.2s;
}
.modal-close:hover { color: var(--text); }

.modal-body { padding: 1.5rem; }

.modal-bot-info {
  display: flex; align-items: center; gap: 1rem;
  padding: 1rem; background: var(--surface2); border-radius: 10px;
  margin-bottom: 1.5rem;
}

.modal-bot-avatar {
  width: 52px; height: 52px; border-radius: 12px;
  background: linear-gradient(135deg, var(--accent2), var(--accent));
  display: flex; align-items: center; justify-content: center;
  font-family: 'Orbitron', monospace; font-size: 1.5rem; font-weight: 900; color: #fff;
}

.modal-bot-name { font-family: 'Orbitron', monospace; font-weight: 700; font-size: 1.1rem; margin-bottom: 0.3rem; }

.queue-info {
  display: flex; align-items: flex-start; gap: 1rem;
  padding: 1rem; background: rgba(255,215,0,0.08);
  border: 1px solid rgba(255,215,0,0.25); border-radius: 10px;
  margin-bottom: 1.5rem;
}

.queue-icon { font-size: 1.75rem; }
.queue-title { font-weight: 700; color: var(--gold); margin-bottom: 0.25rem; }
.queue-sub { color: var(--text-muted); font-size: 0.9rem; line-height: 1.5; }

.settings-group { margin-bottom: 1.25rem; }

.options-row { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 0.5rem; }

.opt-btn {
  padding: 0.5rem 1rem;
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 8px;
  color: var(--text);
  font-family: 'Orbitron', monospace;
  font-size: 0.8rem; font-weight: 700;
  cursor: pointer; transition: all 0.2s;
}

.opt-btn:hover { border-color: var(--accent); }
.opt-btn.active {
  border-color: var(--accent);
  background: rgba(0,245,212,0.1);
  color: var(--accent);
}
</style>