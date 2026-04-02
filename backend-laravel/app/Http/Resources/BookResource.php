<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'price' => (float)$this->price,
            'pages' => $this->pages,
            'language' => $this->language,
            'status' => $this->status,
            'cover_image' => $this->cover_image ? asset('storage/' . $this->cover_image) : null,
            'pdf_file' => $this->pdf_file ? asset('storage/' . $this->pdf_file) : null,
            'download_count' => $this->download_count,
            'view_count' => $this->view_count,
            'rating' => (float)$this->average_rating,
            'review_count' => $this->review_count,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
