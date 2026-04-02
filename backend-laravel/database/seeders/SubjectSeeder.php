<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\MatchPair;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            [
                'name' => 'Matematika',
                'color' => '#3b82f6',
                'image' => 'https://images.unsplash.com/photo-1635070041078-e363dbe005cb?w=400',
                'description' => 'Algebra va boshqa bo‘limlar',
                'topics' => [
                    [
                        'name' => 'Algebra',
                        'questions' => [
                            [
                                'type' => 'match',
                                'q' => 'Moslang',
                                'pairs' => [
                                    ['left' => '2 + 2', 'right' => '4'],
                                    ['left' => '3 × 3', 'right' => '9'],
                                    ['left' => '√16', 'right' => '4'],
                                ]
                            ],
                            [
                                'type' => 'single',
                                'q' => '2 + 2 = ?',
                                'answers' => ['3','4','5','6'],
                                'correct' => 1
                            ],
                        ]
                    ]
                ]
            ]
        ];

        foreach ($subjects as $order => $subjectData) {
            $subject = Subject::create([
                'name'        => $subjectData['name'],
                'slug'        => Str::slug($subjectData['name']),
                'color'       => $subjectData['color'],
                'image'       => $subjectData['image'],
                'description' => $subjectData['description'],
                'order'       => $order + 1,
                'is_active'   => true,
            ]);

            foreach ($subjectData['topics'] as $topicOrder => $topicData) {
                $topic = Topic::create([
                    'subject_id' => $subject->id,
                    'name'       => $topicData['name'],
                    'order'      => $topicOrder + 1,
                    'is_active'  => true,
                ]);

                foreach ($topicData['questions'] as $qOrder => $qData) {
                    $question = Question::create([
                        'topic_id'   => $topic->id,
                        'question'   => $qData['q'],
                        'type'       => $qData['type'],
                        'difficulty' => ['easy', 'medium', 'hard'][rand(0, 2)],
                        'order'      => $qOrder + 1,
                    ]);

                    if ($qData['type'] === 'open') {
                        continue;
                    }

                    if ($qData['type'] === 'match') {
                        foreach ($qData['pairs'] as $i => $pair) {
                            MatchPair::create([
                                'question_id' => $question->id,
                                'left'  => $pair['left'],
                                'right' => $pair['right'],
                                'order' => $i,
                            ]);
                        }
                        continue;
                    }

                    foreach ($qData['answers'] as $aIndex => $answerText) {
                        $isCorrect = match($qData['type']) {
                            'single'   => $aIndex === $qData['correct'],
                            'multiple' => in_array($aIndex, $qData['correct']),
                            default    => false,
                        };

                        Answer::create([
                            'question_id' => $question->id,
                            'answer'      => $answerText,
                            'is_correct'  => $isCorrect,
                        ]);
                    }
                }
            }
        }

        $this->command->info('Seeder ishladi ✅');
    }
}
