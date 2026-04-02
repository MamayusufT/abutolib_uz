<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = auth()->user()->wishlists()->with('book')->latest()->get();
        return BookResource::collection($wishlists->pluck('book'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $wishlist = Wishlist::firstOrCreate([
            'user_id' => auth()->id(),
            'book_id' => $validated['book_id'],
        ]);

        return response()->json([
            'message' => 'Book added to wishlist',
            'book' => new BookResource(Book::find($validated['book_id'])),
        ], Response::HTTP_CREATED);
    }

    public function destroy(Book $book)
    {
        $wishlist = Wishlist::where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->firstOrFail();

        $wishlist->delete();

        return response()->json(['message' => 'Book removed from wishlist'], Response::HTTP_NO_CONTENT);
    }

    public function check(Book $book)
    {
        $exists = Wishlist::where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->exists();

        return response()->json(['in_wishlist' => $exists]);
    }

    public function count()
    {
        $count = auth()->user()->wishlists()->count();
        return response()->json(['count' => $count]);
    }
}
