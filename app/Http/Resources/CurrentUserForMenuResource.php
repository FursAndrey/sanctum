<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrentUserForMenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (is_null(auth()->user())) {
            return [
                'name' => null,
            ];
        } else {
            return [
                'id' => (string) auth()->user()->id,
                'name' => auth()->user()->name,
                'roles' => auth()->user()->roles->pluck('title')->toArray(),
                'has_ban_chat' => ($this->banChat?->created_at) ? true : false,
                'has_ban_comment' => ($this->banComment?->created_at) ? true : false,
            ];
        }
    }
}
