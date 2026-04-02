export const useTestSave = () => {
  const { $api } = useApi()
  let autoSaveTimer: any = null
  const resultId = ref<number | null>(null)

  function startAutoSave(
    topicId: number,
    getAnswers: () => any,
    getTimeSpent: () => number
  ) {
    stopAutoSave()
    autoSaveTimer = setInterval(async () => {
      await saveProgress(topicId, getAnswers(), getTimeSpent(), 'in_progress')
    }, 10000)
  }

  function stopAutoSave() {
    if (autoSaveTimer) {
      clearInterval(autoSaveTimer)
      autoSaveTimer = null
    }
  }

  async function saveProgress(
    topicId: number,
    answers: any,
    timeSpent: number,
    status: 'in_progress' | 'completed' | 'abandoned' = 'in_progress'
  ) {
    try {
      const res: any = await $api('/test/save', {
        method: 'POST',
        body: {
          topic_id: topicId,
          result_id: resultId.value,
          answers,
          time_spent: Math.floor(timeSpent),
          status,
        },
      })
      if (res?.result?.id) {
        resultId.value = res.result.id
      }
      return res
    } catch (err) {
      console.error('Error saving progress:', err)
    }
  }

  async function checkResume(topicId: number) {
    try {
      const res: any = await $api(`/test/resume/${topicId}`)
      if (res?.id) {
        resultId.value = res.id
      }
      return res
    } catch {
      return null
    }
  }

  function resetResultId() {
    resultId.value = null
  }

  return {
    resultId,
    saveProgress,
    checkResume,
    startAutoSave,
    stopAutoSave,
    resetResultId,
  }
}