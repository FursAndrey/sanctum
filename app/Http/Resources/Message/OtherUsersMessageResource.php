<?php

namespace App\Http\Resources\Message;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OtherUsersMessageResource extends JsonResource
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
            'body' => $this->body,
            'chat_id' => $this->chat_id,
            'user_name' => $this->user->name,
            'time' => $this->created_at->format('d.m.Y H:i:s'),
            'is_owner' => false,
        ];
    }
}
