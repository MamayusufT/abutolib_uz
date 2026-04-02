<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'slug'            => $this->slug,
            'description'     => $this->description,
            'image'           => $this->image,
            'color'           => $this->color,
            'order'           => $this->order,
            'is_active'       => $this->is_active,
            'topics_count'    => $this->whenCounted('topics'),
            'topics'          => TopicResource::collection($this->whenLoaded('topics')),
            'files'           => SubjectFileResource::collection($this->whenLoaded('files')),
            'created_at'      => $this->created_at?->toDateTimeString(),
            'updated_at'      => $this->updated_at?->toDateTimeString(),
        ];
    }
}
