<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OlympiadResource;
use App\Models\Olympiads;
use App\Models\OlympiadAnswer;
use App\Models\OlympiadQuestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class OlympiadController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $olympiads = Olympiads::withCount('questions')
            ->selectSub(
                \DB::table('olympiad_registrations')->whereColumn('olympiad_id', 'olympiads.id')->selectRaw('count(*)'),
                'registrations_count'
            )
            ->when($request->search, fn($q) => $q->where('name_uz', 'like', "%{$request->search}%"))
            ->when($request->status, function ($q) use ($request) {
                $now = now();
                match ($request->status) {
                    'upcoming' => $q->where('starts_at', '>', $now),
                    'live'     => $q->where('starts_at', '<=', $now)->where('ends_at', '>=', $now),
                    'finished' => $q->where('ends_at', '<', $now),
                    default    => null,
                };
            })
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return OlympiadResource::collection($olympiads);
    }

    public function store(Request $request): OlympiadResource
    {
        $request->validate([
            'name_uz'           => 'required|string|max:255',
            'type'              => 'required|in:quiz,olympiad,exam',
            'lang'              => 'nullable|string|max:10',
            'description_uz'    => 'nullable|string',
            'starts_at'         => 'required|date',
            'ends_at'           => 'required|date|after:starts_at',
            'duration'          => 'required|integer|min:1',
            'pass_score'        => 'nullable|integer|min:0|max:100',
            'max_attempts'      => 'nullable|integer|min:1',
            'questions_count'   => 'nullable|integer|min:1',
            'show_answers' => 'nullable|in:never,after_submission,after_finish',
            'shuffle_questions' => 'boolean',
            'shuffle_options'   => 'boolean',
            'is_active'         => 'boolean',
            'questions'                         => 'nullable|array',
            'questions.*.question'              => 'required|string',
            'questions.*.type'                  => 'nullable|in:single,multiple,open',
            'questions.*.difficulty'            => 'nullable|in:easy,medium,hard',
            'questions.*.order'                 => 'nullable|integer',
            'questions.*.answers'               => 'nullable|array',
            'questions.*.answers.*.answer'      => 'required|string',
            'questions.*.answers.*.is_correct'  => 'boolean',
        ]);

        $olympiad = DB::transaction(function () use ($request) {
            $olympiad = Olympiads::create([
                'name_uz'           => $request->name_uz,
                'type'              => $request->type,
                'lang'              => $request->lang ?? 'uz',
                'description_uz'    => $request->description_uz,
                'starts_at'         => $request->starts_at,
                'ends_at'           => $request->ends_at,
                'duration'          => $request->duration,
                'pass_score'        => $request->pass_score ?? 60,
                'max_attempts'      => $request->max_attempts ?? 1,
                'questions_count'   => $request->questions_count,
                'show_answers'      => $request->show_answers ?? 'after_submission',
                'shuffle_questions' => $request->boolean('shuffle_questions', true),
                'shuffle_options'   => $request->boolean('shuffle_options', true),
                'is_active'         => $request->boolean('is_active', true),
            ]);

            $this->saveQuestions($olympiad, $request->questions ?? []);

            return $olympiad;
        });

        return new OlympiadResource($olympiad->load('questions.answers'));
    }

    public function show(Olympiads $olympiad): OlympiadResource
    {
        $olympiad->load(['questions.answers'])
            ->loadCount(['questions as real_questions_count']);

        $olympiad->registrations_count = DB::table('olympiad_registrations')
            ->where('olympiad_id', $olympiad->id)
            ->count();
        return new OlympiadResource($olympiad);
    }

    public function update(Request $request, Olympiads $olympiad): OlympiadResource
    {
        $request->validate([
            'name_uz'           => 'required|string|max:255',
            'type'              => 'required|in:quiz,olympiad,exam',
            'lang'              => 'nullable|string|max:10',
            'description_uz'    => 'nullable|string',
            'starts_at'         => 'required|date',
            'ends_at'           => 'required|date|after:starts_at',
            'duration'          => 'required|integer|min:1',
            'pass_score'        => 'nullable|integer|min:0|max:100',
            'max_attempts'      => 'nullable|integer|min:1',
            'questions_count'   => 'nullable|integer|min:1',
            'show_answers' => 'nullable|in:never,after_submission,after_finish',
            'shuffle_questions' => 'boolean',
            'shuffle_options'   => 'boolean',
            'is_active'         => 'boolean',
            'questions'                         => 'nullable|array',
            'questions.*.id'                    => 'nullable|integer',
            'questions.*.question'              => 'required|string',
            'questions.*.type'                  => 'nullable|in:single,multiple,open',
            'questions.*.difficulty'            => 'nullable|in:easy,medium,hard',
            'questions.*.order'                 => 'nullable|integer',
            'questions.*.answers'               => 'nullable|array',
            'questions.*.answers.*.id'          => 'nullable|integer',
            'questions.*.answers.*.answer'      => 'required|string',
            'questions.*.answers.*.is_correct'  => 'boolean',
            'deleted_question_ids'              => 'nullable|array',
            'deleted_question_ids.*'            => 'integer',
        ]);

        DB::transaction(function () use ($request, $olympiad) {
            $olympiad->update([
                'name_uz'           => $request->name_uz,
                'type'              => $request->type,
                'lang'              => $request->lang ?? $olympiad->lang,
                'description_uz'    => $request->description_uz,
                'starts_at'         => $request->starts_at,
                'ends_at'           => $request->ends_at,
                'duration'          => $request->duration,
                'pass_score'        => $request->pass_score ?? $olympiad->pass_score,
                'max_attempts'      => $request->max_attempts ?? $olympiad->max_attempts,
                'questions_count'   => $request->questions_count,
                'show_answers' => $request->show_answers ?? $olympiad->show_answers,
                'shuffle_questions' => $request->boolean('shuffle_questions', $olympiad->shuffle_questions),
                'shuffle_options'   => $request->boolean('shuffle_options', $olympiad->shuffle_options),
                'is_active'         => $request->boolean('is_active', $olympiad->is_active),
            ]);

            if ($request->filled('deleted_question_ids')) {
                $olympiad->questions()->whereIn('id', $request->deleted_question_ids)->each(function ($q) {
                    $q->answers()->delete();
                    $q->delete();
                });
            }

            $this->saveQuestions($olympiad, $request->questions ?? [], true);
        });

        return new OlympiadResource($olympiad->fresh()->load('questions.answers'));
    }

    public function destroy(Olympiads $olympiad): JsonResponse
    {
        DB::transaction(function () use ($olympiad) {
            foreach ($olympiad->questions as $q) {
                $q->answers()->delete();
                $q->delete();
            }
            $olympiad->registrations()->delete();
            $olympiad->delete();
        });

        return response()->json(['message' => 'Olympiad deleted successfully']);
    }

    public function importQuestions(Request $request, Olympiads $olympiad): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt,doc,docx|max:10240',
        ]);

        $file = $request->file('file');
        $ext  = strtolower($file->getClientOriginalExtension());

        $questions = match (true) {
            in_array($ext, ['xlsx', 'xls', 'csv']) => $this->parseSpreadsheet($file->getRealPath(), $ext),
            in_array($ext, ['txt'])                => $this->parseTxt($file->getRealPath()),
            in_array($ext, ['doc', 'docx'])        => $this->parseDocx($file->getRealPath()),
            default                                => [],
        };

        if (empty($questions)) {
            return response()->json(['message' => 'No questions found in file', 'count' => 0], 422);
        }

        $saved = 0;
        DB::transaction(function () use ($olympiad, $questions, &$saved) {
            $offset = $olympiad->questions()->count();
            foreach ($questions as $i => $qData) {
                $question = $olympiad->questions()->create([
                    'question'   => $qData['question'],
                    'type'       => $qData['type'] ?? 'single',
                    'difficulty' => $qData['difficulty'] ?? 'medium',
                    'order'      => $offset + $i,
                ]);
                foreach ($qData['answers'] ?? [] as $aData) {
                    $question->answers()->create([
                        'answer'     => $aData['answer'],
                        'is_correct' => $aData['is_correct'] ?? false,
                    ]);
                }
                $saved++;
            }
        });

        return response()->json(['message' => "{$saved} questions imported successfully", 'count' => $saved]);
    }

    private function saveQuestions(Olympiads $olympiad, array $questions, bool $update = false): void
    {
        foreach ($questions as $i => $qData) {
            if ($update && !empty($qData['id'])) {
                $question = $olympiad->questions()->find($qData['id']);
                if ($question) {
                    $question->update([
                        'question'   => $qData['question'],
                        'type'       => $qData['type'] ?? 'single',
                        'difficulty' => $qData['difficulty'] ?? 'medium',
                        'order'      => $qData['order'] ?? $i,
                    ]);
                    $existingIds = collect($qData['answers'] ?? [])->pluck('id')->filter()->toArray();
                    $question->answers()->whereNotIn('id', $existingIds)->delete();

                    foreach ($qData['answers'] ?? [] as $aData) {
                        if (!empty($aData['id'])) {
                            $question->answers()->where('id', $aData['id'])->update([
                                'answer'     => $aData['answer'],
                                'is_correct' => $aData['is_correct'] ?? false,
                            ]);
                        } else {
                            $question->answers()->create([
                                'answer'     => $aData['answer'],
                                'is_correct' => $aData['is_correct'] ?? false,
                            ]);
                        }
                    }
                    continue;
                }
            }

            $question = $olympiad->questions()->create([
                'question'   => $qData['question'],
                'type'       => $qData['type'] ?? 'single',
                'difficulty' => $qData['difficulty'] ?? 'medium',
                'order'      => $qData['order'] ?? $i,
            ]);

            foreach ($qData['answers'] ?? [] as $aData) {
                $question->answers()->create([
                    'answer'     => $aData['answer'],
                    'is_correct' => $aData['is_correct'] ?? false,
                ]);
            }
        }
    }

    private function parseSpreadsheet(string $path, string $ext): array
    {
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        $rows = $spreadsheet->getActiveSheet()->toArray();

        $questions = [];
        $current   = null;

        foreach ($rows as $row) {
            $col0 = trim((string) ($row[0] ?? ''));
            $col1 = trim((string) ($row[1] ?? ''));

            if (strtolower($col0) === 'q' && !empty($col1)) {
                if ($current) $questions[] = $current;
                $current = ['question' => $col1, 'type' => 'single', 'answers' => []];
                continue;
            }

            if (in_array(strtolower($col0), ['a', 'b', 'c', 'd', 'e']) && $current && !empty($col1)) {
                $col2 = trim((string) ($row[2] ?? ''));
                $current['answers'][] = [
                    'answer'     => $col1,
                    'is_correct' => strtolower($col2) === 'true' || $col2 === '1',
                ];
            }
        }

        if ($current) $questions[] = $current;

        return $questions;
    }

    private function parseTxt(string $path): array
    {
        $lines     = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $questions = [];
        $current   = null;

        foreach ($lines as $line) {
            $line = trim($line);

            if (preg_match('/^\d+[.)]\s+(.+)/', $line, $m)) {
                if ($current) $questions[] = $current;
                $current = ['question' => trim($m[1]), 'type' => 'single', 'answers' => []];
                continue;
            }

            if (preg_match('/^([A-Ea-e])[.)]\s+(.+)/', $line, $m) && $current) {
                $current['answers'][] = ['answer' => trim($m[2]), 'is_correct' => false];
                continue;
            }

            if (preg_match('/^[Тт]o\'g\'ri|^[Аа]nswer[:\s]+([A-Ea-e])/u', $line, $m) && $current) {
                $correct = strtoupper(trim($m[1] ?? ''));
                $idx     = ord($correct) - ord('A');
                if (isset($current['answers'][$idx])) {
                    $current['answers'][$idx]['is_correct'] = true;
                }
            }
        }

        if ($current) $questions[] = $current;

        return $questions;
    }

    private function parseDocx(string $path): array
    {
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) return [];

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if (!$xml) return [];

        $text  = strip_tags($xml);
        $lines = array_filter(array_map('trim', explode("\n", $text)));

        return $this->parseTxt('data://text/plain,' . urlencode(implode("\n", $lines)));
    }
}
