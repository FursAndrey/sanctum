<?php
namespace App\Actions\Post;

use App\Models\Preview;

class createPostWithPreviewAction
{
    public function __invoke():Preview
    {
        return Preview::factory(1)->create()->first();
    }
}
