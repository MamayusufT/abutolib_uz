<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubjectResource;
use App\Models\Subject;
use App\Models\SubjectFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubjectController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $subjects = Subject::withCount('topics')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->has('is_active'), fn($q) => $q->where('is_active', $request->boolean('is_active')))
            ->orderBy('order')
            ->paginate($request->per_page ?? 15);

        return SubjectResource::collection($subjects);
    }

    public function store(Request $request): SubjectResource
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'color'             => 'nullable|string|max:20',
            'order'             => 'nullable|integer|min:0',
            'is_active'         => 'boolean',
            'image'             => 'nullable|image|max:2048',
            'topics'            => 'nullable|array',
            'topics.*.name'     => 'required|string|max:255',
            'topics.*.description' => 'nullable|string',
            'topics.*.order'    => 'nullable|integer',
            'topics.*.time_limit' => 'nullable|integer',
            'topics.*.is_active' => 'boolean',
            'files'             => 'nullable|array',
            'files.*'           => 'file|max:10240',
        ]);

        $data = $request->only(['name', 'description', 'color', 'order', 'is_active']);
        $data['slug'] = Str::slug($request->name);
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('subjects', 'public');
        }

        $subject = Subject::create($data);

        if ($request->filled('topics')) {
            foreach ($request->topics as $index => $topicData) {
                $subject->topics()->create([
                    'name'        => $topicData['name'],
                    'description' => $topicData['description'] ?? null,
                    'order'       => $topicData['order'] ?? $index,
                    'time_limit'  => $topicData['time_limit'] ?? null,
                    'is_active'   => $topicData['is_active'] ?? true,
                ]);
            }
        }

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('subject-files/' . $subject->id, 'public');
                $subject->files()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return new SubjectResource(
            $subject->load(['topics.questions.matchPairs', 'files'])
        );
    }

    public function show(Subject $subject): SubjectResource
    {
        return new SubjectResource(
            $subject->load(['topics.questions.matchPairs', 'files'])
        );
    }

    public function update(Request $request, Subject $subject): SubjectResource
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'color'             => 'nullable|string|max:20',
            'order'             => 'nullable|integer|min:0',
            'is_active'         => 'boolean',
            'image'             => 'nullable|image|max:2048',
            'topics'            => 'nullable|array',
            'topics.*.id'       => 'nullable|integer|exists:topics,id',
            'topics.*.name'     => 'required|string|max:255',
            'topics.*.description' => 'nullable|string',
            'topics.*.order'    => 'nullable|integer',
            'topics.*.time_limit' => 'nullable|integer',
            'topics.*.is_active' => 'boolean',
            'deleted_topic_ids' => 'nullable|array',
            'deleted_topic_ids.*' => 'integer|exists:topics,id',
            'deleted_file_ids'  => 'nullable|array',
            'deleted_file_ids.*' => 'integer|exists:subject_files,id',
            'files'             => 'nullable|array',
            'files.*'           => 'file|max:10240',
        ]);

        $data = $request->only(['name', 'description', 'color', 'order', 'is_active']);
        $data['is_active'] = $request->boolean('is_active', $subject->is_active);

        if ($request->hasFile('image')) {
            if ($subject->image) {
                Storage::disk('public')->delete($subject->image);
            }
            $data['image'] = $request->file('image')->store('subjects', 'public');
        }

        $subject->update($data);

        if ($request->filled('deleted_topic_ids')) {
            $subject->topics()->whereIn('id', $request->deleted_topic_ids)->delete();
        }

        if ($request->filled('topics')) {
            foreach ($request->topics as $index => $topicData) {
                if (!empty($topicData['id'])) {
                    $subject->topics()->where('id', $topicData['id'])->update([
                        'name'        => $topicData['name'],
                        'description' => $topicData['description'] ?? null,
                        'order'       => $topicData['order'] ?? $index,
                        'time_limit'  => $topicData['time_limit'] ?? null,
                        'is_active'   => $topicData['is_active'] ?? true,
                    ]);
                } else {
                    $subject->topics()->create([
                        'name'        => $topicData['name'],
                        'description' => $topicData['description'] ?? null,
                        'order'       => $topicData['order'] ?? $index,
                        'time_limit'  => $topicData['time_limit'] ?? null,
                        'is_active'   => $topicData['is_active'] ?? true,
                    ]);
                }
            }
        }

        if ($request->filled('deleted_file_ids')) {
            $files = SubjectFile::whereIn('id', $request->deleted_file_ids)
                ->where('subject_id', $subject->id)
                ->get();

            foreach ($files as $file) {
                Storage::disk('public')->delete($file->file_path);
                $file->delete();
            }
        }

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('subject-files/' . $subject->id, 'public');
                $subject->files()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return new SubjectResource(
            $subject->load(['topics.questions.matchPairs', 'files'])
        );
    }

    public function destroy(Subject $subject): JsonResponse
    {
        if ($subject->image) {
            Storage::disk('public')->delete($subject->image);
        }

        foreach ($subject->files as $file) {
            Storage::disk('public')->delete($file->file_path);
        }

        $subject->delete();

        return response()->json(['message' => 'Subject deleted successfully']);
    }
}
