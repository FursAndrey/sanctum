<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'title' => $this->title,
            'body' => $this->body,
            'published' => $this->published,
            'preview' => new MediaResource($this->getMedia('preview')->first()),
            'commentCount' => $this->comments->count(),
            'likeCount' => $this->likes->count(),
            'is_liked' => $this->is_liked,
            // 'preview' => new PreviewResource($this->whenLoaded('preview')),
        ];
    }
}
