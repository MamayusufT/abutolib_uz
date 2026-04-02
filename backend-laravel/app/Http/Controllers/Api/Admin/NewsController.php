<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $news = News::with('author')
            ->when($request->search, fn($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->category, fn($q) => $q->where('category', $request->category))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return NewsResource::collection($news);
    }

    public function store(Request $request): NewsResource
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'excerpt'      => 'nullable|string|max:500',
            'body'         => 'required|string',
            'status'       => 'required|in:draft,published,archived',
            'category'     => 'nullable|string|max:100',
            'image'        => 'nullable|image|max:4096',
            'published_at' => 'nullable|date',
        ]);

        $data = $request->only(['title', 'excerpt', 'body', 'status', 'category', 'published_at']);
        $data['user_id'] = $request->user()->id;
        $data['views'] = 0;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $news = News::create($data);

        return new NewsResource($news->load('author'));
    }

    public function show(News $news): NewsResource
    {
        return new NewsResource($news->load('author'));
    }

    public function update(Request $request, News $news): NewsResource
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'excerpt'      => 'nullable|string|max:500',
            'body'         => 'required|string',
            'status'       => 'required|in:draft,published,archived',
            'category'     => 'nullable|string|max:100',
            'image'        => 'nullable|image|max:4096',
            'published_at' => 'nullable|date',
        ]);

        $data = $request->only(['title', 'excerpt', 'body', 'status', 'category', 'published_at']);

        if ($request->hasFile('image')) {
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        if ($data['status'] === 'published' && empty($data['published_at']) && !$news->published_at) {
            $data['published_at'] = now();
        }

        $news->update($data);

        return new NewsResource($news->load('author'));
    }

    public function destroy(News $news): JsonResponse
    {
        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }

        $news->delete();

        return response()->json(['message' => 'News deleted successfully']);
    }
}
