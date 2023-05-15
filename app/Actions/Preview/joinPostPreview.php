<?php
namespace App\Actions\Preview;

use App\Models\Preview;

class joinPostPreview
{
    public function __invoke(int $postId, ?int $imageId):void
    {
        if (isset($imageId)) {
            $preview = Preview::find($imageId);
            $preview->update([
                'post_id' =>$postId,
            ]);
        }
    }
}