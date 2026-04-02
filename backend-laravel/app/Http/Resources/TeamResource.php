<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TeamResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'role'       => $this->role,
            'email'      => $this->email,
            'telegram'   => $this->telegram,
            'instagram'  => $this->instagram,
            'bio'        => $this->bio,
            'order'      => $this->order,
            'is_active'  => $this->is_active,

            'avatar'     => $this->avatar
                ? asset('storage/' . $this->avatar)
                : asset('images/default-avatar.png'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
