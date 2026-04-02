<?php

namespace App\Http\Resources;

use App\Models\OlympiadRegistration;
use App\Models\OlympiadResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OlympiadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'type'              => $this->type,
            'name_uz'           => $this->name_uz,
            'lang'              => $this->lang,
            'description_uz'    => $this->description_uz,
            'questions_count'   => $this->questions_count,
            'starts_at'         => $this->starts_at?->toDateTimeString(),
            'ends_at'           => $this->ends_at?->toDateTimeString(),
            'duration'          => $this->duration,
            'duration_label'    => $this->duration_label,
            'pass_score'        => $this->pass_score,
            'max_attempts'      => $this->max_attempts,
            'show_answers'      => $this->show_answers,
            'shuffle_questions' => $this->shuffle_questions,
            'shuffle_options'   => $this->shuffle_options,
            'is_active'         => $this->is_active,
            'status'            => $this->status,
            'status_label'      => $this->status_label,
            'time_until_start'  => $this->time_until_start,
            'registrations_count' => $this->whenCounted('registrations'),

            'participants_count' => OlympiadResult::where('olympiad_id', $this->id)
                ->distinct('user_id')
                ->count(),

            'is_registered'     => $request->user()
                ? OlympiadRegistration::where('olympiad_id', $this->id)
                    ->where('user_id', $request->user()->id)
                    ->exists()
                : false,

            'actual_questions_count' => $this->whenCounted('questions'),
            'questions'         => OlympiadQuestionResource::collection($this->whenLoaded('questions')),

            'created_at'        => $this->created_at?->toDateTimeString(),


        ];
    }
}
