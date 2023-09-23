<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'published' => $this->published,
            'user' => $this->user->name,
            'answerCount' => $this->answers->count(),
            'likeCount' => $this->likes->count(),
            'is_liked' => $this->is_liked,
        ];
    }
}
