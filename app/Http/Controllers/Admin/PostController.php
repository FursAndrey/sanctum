<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Post\createPostWithPreviewAction;
use App\Actions\Post\getLikedOnePostActions;
use App\Actions\Post\getLikedPostsActions;
use App\Actions\Preview\cutImageIdAction;
use App\Actions\Preview\destroyAllUnjoinedPreviewsAction;
use App\Actions\Preview\destroyOnePreviewAction;
use App\Actions\Preview\joinPostPreviewAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Media\StoreRequest as MediaStoreRequest;
use App\Http\Requests\Media\UpdateRequest as MediaUpdateRequest;
use App\Http\Requests\Post\StoreRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::orderBy('id', 'desc')->with(['comments','likes'])->paginate(10);
        $posts = (new getLikedPostsActions)($posts);

        return new PostCollection($posts);
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
            $imageId = (new cutImageIdAction())($data);
            $post = Post::create($data);
            (new joinPostPreviewAction())($post->id, $imageId);
            (new destroyAllUnjoinedPreviewsAction())();

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();

            return response()->json(['error' => $exception->getMessage()]);
        }

        return new PostResource($post);
    }

    public function store2(MediaStoreRequest $request)
    {
        $this->authorize('create', Post::class);

        try {
            DB::beginTransaction();

            $storedPost = $request->validated();
            if (isset($storedPost['imgs'])) {
                $storedImgs = $storedPost['imgs'];
                unset($storedPost['imgs']);
            }

            $post = Post::create($storedPost);

            if (isset($storedImgs)) {
                foreach ($storedImgs as $img) {
                    $post->addMedia($img)->toMediaCollection('preview');
                }
            }

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
        $post = (new getLikedOnePostActions)($post);

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
                (new destroyOnePreviewAction())($post->preview);
            }

            $imageId = (new cutImageIdAction())($data);
            $post->fill($data)->save();
            (new joinPostPreviewAction())($post->id, $imageId);
            (new destroyAllUnjoinedPreviewsAction())();

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();

            return response()->json(['error' => $exception->getMessage()]);
        }

        return new PostResource($post);
    }

    public function update2(MediaUpdateRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        try {
            DB::beginTransaction();

            $updatedPost = $request->validated();

            //удалить выбранные для этого картинки
            if (isset($updatedPost['deleted_preview'])) {
                $deleted_preview = $updatedPost['deleted_preview'];
                unset($updatedPost['deleted_preview']);

                $previews = $post->getMedia('preview');
                foreach ($previews as $img) {
                    if (in_array($img->id, $deleted_preview)) {
                        $img->delete();
                    }
                }
            }
            //сохранить новые
            if (isset($updatedPost['imgs'])) {
                $storedImgs = $updatedPost['imgs'];
                unset($updatedPost['imgs']);

                foreach ($storedImgs as $img) {
                    $post->addMedia($img)->toMediaCollection('preview');
                }
            }

            $post->fill($updatedPost)->save();

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
            (new destroyOnePreviewAction())($post->preview);
        }
        $post->delete();

        return response()->noContent();
    }

    public function storeRandomPost()
    {
        $this->authorize('create', Post::class);
        $randomPost = (new createPostWithPreviewAction())();

        return new PostResource($randomPost);
        // return new PostResource($randomPreview->post);
    }
}
