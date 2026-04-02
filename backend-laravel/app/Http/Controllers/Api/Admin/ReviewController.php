<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReviewController extends Controller
{

    public function index(Book $book)
    {
        $reviews = $book->reviews()
            ->with('user')
            ->latest()
            ->paginate(10);

        return ReviewResource::collection($reviews);
    }

    public function store(Request $request, Book $book)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $existingReview = Review::where('book_id', $book->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existingReview) {
            $existingReview->update($validated);
            return new ReviewResource($existingReview);
        }

        $review = Review::create([
            'book_id' => $book->id,
            'user_id' => auth()->id(),
            ...$validated,
        ]);

        return new ReviewResource($review->load('user'));
    }

    public function update(Request $request, Review $review)
    {
        $this->authorize('update', $review);

        $validated = $request->validate([
            'rating' => 'integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update($validated);

        return new ReviewResource($review);
    }

    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);

        $review->delete();

        return response()->json(['message' => 'Review deleted successfully'], Response::HTTP_NO_CONTENT);
    }

    public function markHelpful(Review $review)
    {
        $review->incrementHelpfulCount();

        return new ReviewResource($review);
    }

    public function userReview(Book $book)
    {
        $review = Review::where('book_id', $book->id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$review) {
            return response()->json(['message' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }

        return new ReviewResource($review);
    }
}
