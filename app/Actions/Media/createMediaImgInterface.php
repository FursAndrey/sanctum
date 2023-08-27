<?php

namespace App\Actions\Media;

use App\Models\Post;

interface createMediaImgInterface
{
    public function __invoke(Post $post, string $dir): void;
}
