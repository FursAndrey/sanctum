<?php

namespace App\Http\Resources\User;

use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'tg_name' => $this->tg_name,
            'email' => $this->email,
            'created' => $this->created,
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'ban_chat' => $this->banChat?->created_at->format('d.m.Y H:i:s'),
            'ban_comment' => $this->banComment?->created_at->format('d.m.Y H:i:s'),
        ];
    }
}
