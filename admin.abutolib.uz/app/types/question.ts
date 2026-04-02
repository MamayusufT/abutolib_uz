export type QuestionType = 'single' | 'multiple' | 'open' | 'match'

export interface AnswerForm {
  id?: number
  answer: string
  is_correct: boolean
}

export interface MatchPairForm {
  id?: number
  left: string
  right: string
  order: number
}

export interface QuestionForm {
  id?: number
  question: string
  difficulty: 'easy' | 'medium' | 'hard'
  type: QuestionType
  order: number
  answers: AnswerForm[]
  match_pairs: MatchPairForm[]
}

export interface TopicFormData {
  subject_id: number | null
  name: string
  description: string
  order: number
  time_limit: number | null
  is_active: boolean
}