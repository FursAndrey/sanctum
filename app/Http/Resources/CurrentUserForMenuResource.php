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
                'name' => NULL,
            ];
        } else {
            return [
                'name' => auth()->user()->name,
                'roles' => auth()->user()->roles->pluck('title')->toArray(),
            ];
        }
    }
}
