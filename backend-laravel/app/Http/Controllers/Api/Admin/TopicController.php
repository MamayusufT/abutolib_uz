<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TopicResource;
use App\Models\Topic;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TopicController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $topics = Topic::with('subject')
            ->withCount('questions')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->subject_id, fn($q) => $q->where('subject_id', $request->subject_id))
            ->when($request->has('is_active'), fn($q) => $q->where('is_active', $request->boolean('is_active')))
            ->orderBy('subject_id')
            ->orderBy('order')
            ->paginate($request->per_page ?? 15);

        return TopicResource::collection($topics);
    }

    public function store(Request $request): TopicResource
    {
        $request->validate([
            'subject_id'                       => 'required|exists:subjects,id',
            'name'                             => 'required|string|max:255',
            'description'                      => 'nullable|string',
            'order'                            => 'nullable|integer|min:0',
            'time_limit'                       => 'nullable|integer|min:1',
            'is_active'                        => 'boolean',
            'questions'                        => 'nullable|array',
            'questions.*.question'             => 'required|string',
            'questions.*.difficulty'           => 'nullable|in:easy,medium,hard',
            'questions.*.type'                 => 'required|in:single,multiple,open,match',
            'questions.*.order'                => 'nullable|integer',
            'questions.*.answers'              => 'nullable|array',
            'questions.*.answers.*.answer'     => 'required|string',
            'questions.*.answers.*.is_correct' => 'boolean',
            'questions.*.match_pairs'          => 'nullable|array',
            'questions.*.match_pairs.*.left'   => 'required|string',
            'questions.*.match_pairs.*.right'  => 'required|string',
        ]);

        $topic = Topic::create([
            'subject_id' => $request->subject_id,
            'name'       => $request->name,
            'description'=> $request->description,
            'order'      => $request->order ?? 0,
            'time_limit' => $request->time_limit,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        $this->syncQuestions($topic, $request->questions ?? []);

        return new TopicResource($topic->load(['subject', 'questions.answers', 'questions.matchPairs']));
    }

    public function show(Topic $topic): TopicResource
    {
        return new TopicResource($topic->load(['subject', 'questions.answers', 'questions.matchPairs']));
    }

    public function update(Request $request, Topic $topic): TopicResource
    {
        $request->validate([
            'subject_id'                       => 'required|exists:subjects,id',
            'name'                             => 'required|string|max:255',
            'description'                      => 'nullable|string',
            'order'                            => 'nullable|integer|min:0',
            'time_limit'                       => 'nullable|integer|min:1',
            'is_active'                        => 'boolean',
            'questions'                        => 'nullable|array',
            'questions.*.id'                   => 'nullable|integer|exists:questions,id',
            'questions.*.question'             => 'required|string',
            'questions.*.difficulty'           => 'nullable|in:easy,medium,hard',
            'questions.*.type'                 => 'required|in:single,multiple,open,match',
            'questions.*.order'                => 'nullable|integer',
            'questions.*.answers'              => 'nullable|array',
            'questions.*.answers.*.answer'     => 'required|string',
            'questions.*.answers.*.is_correct' => 'boolean',
            'questions.*.match_pairs'          => 'nullable|array',
            'questions.*.match_pairs.*.left'   => 'required|string',
            'questions.*.match_pairs.*.right'  => 'required|string',
            'deleted_question_ids'             => 'nullable|array',
            'deleted_question_ids.*'           => 'integer|exists:questions,id',
        ]);

        $topic->update([
            'subject_id' => $request->subject_id,
            'name'       => $request->name,
            'description'=> $request->description,
            'order'      => $request->order ?? $topic->order,
            'time_limit' => $request->time_limit,
            'is_active'  => $request->boolean('is_active', $topic->is_active),
        ]);

        if ($request->filled('deleted_question_ids')) {
            Question::whereIn('id', $request->deleted_question_ids)
                ->where('topic_id', $topic->id)
                ->delete();
        }

        $this->syncQuestions($topic, $request->questions ?? []);

        return new TopicResource($topic->load(['subject', 'questions.answers', 'questions.matchPairs']));
    }

    public function destroy(Topic $topic): JsonResponse
    {
        $topic->delete();
        return response()->json(['message' => 'Topic deleted successfully']);
    }

    private function syncQuestions(Topic $topic, array $questions): void
    {
        foreach ($questions as $index => $qData) {

            $question = isset($qData['id'])
                ? Question::where('id', $qData['id'])->where('topic_id', $topic->id)->first()
                : new Question(['topic_id' => $topic->id]);

            if (!$question) continue;

            $question->fill([
                'question'   => $qData['question'],
                'difficulty' => $qData['difficulty'] ?? 'medium',
                'type'       => $qData['type'] ?? 'single',
                'order'      => $qData['order'] ?? $index,
            ])->save();

            if (in_array($question->type, ['single', 'multiple'])) {
                $question->matchPairs()->delete();
                $question->answers()->delete();

                foreach ($qData['answers'] ?? [] as $aData) {
                    $question->answers()->create([
                        'answer'     => $aData['answer'],
                        'is_correct' => $aData['is_correct'] ?? false,
                    ]);
                }
            }

            if ($question->type === 'match') {
                $question->answers()->delete();
                $question->matchPairs()->delete();

                foreach ($qData['match_pairs'] ?? [] as $i => $pair) {
                    $question->matchPairs()->create([
                        'left'  => $pair['left'],
                        'right' => $pair['right'],
                        'order' => $i,
                    ]);
                }
            }

            if ($question->type === 'open') {
                $question->answers()->delete();
                $question->matchPairs()->delete();
            }
        }
    }
}
