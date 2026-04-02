<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Builder;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('language')) {
            $query->where('language', $request->input('language'));
        }

        if ($request->has('sort')) {
            $sort = $request->input('sort');
            if ($sort === 'rating') {
                $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating');
            } elseif ($sort === 'newest') {
                $query->latest();
            } elseif ($sort === 'popular') {
                $query->orderByDesc('download_count');
            }
        }

        $books = $query->paginate($request->input('per_page', 15));

        return BookResource::collection($books);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books',
            'price' => 'required|numeric|min:0',
            'pages' => 'required|integer|min:1',
            'language' => 'required|in:uz,ru,en',
            'status' => 'required|in:available,unavailable,coming_soon',
            'cover_image' => 'nullable|image|max:2048',
            'pdf_file' => 'nullable|mimes:pdf|max:102400',
        ]);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        if ($request->hasFile('pdf_file')) {
            $validated['pdf_file'] = $request->file('pdf_file')->store('books/pdfs', 'public');
        }

        $book = Book::create($validated);

        return new BookResource($book);
    }

    public function show(Book $book)
    {
        $book->incrementViewCount();
        return new BookResource($book);
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'string',
            'author' => 'string|max:255',
            'isbn' => "string|unique:books,isbn,{$book->id}",
            'price' => 'numeric|min:0',
            'pages' => 'integer|min:1',
            'language' => 'in:uz,ru,en',
            'status' => 'in:available,unavailable,coming_soon',
            'cover_image' => 'nullable|image|max:2048',
            'pdf_file' => 'nullable|mimes:pdf|max:102400',
        ]);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        if ($request->hasFile('pdf_file')) {
            $validated['pdf_file'] = $request->file('pdf_file')->store('books/pdfs', 'public');
        }

        $book->update($validated);

        return new BookResource($book);
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json(['message' => 'Book deleted successfully'], Response::HTTP_NO_CONTENT);
    }

    public function download(Book $book)
    {
        if (!$book->pdf_file) {
            return response()->json(['message' => 'PDF file not found'], Response::HTTP_NOT_FOUND);
        }

        $book->incrementDownloadCount();

        return response()->download(
            storage_path('app/public/' . $book->pdf_file),
            $book->title . '.pdf'
        );
    }

    public function trending(Request $request)
    {
        $books = Book::where('status', 'available')
            ->orderByDesc('download_count')
            ->limit($request->input('limit', 10))
            ->get();

        return BookResource::collection($books);
    }

    public function topRated(Request $request)
    {
        $books = Book::where('status', 'available')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->limit($request->input('limit', 10))
            ->get();

        return BookResource::collection($books);
    }

    public static function middleware()
    {
        // TODO: Implement middleware() method.
    }
}
