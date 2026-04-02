<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'subject_id'      => $this->subject_id,
            'name'            => $this->name,
            'description'     => $this->description,
            'order'           => $this->order,
            'time_limit'      => $this->time_limit,
            'is_active'       => $this->is_active,
            'questions_count' => $this->whenCounted('questions'),
            'subject'         => new SubjectResource($this->whenLoaded('subject')),
            'questions'       => QuestionResource::collection($this->whenLoaded('questions')),
            'created_at'      => $this->created_at?->toDateTimeString(),
        ];
    }
}
