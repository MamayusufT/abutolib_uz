<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Subject;
use App\Models\SubjectFile;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\SubjectResource;

class SubjectController extends Controller
{
    public function downloadFile($id)
    {
        $file = SubjectFile::findOrFail($id);

        if (!Storage::disk('public')->exists($file->file_path)) {
            return response()->json(['message' => 'Fayl topilmadi'], 404);
        }

        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }

    public function index(Request $request)
    {
        $query = Subject::where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $subjects = $query->withCount(['topics'])
            ->orderBy('order')
            ->paginate($request->get('per_page', 6));

        return SubjectResource::collection($subjects);
    }

    public function show($slug)
    {
        $subject = Subject::where('slug', $slug)
            ->where('is_active', true)
            ->with(['files', 'topics.questions'])
            ->with(['topics' => fn($q) => $q->withCount('questions')])
            ->firstOrFail();

        return new SubjectResource($subject);
    }

    public function questions($topicId)
    {
        $topic = Topic::with('subject:id,name,slug,color')
            ->withCount('questions')
            ->findOrFail($topicId);

        $questions = Question::where('topic_id', $topicId)
            ->with(['answers:id,question_id,answer','matchPairs:id,question_id,left,right,order'])
            ->inRandomOrder()
            ->get()
            ->map(function ($question) {
                return [
                    'id'          => $question->id,
                    'question'    => $question->question,
                    'difficulty'  => $question->difficulty,
                    'type'        => $question->type,
                    'order'       => $question->order,
                    'answers'     => $question->type !== 'open' ? $question->answers : [],
                    'match_pairs' => $question->type === 'match' ? $question->matchPairs : [],
                ];
            });

        return response()->json([
            'topic'     => $topic,
            'questions' => $questions,
        ]);
    }
}
