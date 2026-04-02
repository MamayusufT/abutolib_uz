export interface Answer {
  id:     number
  answer: string
}

export interface Question {
  id:         number
  question:   string
  difficulty: string
  answers:    Answer[]
}

export interface LeaderboardRow {
  rank:               number
  user_id:            number
  name:               string
  score_percent:      number
  correct_answers:    number
  wrong_answers:      number
  skipped_questions:  number
  time_spent:         number
  finished_at:        string
}

export interface Participant {
  id:            number
  name:          string
  registered_at: string
}

export interface MyResult {
  status:             string
  score_percent:      number
  correct_answers:    number
  wrong_answers:      number
  skipped_questions:  number
  time_spent:         number
}

export type OlympiadStatus = 'upcoming' | 'active' | 'ended'