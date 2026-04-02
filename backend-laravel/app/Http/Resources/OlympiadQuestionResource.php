<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OlympiadQuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'question'   => $this->question,
            'type'       => $this->type ?? 'single',
            'difficulty' => $this->difficulty,
            'order'      => $this->order,
            'answers'    => OlympiadAnswerResource::collection($this->whenLoaded('answers')),
        ];
    }
}
