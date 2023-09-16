<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;

class LikeController extends Controller
{
    public function postToggleLike(Post $post)
    {
        $like = Like::where('likeable_type', '=', Post::class)
            ->where('likeable_id', '=', $post->id)
            ->where('user_id', '=', auth()->user()->id)
            ->first();

        if (is_null($like)) {
            $post->likes()->create([
                'user_id' => auth()->user()->id,
            ]);

            $result['is_liked'] = true;
        } else {
            $like->delete();

            $result['is_liked'] = false;
        }

        $result['likes_count'] = $post->likes->count();

        return response()->json($result);
    }
}
