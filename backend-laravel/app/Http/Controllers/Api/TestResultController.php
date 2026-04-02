<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\TestResult;
use App\Models\Topic;
use Illuminate\Http\Request;

class TestResultController extends Controller
{
    public function save(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'topic_id'        => 'required|exists:topics,id',
            'result_id'       => 'nullable|integer|exists:test_results,id',
            'answers'         => 'nullable|array',
            'time_spent'      => 'nullable|integer|min:0',
            'status'          => 'in:in_progress,completed,abandoned',
        ]);

        $topic = Topic::with(['questions.answers', 'questions.matchPairs'])->findOrFail($request->topic_id);

        $totalQuestions = $topic->questions->count();
        $userAnswers    = $request->answers ?? [];

        $correct = 0;
        $wrong   = 0;

        foreach ($userAnswers as $questionId => $userAnswer) {
            $question = $topic->questions->find($questionId);
            if (!$question) continue;

            switch ($question->type) {

                case 'single':
                    $answer = Answer::find($userAnswer);
                    if ($answer) {
                        $answer->is_correct ? $correct++ : $wrong++;
                    }
                    break;

                case 'multiple':
                    $selectedIds = collect((array) $userAnswer)->map(fn($id) => (int) $id)->sort()->values();
                    $correctIds  = $question->answers->where('is_correct', true)->pluck('id')->sort()->values();
                    if ($selectedIds->toArray() === $correctIds->toArray()) {
                        $correct++;
                    } else {
                        $wrong++;
                    }
                    break;

                case 'match':
                    $pairs = $question->matchPairs->sortBy('order')->values();
                    $isCorrect = true;

                    foreach ($pairs as $pair) {
                        if (!isset($userAnswer[$pair->id]) || $userAnswer[$pair->id] != $pair->right) {
                            $isCorrect = false;
                            break;
                        }
                    }

                    $isCorrect ? $correct++ : $wrong++;
                    break;

                case 'open':
                    break;
            }
        }

        $answered = count($userAnswers);
        $skipped  = $totalQuestions - $answered;

        $gradableTotal = $topic->questions->whereIn('type', ['single', 'multiple', 'match'])->count();

        $percent = $gradableTotal > 0
            ? round(($correct / $gradableTotal) * 100, 2)
            : 0;

        $status = $request->status ?? 'in_progress';

        if ($request->result_id) {
            $result = TestResult::where('id', $request->result_id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();
        } else {
            $result = TestResult::where('user_id', $request->user()->id)
                ->where('topic_id', $request->topic_id)
                ->where('status', 'in_progress')
                ->latest()
                ->first();

            if (!$result) {
                $result = new TestResult([
                    'user_id'    => $request->user()->id,
                    'topic_id'   => $request->topic_id,
                    'started_at' => now(),
                ]);
            }
        }

        $result->fill([
            'total_questions'    => $totalQuestions,
            'answered_questions' => $answered,
            'correct_answers'    => $correct,
            'wrong_answers'      => $wrong,
            'skipped_questions'  => $skipped,
            'score_percent'      => $percent,
            'time_spent'         => $request->time_spent ?? 0,
            'status'             => $status,
            'answers'            => $userAnswers,
            'finished_at'        => in_array($status, ['completed', 'abandoned']) ? now() : null,
        ]);

        $result->save();

        return response()->json([
            'result'  => $result->load('topic'),
            'message' => match($status) {
                'completed' => 'Test muvaffaqiyatli yakunlandi!',
                'abandoned' => 'Test to\'xtatildi va saqlandi.',
                default     => 'Jarayon saqlandi.',
            },
        ]);
    }

    public function index(Request $request)
    {
        $results = TestResult::where('user_id', $request->user()->id)
            ->with('topic:id,name,subject_id', 'topic.subject:id,name,slug,color')
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json($results);
    }

    public function show(Request $request, $id)
    {
        $result = TestResult::where('user_id', $request->user()->id)
            ->with('topic:id,name,subject_id', 'topic.subject:id,name,slug,color')
            ->findOrFail($id);

        return response()->json($result);
    }

    public function resume(Request $request, $topicId)
    {
        $result = TestResult::where('user_id', $request->user()->id)
            ->where('topic_id', $topicId)
            ->where('status', 'in_progress')
            ->with('topic')
            ->latest()
            ->first();

        return response()->json($result);
    }

    public function stats(Request $request)
    {
        $userId = $request->user()->id;

        $total    = TestResult::where('user_id', $userId)->where('status', 'completed')->count();
        $avgScore = TestResult::where('user_id', $userId)->where('status', 'completed')->avg('score_percent');
        $best     = TestResult::where('user_id', $userId)->where('status', 'completed')->max('score_percent');

        $recent = TestResult::where('user_id', $userId)
            ->where('status', 'completed')
            ->with('topic:id,name,subject_id', 'topic.subject:id,name,color')
            ->orderByDesc('finished_at')
            ->limit(5)
            ->get();

        return response()->json([
            'total_tests' => $total,
            'avg_score'   => round($avgScore ?? 0, 1),
            'best_score'  => round($best ?? 0, 1),
            'recent'      => $recent,
        ]);
    }
}
