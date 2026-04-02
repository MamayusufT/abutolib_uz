<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'role'       => $this->role,
            'avatar'     => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'email'      => $this->email,
            'telegram'   => $this->telegram,
            'instagram'  => $this->instagram,
            'bio'        => $this->bio,
            'order'      => $this->order,
            'is_active'  => $this->is_active,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
