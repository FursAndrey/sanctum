<?php

namespace App\Http\Controllers;

use App\Actions\Comment\createRandomCommentAction;
use App\Actions\Post\getLikedCommentsActions;
use App\Http\Requests\Comment\StoreRequest;
use App\Http\Requests\Comment\UpdateRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(int $post, int $comment)
    {
        $commentsQuery = Comment::where('post_id', '=', $post);
        if ($comment == 0) {
            $commentsQuery->whereNull('parent_id');
        } else {
            $commentsQuery->where('parent_id', '=', $comment);
        }
        $comments = $commentsQuery->orderBy('id', 'desc')->with(['user', 'likes', 'answers'])->get();
        $comments = (new getLikedCommentsActions)($comments);

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
    public function update(UpdateRequest $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $data = $request->validated();
        $comment->update($data);

        return new CommentResource($comment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        if ($comment->answers->count() == 0) {
            $comment->delete();

            return response()->noContent();
        } else {
            return response()->json(['status' => false, 'message' => 'Delete imposible. You have answers.']);
        }

    }

    public function storeRandomComment()
    {
        $this->authorize('createRandom', Comment::class);
        $randomComment = (new createRandomCommentAction())();

        return new CommentResource($randomComment);
    }
}
