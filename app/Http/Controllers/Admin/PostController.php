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
use App\Http\Resources\Post\PostCollection;
use App\Http\Resources\Post\PostResource;
use App\Models\Post;
use Exception;
use Illuminate\Support\Facades\DB;

// use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    /**
     *  @OA\Get(
     *      path="/api/posts",
     *      summary="Страница постов",
     *      tags={"posts"},
     *
     *      @OA\Parameter(
     *         description="Page number",
     *         in="query",
     *         name="page",
     *         required=false,
     *         example=1,
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="posts", type="array",
     *
     *                      @OA\Items(
     *
     *                          @OA\Property(property="body", type="string", example="Body of this post"),
     *                          @OA\Property(property="commentCount", type="integer", example=10),
     *                          @OA\Property(property="id", type="integer", example=1),
     *                          @OA\Property(property="is_liked", type="boolean", example=false),
     *                          @OA\Property(property="likeCount", type="integer", example=3),
     *                          @OA\Property(property="published", type="string", example="1 month ago"),
     *                          @OA\Property(property="title", type="string", example="Title of this post"),
     *                          @OA\Property(property="preview", type="object",
     *                              @OA\Property(property="id", type="integer", example=3),
     *                              @OA\Property(property="url_original", type="string", example="http://sanctum/storage/media/45/9TFZiOkNjfiGYdTyunVIxqZAGrAu7yI4MxGcqSyv.jpg"),
     *                              @OA\Property(property="url_preview", type="string", example="http://sanctum/storage/media/45/conversions/9TFZiOkNjfiGYdTyunVIxqZAGrAu7yI4MxGcqSyv-preview.jpg"),
     *                          ),
     *                      ),
     *                  ),
     *              ),
     *          ),
     *      ),
     *  )
     */
    public function index()
    {
        $posts = Post::orderBy('id', 'desc')->with(['comments', 'likes'])->paginate(10);
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

    /**
     *  @OA\Post(
     *      path="/api/posts2",
     *      summary="Создание",
     *      tags={"posts"},
     *      security={{ "sanctum": "" }},
     *
     *      @OA\RequestBody(
     *
     *          @OA\JsonContent(
     *              allOf={
     *
     *                  @OA\Schema(
     *
     *                      @OA\Property(property="body", type="string", example="Body of this post"),
     *                      @OA\Property(property="title", type="string", example="Title of this post"),
     *                  ),
     *              }
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=201,
     *          description="OK",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="body", type="string", example="Body of this post"),
     *                  @OA\Property(property="commentCount", type="integer", example=10),
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="is_liked", type="boolean", example=false),
     *                  @OA\Property(property="likeCount", type="integer", example=3),
     *                  @OA\Property(property="published", type="string", example="1 month ago"),
     *                  @OA\Property(property="title", type="string", example="Title of this post"),
     *                  @OA\Property(property="preview", type="object",
     *                      @OA\Property(property="id", type="integer", example=3),
     *                      @OA\Property(property="url_original", type="string", example="http://sanctum/storage/media/45/9TFZiOkNjfiGYdTyunVIxqZAGrAu7yI4MxGcqSyv.jpg"),
     *                      @OA\Property(property="url_preview", type="string", example="http://sanctum/storage/media/45/conversions/9TFZiOkNjfiGYdTyunVIxqZAGrAu7yI4MxGcqSyv-preview.jpg"),
     *                  ),
     *              ),
     *          ),
     *      ),
     *
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=403, description="Unauthorized"),
     *      @OA\Response(response=422, description="Invalid params"),
     *  )
     */
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
     *  @OA\Get(
     *      path="/api/posts/{post}",
     *      summary="Страница поста",
     *      tags={"posts"},
     *
     *      @OA\Parameter(
     *         description="Post ID",
     *         in="path",
     *         name="post",
     *         required=true,
     *         example=53,
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="body", type="string", example="Body of this post"),
     *                  @OA\Property(property="commentCount", type="integer", example=10),
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="is_liked", type="boolean", example=false),
     *                  @OA\Property(property="likeCount", type="integer", example=3),
     *                  @OA\Property(property="published", type="string", example="1 month ago"),
     *                  @OA\Property(property="title", type="string", example="Title of this post"),
     *                  @OA\Property(property="preview", type="object",
     *                      @OA\Property(property="id", type="integer", example=3),
     *                      @OA\Property(property="url_original", type="string", example="http://sanctum/storage/media/45/9TFZiOkNjfiGYdTyunVIxqZAGrAu7yI4MxGcqSyv.jpg"),
     *                      @OA\Property(property="url_preview", type="string", example="http://sanctum/storage/media/45/conversions/9TFZiOkNjfiGYdTyunVIxqZAGrAu7yI4MxGcqSyv-preview.jpg"),
     *                  ),
     *              ),
     *          ),
     *      ),
     *
     *      @OA\Response(response=404, description="Page not found"),
     *  )
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

    /**
     *  @OA\Patch(
     *      path="/api/posts2/{post}",
     *      summary="Обновление",
     *      tags={"posts"},
     *      security={{ "sanctum": "" }},
     *
     *      @OA\Parameter(
     *         description="Post ID",
     *         in="path",
     *         name="post",
     *         required=true,
     *         example=53,
     *      ),
     *
     *      @OA\RequestBody(
     *
     *          @OA\JsonContent(
     *              allOf={
     *
     *                  @OA\Schema(
     *
     *                      @OA\Property(property="body", type="string", example="Body of this updated post"),
     *                      @OA\Property(property="title", type="string", example="Title of this updated post"),
     *                  ),
     *              }
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="body", type="string", example="Body of this post"),
     *                  @OA\Property(property="commentCount", type="integer", example=10),
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="is_liked", type="boolean", example=false),
     *                  @OA\Property(property="likeCount", type="integer", example=3),
     *                  @OA\Property(property="published", type="string", example="1 month ago"),
     *                  @OA\Property(property="title", type="string", example="Title of this post"),
     *                  @OA\Property(property="preview", type="object",
     *                      @OA\Property(property="id", type="integer", example=3),
     *                      @OA\Property(property="url_original", type="string", example="http://sanctum/storage/media/45/9TFZiOkNjfiGYdTyunVIxqZAGrAu7yI4MxGcqSyv.jpg"),
     *                      @OA\Property(property="url_preview", type="string", example="http://sanctum/storage/media/45/conversions/9TFZiOkNjfiGYdTyunVIxqZAGrAu7yI4MxGcqSyv-preview.jpg"),
     *                  ),
     *              ),
     *          ),
     *      ),
     *
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=403, description="Unauthorized"),
     *      @OA\Response(response=404, description="Page not found"),
     *      @OA\Response(response=422, description="Invalid params"),
     *  )
     */
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
     *  @OA\Delete(
     *      path="/api/posts/{post}",
     *      summary="Удаление",
     *      tags={"posts"},
     *      security={{ "sanctum": "" }},
     *
     *      @OA\Parameter(
     *         description="Post ID",
     *         in="path",
     *         name="post",
     *         required=true,
     *         example=53,
     *      ),
     *
     *      @OA\Response(response=204, description="OK"),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=403, description="Unauthorized"),
     *      @OA\Response(response=404, description="Page not found"),
     *  )
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
    }
}
