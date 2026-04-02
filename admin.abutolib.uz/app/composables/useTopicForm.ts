import type { TopicFormData, QuestionForm } from '~/types/question'

export function useTopicForm() {
  const form = reactive<TopicFormData>({
    subject_id: null,
    name: '',
    description: '',
    order: 0,
    time_limit: null,
    is_active: true,
  })

  const questions = ref<QuestionForm[]>([])
  const deletedQuestionIds = ref<number[]>([])

  function addQuestion() {
    questions.value.push({
      question: '',
      difficulty: 'medium',
      type: 'single',
      order: questions.value.length,
      answers: [
        { answer: '', is_correct: true },
        { answer: '', is_correct: false },
      ],
      match_pairs: [],
    })
  }

  function removeQuestion(index: number) {
    const q = questions.value[index]
    if (q.id) deletedQuestionIds.value.push(q.id)
    questions.value.splice(index, 1)
  }

  function onTypeChange(qIndex: number) {
    const q = questions.value[qIndex]
    if (q.type === 'single' || q.type === 'multiple') {
      q.answers = [
        { answer: '', is_correct: true },
        { answer: '', is_correct: false },
      ]
      q.match_pairs = []
    } else if (q.type === 'open') {
      q.answers = []
      q.match_pairs = []
    } else if (q.type === 'match') {
      q.answers = []
      q.match_pairs = [
        { left: '', right: '', order: 0 },
        { left: '', right: '', order: 1 },
      ]
    }
  }

  function addAnswer(qIndex: number) {
    questions.value[qIndex].answers.push({ answer: '', is_correct: false })
  }

  function removeAnswer(qIndex: number, aIndex: number) {
    questions.value[qIndex].answers.splice(aIndex, 1)
  }

  function setCorrect(qIndex: number, aIndex: number) {
    const q = questions.value[qIndex]
    if (q.type === 'single') {
      q.answers.forEach((a, i) => { a.is_correct = i === aIndex })
    } else {
      q.answers[aIndex].is_correct = !q.answers[aIndex].is_correct
    }
  }

  function addMatchPair(qIndex: number) {
    const pairs = questions.value[qIndex].match_pairs
    pairs.push({ left: '', right: '', order: pairs.length })
  }

  function removeMatchPair(qIndex: number, pIndex: number) {
    questions.value[qIndex].match_pairs.splice(pIndex, 1)
  }

  function buildPayload() {
    return {
      subject_id: form.subject_id,
      name: form.name,
      description: form.description,
      order: form.order,
      time_limit: form.time_limit,
      is_active: form.is_active,
      questions: questions.value,
      deleted_question_ids: deletedQuestionIds.value,
    }
  }

  return {
    form,
    questions,
    deletedQuestionIds,
    addQuestion,
    removeQuestion,
    onTypeChange,
    addAnswer,
    removeAnswer,
    setCorrect,
    addMatchPair,
    removeMatchPair,
    buildPayload,
  }
}