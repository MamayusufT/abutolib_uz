<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'email'              => $this->email,
            'phone'              => $this->phone,
            'telegram_id'        => $this->telegram_id,
            'telegram_username'  => $this->telegram_username,
            'is_active'          => $this->is_active,
            'last_seen'          => $this->last_seen?->toDateTimeString(),
            'email_verified_at'  => $this->email_verified_at?->toDateTimeString(),
            'roles'              => $this->roles,
            'created_at'         => $this->created_at?->toDateTimeString(),
        ];
    }
}
