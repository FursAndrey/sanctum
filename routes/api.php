<?php

use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\PreviewController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TelegramController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/users/storeRandomUser', [UserController::class, 'storeRandomUser'])->name('storeRandomUser');
    Route::get('/users/forChat', [UserController::class, 'getUsersForChat'])->name('getUsersForChat');
    Route::post('/posts/storeRandomPost', [PostController::class, 'storeRandomPost'])->name('storeRandomPost');
    Route::post('/comments/storeRandomComment', [CommentController::class, 'storeRandomComment'])->name('storeRandomComment');
    Route::apiResource('/users', UserController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::get('/roles/forForm', [RoleController::class, 'forForm'])->name('forForm');
    Route::apiResource('/roles', RoleController::class);
    Route::apiResource('/posts', PostController::class)->only([/*'store','update',*/ 'destroy']);
    Route::post('/posts2', [PostController::class, 'store2'])->name('postStore2');
    Route::patch('/posts2/{post}', [PostController::class, 'update2'])->name('postUpdate2');
    Route::post('/preview', [PreviewController::class, 'store'])->name('store');
    Route::apiResource('/comments', CommentController::class)->only(['store', 'update', 'destroy']);
    Route::post('/postLike/{post}', [LikeController::class, 'postToggleLike'])->name('postToggleLike');
    Route::post('/commentLike/{comment}', [LikeController::class, 'commentToggleLike'])->name('commentToggleLike');
    Route::apiResource('/chats', ChatController::class)->only(['index', 'show', 'store']);
});
Route::apiResource('/posts', PostController::class)->only(['index', 'show']);
Route::get('/comments/{post}/{connemt}', [CommentController::class, 'index'])->name('commentsOfPost')->where(['post' => '[0-9]+', 'connemt' => '[0-9]+']);
Route::get('/currentUser', [UserController::class, 'getCurrentUserForMenu'])->name('getCurrentUserForMenu');

Route::post('/telegram', [TelegramController::class, 'telegramCallback'])->name('telegramCallback');
