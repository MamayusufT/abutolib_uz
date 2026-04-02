<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'topic_id'   => $this->topic_id,
            'question'   => $this->question,
            'difficulty' => $this->difficulty,
            'type'       => $this->type,
            'order'      => $this->order,

            'answers' => $this->when(
                in_array($this->type, ['single', 'multiple']),
                fn() => AnswerResource::collection($this->whenLoaded('answers'))
            ),

            'match_pairs' => $this->when(
                $this->type === 'match',
                fn() => $this->matchPairs->map(fn($p) => [
                    'id'    => $p->id,
                    'left'  => $p->left,
                    'right' => $p->right,
                    'order' => $p->order,
                ])
            ),
        ];
    }
}
