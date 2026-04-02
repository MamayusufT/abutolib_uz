<?php

namespace App\Http\Controllers\Api;

use App\Events\OlympiadLeaderboardUpdated;
use App\Http\Controllers\Controller;
use App\Models\OlympiadRegistration;
use App\Models\OlympiadResult;
use App\Models\Olympiads;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OlympiadController extends Controller
{
    /**
     * Bugungi olimpiadalar — banner uchun
     * GET /api/olympiads/today
     */
    public function today()
    {
        $olympiads = Olympiads::today()
            ->active()
            ->orderBy('starts_at')
            ->get()
            ->map(fn($o) => [
                'id'               => $o->id,
                'title'            => $o->name_uz,
                'description'      => $o->description_uz,
                'time'             => $o->starts_at->format('H:i'),
                'ends_at'          => $o->ends_at?->format('H:i'),
                'duration_label'   => $o->duration_label,
                'status'           => $o->status,
                'status_label'     => $o->status_label,
                'time_until_start' => $o->time_until_start,
                'questions_count'  => $o->questions_count,
                'pass_score'       => $o->pass_score,
            ]);

        return response()->json($olympiads);
    }

    /**
     * Bitta olimpiada
     * GET /api/olympiads/{id}
     */

    // ─── Helper ──────────────────────────────────────────────

    private function subjectIcon(?string $subjectName): string
    {
        if (!$subjectName) return '🏆';

        $icons = [
            'Matematika'  => '📐',
            'Fizika'      => '⚛️',
            'Kimyo'       => '🧪',
            'Biologiya'   => '🧬',
            'Tarix'       => '📜',
            'Ingliz tili' => '🇬🇧',
            'Informatika' => '💻',
            'Geografiya'  => '🌍',
        ];

        return $icons[$subjectName] ?? '🏆';
    }

    private function getStatus(Olympiads $olympiad): string
    {
        $now   = now();
        $start = $olympiad->starts_at;
        $end   = $olympiad->ends_at;
        if ($start && $now->lt($start)) return 'upcoming';
        if ($end   && $now->gt($end))   return 'ended';
        return 'active';
    }

    public function participants(Olympiads $olympiad)
    {
        $rows = OlympiadRegistration::where('olympiad_id', $olympiad->id)
            ->with('user:id,name')
            ->orderBy('registered_at')
            ->get()
            ->map(fn($r) => [
                'id'            => $r->id,
                'name'          => $r->user->name,
                'registered_at' => $r->registered_at,
            ]);

        return response()->json($rows);
    }

    public function index()
    {
        return Olympiads::where('is_active', true)
            ->withCount('questions')
            ->orderByDesc('starts_at')
            ->get()
            ->map(function ($o) {
                $o->participants_count = OlympiadRegistration::where('olympiad_id', $o->id)
                    ->distinct('user_id')->count();
                return $o;
            });
    }

    public function show(Request $request, Olympiads $olympiad)
    {
        $olympiad->loadCount(['questions as real_questions_count']);

        $olympiad->participants_count = OlympiadResult::where('olympiad_id', $olympiad->id)
            ->distinct('user_id')->count();
        $olympiad->is_registered = OlympiadRegistration::where('olympiad_id', $olympiad->id)
            ->where('user_id', $request->user()->id)
            ->exists();
        $olympiad->status = $this->getStatus($olympiad);
        return response()->json($olympiad);
    }

    public function register(Request $request, Olympiads $olympiad)
    {
        if ($this->getStatus($olympiad) !== 'upcoming') {
            return response()->json(['message' => 'Faqat kutilayotgan olimpiadaga ro\'yhatdan o\'tish mumkin'], 403);
        }

        $exists = OlympiadRegistration::where('olympiad_id', $olympiad->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Allaqachon ro\'yhatdan o\'tgansiz'], 409);
        }

        $reg = OlympiadRegistration::create([
            'olympiad_id'   => $olympiad->id,
            'user_id'       => $request->user()->id,
            'registered_at' => now(),
        ]);

        return response()->json($reg, 201);
    }

    public function unregister(Request $request, Olympiads $olympiad): \Illuminate\Http\JsonResponse
    {
        if ($this->getStatus($olympiad) !== 'upcoming') {
            return response()->json(['message' => 'Faqat kutilayotgan olimpiadadan bekor qilish mumkin'], 403);
        }

        $deleted = OlympiadRegistration::where('olympiad_id', $olympiad->id)
            ->where('user_id', $request->user()->id)
            ->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Siz ro\'yhatdan o\'tmagansiz'], 404);
        }

        return response()->json(['message' => 'Muvaffaqiyatli bekor qilindi']);
    }

    public function myRegistration(Request $request, Olympiads $olympiad)
    {
        $reg = OlympiadRegistration::where('olympiad_id', $olympiad->id)
            ->where('user_id', $request->user()->id)
            ->first();

        return response()->json([
            'is_registered' => (bool) $reg,
            'registered_at' => $reg?->registered_at,
        ]);
    }

    public function questions(Olympiads $olympiad)
    {
        $this->assertActive($olympiad);

        $limit = (int) $olympiad->questions_count;

        $questions = $olympiad->questions()
            ->with('answers')
            ->inRandomOrder()
            ->limit($limit)
            ->get()
            ->map(function ($q) use ($olympiad) {
                $answers = $olympiad->shuffle_options
                    ? $q->answers->shuffle()
                    : $q->answers;

                return [
                    'id'         => $q->id,
                    'question'   => $q->question,
                    'difficulty' => $q->difficulty,
                    'answers'    => $answers->map(fn($a) => [
                        'id'     => $a->id,
                        'answer' => $a->answer
                    ])->values(),
                ];
            });

        $olympiad->questions_count = $limit;

        return response()->json([
            'olympiad'  => $olympiad,
            'questions' => $questions,
        ]);
    }

    public function start(Request $request, Olympiads $olympiad)
    {
        $this->assertActive($olympiad);

        $isRegistered = OlympiadRegistration::where('olympiad_id', $olympiad->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if (!$isRegistered) {
            return response()->json(['message' => 'Avval ro\'yhatdan o\'ting'], 403);
        }

        $attempt = OlympiadResult::where('olympiad_id', $olympiad->id)
                ->where('user_id', $request->user()->id)
                ->max('attempt') ?? 0;

        if ($olympiad->max_attempts && $attempt >= $olympiad->max_attempts) {
            return response()->json(['message' => 'Urinishlar tugadi'], 403);
        }

        $existing = OlympiadResult::where('olympiad_id', $olympiad->id)
            ->where('user_id', $request->user()->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existing) return response()->json($existing);

        $result = OlympiadResult::create([
            'olympiad_id'     => $olympiad->id,
            'user_id'         => $request->user()->id,
            'attempt'         => $attempt + 1,
            'total_questions' => $olympiad->questions_count,
            'status'          => 'in_progress',
        ]);

        return response()->json($result, 201);
    }

    public function submit(Request $request, Olympiads $olympiad)
    {
        $request->validate([
            'answers'    => 'required|array',
            'time_spent' => 'required|integer',
        ]);

        $result = OlympiadResult::where('olympiad_id', $olympiad->id)
            ->where('user_id', $request->user()->id)
            ->where('status', 'in_progress')
            ->firstOrFail();

        $correctIds = $olympiad->questions()
            ->with('answers')
            ->get()
            ->flatMap(fn($q) => $q->answers->where('is_correct', true)->pluck('id'))
            ->toArray();

        $answers = $request->answers;
        $correct = $wrong = $skipped = 0;
        $total   = $olympiad->questions_count;

        foreach ($olympiad->questions as $q) {
            $chosen = $answers[$q->id] ?? null;
            if (!$chosen)                           $skipped++;
            elseif (in_array($chosen, $correctIds)) $correct++;
            else                                    $wrong++;
        }

        $result->update([
            'answers'           => $answers,
            'correct_answers'   => $correct,
            'wrong_answers'     => $wrong,
            'skipped_questions' => $skipped,
            'total_questions'   => $total,
            'score_percent'     => $total > 0 ? round(($correct / $total) * 100, 2) : 0,
            'time_spent'        => $request->time_spent,
            'status'            => 'completed',
            'finished_at'       => Carbon::now(),
        ]);



        broadcast(new OlympiadLeaderboardUpdated($olympiad->id))->toOthers();

        return response()->json($result);
    }

    public function leaderboard(Olympiads $olympiad)
    {
        $rows = OlympiadResult::where('olympiad_id', $olympiad->id)
            ->where('status', 'completed')
            ->with('user:id,name')
            ->orderByDesc('score_percent')
            ->orderBy('time_spent')
            ->get()
            ->map(fn($r, $i) => [
                'rank'               => $i + 1,
                'user_id'            => $r->user_id,
                'name'               => $r->user->name,
                'score_percent'      => $r->score_percent,
                'correct_answers'    => $r->correct_answers,
                'wrong_answers'      => $r->wrong_answers,
                'skipped_questions'  => $r->skipped_questions,
                'time_spent'         => $r->time_spent,
                'finished_at'        => $r->finished_at,
            ]);

        return response()->json($rows);
    }

    public function myResult(Request $request, Olympiads $olympiad)
    {
        $result = OlympiadResult::where('olympiad_id', $olympiad->id)
            ->where('user_id', $request->user()->id)
            ->latest()
            ->first();

        return response()->json($result);
    }

    private function assertActive(Olympiads $olympiad): void
    {
        $now = Carbon::now();
        if ($olympiad->starts_at && $now->lt($olympiad->starts_at)) {
            abort(403, 'Olimpiada hali boshlanmagan');
        }
        if ($olympiad->ends_at && $now->gt($olympiad->ends_at)) {
            abort(403, 'Olimpiada tugagan');
        }
    }
}
