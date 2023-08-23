<?php
namespace App\Actions\Comment;

use App\Models\Comment;

class createRandomCommentAction
{
    public function __invoke(): Comment
    {
        $comments = Comment::factory(1)->create();
        return $comments->first();
    }
}