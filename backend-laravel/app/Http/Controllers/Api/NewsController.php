<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Http\Resources\NewsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::with('author')
            ->published()
            ->orderByDesc('published_at');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('excerpt', 'like', '%' . $request->search . '%');
            });
        }

        $news = $query->paginate($request->get('per_page', 9));

        return NewsResource::collection($news);
    }

    public function show($slug)
    {
        $news = News::with('author')
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $news->increment('views');

        return new NewsResource($news);
    }

    public function categories(): \Illuminate\Http\JsonResponse
    {
        $categories = News::select('category', DB::raw('count(*) as total'))
            ->whereNotNull('category')
            ->where('status', 'published')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return response()->json($categories);
    }
}
