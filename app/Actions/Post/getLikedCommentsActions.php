<?php

namespace App\Actions\Post;

use App\Models\Like;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class getLikedCommentsActions
{
    public function __invoke(Collection $comments): Collection
    {
        // DB::enableQueryLog();
        if (is_null(auth()->user())) {
            $likedComments = [];
        } else {
            $likedComments = Like::where('user_id', '=', auth()->user()->id)
                ->where('likeable_type', '=', 'App\Models\Comment')
                ->get('likeable_id')
                ->pluck('likeable_id')
                ->toArray();
        }
        // dump(DB::getQueryLog());
        $comments->transform(function ($comment) use ($likedComments) {
            if (in_array($comment->id, $likedComments)) {
                $comment->is_liked = true;
            } else {
                $comment->is_liked = false;
            }

            return $comment;
        });

        return $comments;
    }
}
