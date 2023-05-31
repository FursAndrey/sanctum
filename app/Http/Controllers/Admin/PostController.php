<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Post\createPostWithPreviewAction;
use App\Actions\Preview\cutImageIdAction;
use App\Actions\Preview\destroyAllUnjoinedPreviews;
use App\Actions\Preview\destroyOnePreview;
use App\Actions\Preview\joinPostPreview;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StoreRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Exception;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::latest()->get();
        
        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $this->authorize('create', Post::class);

        // Log::info('This is some useful information.');
        try {
            DB::beginTransaction();
            
            $data = $request->validated();
            $imageId = (new cutImageIdAction)($data);
            $post = Post::create($data);
            (new joinPostPreview)($post->id, $imageId);
            (new destroyAllUnjoinedPreviews)();

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['error' => $exception->getMessage()]);
        }
        
        return new PostResource($post);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        try {
            DB::beginTransaction();

            $data = $request->validated();
            if (isset($post->preview) && $post->preview->id != $data['image_id']) {
                (new destroyOnePreview)($post->preview);
            }

            $imageId = (new cutImageIdAction)($data);
            $post->fill($data)->save();
            (new joinPostPreview)($post->id, $imageId);
            (new destroyAllUnjoinedPreviews)();
            
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['error' => $exception->getMessage()]);
        }
        return new PostResource($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        
        if (isset($post->preview)) {
            (new destroyOnePreview)($post->preview);
        }
        $post->delete();

        return response()->noContent();
    }

    public function storeRandomPost()
    {
        (new createPostWithPreviewAction)();
        
        return response(201);
    }
}
