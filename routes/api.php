<?php

use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\PreviewController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
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

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::apiResource('/users', UserController::class)->only(['index','show','update','destroy']);
    Route::get('/roles/forForm', [RoleController::class, 'forForm'])->name('forForm');
    Route::apiResource('/roles', RoleController::class);
    Route::apiResource('/posts', PostController::class)->only(['store','update','destroy']);
    Route::post('/preview', [PreviewController::class, 'store'])->name('store');
    
    Route::get('/close', [MainController::class, 'closeTest'])->name('closeTest');
});
Route::apiResource('/posts', PostController::class)->only(['index','show']);
Route::get('/currentUser', [UserController::class, 'getCurrentUserForMenu'])->name('getCurrentUserForMenu');
