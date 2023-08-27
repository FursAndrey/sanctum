<?php

namespace App\Actions\Post;

use App\Actions\Media\createMediaImgAction;
use App\Models\Post;
use App\Models\Preview;

class createPostWithPreviewAction
{
    public function __invoke(): Post
    {
        $posts = Post::factory(1)->create()->each(function ($post) {
            (new createMediaImgAction())($post);
        });

        return $posts->first();
    }

    // public function __invoke():Preview
    // {
    //     return Preview::factory(1)->create()->first();
    // }
}
