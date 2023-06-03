<?php
namespace App\Actions\Post;

use App\Models\Preview;

class createPostWithPreviewAction
{
    public function __invoke():void
    {
        Preview::factory(1)->create();
    }
}
