<?php

namespace App\Actions\Post;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class getLikedOnePostActions
{
    public function __invoke(Post $post): Post
    {
        // DB::enableQueryLog();
        if (is_null(auth()->user())) {
            $likedPost = null;
        } else {
            $likedPost = Like::where('user_id', '=', auth()->user()->id)
                ->where('likeable_type', '=', 'App\Models\Post')
                ->where('likeable_id', '=', $post->id)
                ->first('likeable_id')
                ?->likeable_id;
        }

        // dump(DB::getQueryLog());
        $post->is_liked = $post->id == $likedPost;

        return $post;
    }
}
