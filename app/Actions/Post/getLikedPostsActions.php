<?php

namespace App\Actions\Post;

use App\Models\Like;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class getLikedPostsActions
{
    public function __invoke(LengthAwarePaginator $posts): LengthAwarePaginator
    {
        // DB::enableQueryLog();
        $likedPosts = Like::where('user_id', '=', auth()->user()->id)
            ->where('likeable_type', '=', 'App\Models\Post')
            ->get('likeable_id')
            ->pluck('likeable_id')
            ->toArray();
        // dump(DB::getQueryLog());
        $posts->getCollection()->transform(function ($post) use ($likedPosts) {
            if (in_array($post->id, $likedPosts)) {
                $post->is_liked = true;
            } else {
                $post->is_liked = false;
            }

            return $post;
        });

        return $posts;
    }
}
