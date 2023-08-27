<?php

use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\PreviewController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TelegramController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CommentController;
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
    Route::post('/posts/storeRandomPost', [PostController::class, 'storeRandomPost'])->name('storeRandomPost');
    Route::post('/comments/storeRandomComment', [CommentController::class, 'storeRandomComment'])->name('storeRandomComment');
    Route::apiResource('/users', UserController::class)->only(['index','show','update','destroy']);
    Route::get('/roles/forForm', [RoleController::class, 'forForm'])->name('forForm');
    Route::apiResource('/roles', RoleController::class);
    Route::apiResource('/posts', PostController::class)->only([/*'store','update',*/'destroy']);
    Route::post('/posts2', [PostController::class, 'store2'])->name('postStore2');
    Route::patch('/posts2/{post}', [PostController::class, 'update2'])->name('postUpdate2');
    Route::post('/preview', [PreviewController::class, 'store'])->name('store');
    Route::post('/comments', [CommentController::class, 'store'])->name('storeComment');
});
Route::apiResource('/posts', PostController::class)->only(['index','show']);
Route::get('/comments/{post}', [CommentController::class, 'index'])->name('commentsOfPost')->where('post', '[0-9]+');
Route::get('/currentUser', [UserController::class, 'getCurrentUserForMenu'])->name('getCurrentUserForMenu');

Route::post('/telegram', [TelegramController::class, 'telegramCallback'])->name('telegramCallback');
