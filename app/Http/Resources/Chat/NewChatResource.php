<?php

namespace App\Http\Resources\Chat;

use App\Http\Resources\Message\MessageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewChatResource extends JsonResource
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
            'title' => isset($this->title) ? $this->title : 'With '.$this->chatWith->name,
            'users' => $this->users,
            'last_message' => MessageResource::make($this->lastMessage)->resolve(),
            'unreadable_messages_count' => 1,
        ];
    }
}
