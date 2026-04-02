export type QuestionType = 'single' | 'multiple' | 'open'
export type Difficulty = 'easy' | 'medium' | 'hard'

export interface OlympiadAnswerForm {
  id?: number
  answer: string
  is_correct: boolean
}

export interface OlympiadQuestionForm {
  id?: number
  question: string
  type: QuestionType
  difficulty: Difficulty
  order: number
  answers: OlympiadAnswerForm[]
}

export interface OlympiadFormData {
  name_uz: string
  type: 'quiz' | 'olympiad' | 'exam'
  lang: string
  description_uz: string
  starts_at: string
  ends_at: string
  duration: number
  pass_score: number
  max_attempts: number
  questions_count: number | null
  show_answers: 'never' | 'after_submission' | 'after_finish'
  shuffle_questions: boolean
  shuffle_options: boolean
  is_active: boolean
}

export function useOlympiadForm() {
  const form = reactive<OlympiadFormData>({
    name_uz: '',
    type: 'olympiad',
    lang: 'uz',
    description_uz: '',
    starts_at: '',
    ends_at: '',
    duration: 60,
    pass_score: 60,
    max_attempts: 1,
    questions_count: null,
    show_answers: 'after_submission',
    shuffle_questions: true,
    shuffle_options: true,
    is_active: true,
  })

  const questions = ref<OlympiadQuestionForm[]>([])
  const deletedQuestionIds = ref<number[]>([])

  function addQuestion(type: QuestionType = 'single') {
    questions.value.push({
      question: '',
      type,
      difficulty: 'medium',
      order: questions.value.length,
      answers: type === 'open' ? [] : [
        { answer: '', is_correct: true },
        { answer: '', is_correct: false },
        { answer: '', is_correct: false },
        { answer: '', is_correct: false },
      ],
    })
  }

  function removeQuestion(index: number) {
    const q = questions.value[index]
    if (q.id) deletedQuestionIds.value.push(q.id)
    questions.value.splice(index, 1)
  }

  function addAnswer(qIndex: number) {
    questions.value[qIndex].answers.push({ answer: '', is_correct: false })
  }

  function removeAnswer(qIndex: number, aIndex: number) {
    questions.value[qIndex].answers.splice(aIndex, 1)
  }

  function setCorrectSingle(qIndex: number, aIndex: number) {
    questions.value[qIndex].answers.forEach((a, i) => {
      a.is_correct = i === aIndex
    })
  }

  function toggleCorrectMultiple(qIndex: number, aIndex: number) {
    questions.value[qIndex].answers[aIndex].is_correct =
      !questions.value[qIndex].answers[aIndex].is_correct
  }

  function isQuestionEmpty(q: OlympiadQuestionForm): boolean {
    const text = q.question?.replace(/<[^>]*>/g, '').trim()
    return !text
  }

  function buildPayload() {
    return {
      ...toRaw(form),
      questions_count: form.questions_count || null,
      questions: questions.value
        .filter(q => !isQuestionEmpty(q))
        .map((q, i) => ({
          id: q.id,
          question: q.question,
          type: q.type,
          difficulty: q.difficulty,
          order: i,
          answers: q.type === 'open'
            ? []
            : q.answers
                .filter(a => a.answer?.trim())
                .map(a => ({
                  id: a.id,
                  answer: a.answer.trim(),
                  is_correct: a.is_correct ?? false,
                })),
        })),
      deleted_question_ids: deletedQuestionIds.value,
    }
  }

  return {
    form,
    questions,
    deletedQuestionIds,
    addQuestion,
    removeQuestion,
    addAnswer,
    removeAnswer,
    setCorrectSingle,
    toggleCorrectMultiple,
    buildPayload,
  }
}