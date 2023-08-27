<?php

namespace App\Http\Controllers;

use App\Actions\Comment\createRandomCommentAction;
use App\Http\Requests\Comment\StoreRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(int $post)
    {
        $comments = Comment::where('post_id', '=', $post)->orderBy('id', 'desc')->get();
        return CommentResource::collection($comments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $this->authorize('create', Comment::class);

        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $comment = Comment::create($data);

        return new CommentResource($comment);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        //
    }

    public function storeRandomComment()
    {
        $this->authorize('createRandom', Comment::class);
        $randomComment = (new createRandomCommentAction())();

        return new CommentResource($randomComment);
    }
}
