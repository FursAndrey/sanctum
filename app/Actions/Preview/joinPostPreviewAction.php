<?php

namespace App\Actions\Preview;

use App\Models\Preview;

class joinPostPreviewAction
{
    public function __invoke(int $postId, ?int $imageId): void
    {
        if (isset($imageId)) {
            $preview = Preview::find($imageId);
            if (isset($preview)) {
                $preview->update([
                    'post_id' => $postId,
                ]);
            }
        }
    }
}
