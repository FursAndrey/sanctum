<?php

use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\PreviewController;
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
    Route::apiResource('/posts', PostController::class);
    Route::post('/preview', [PreviewController::class, 'store']);
    
    Route::get('/close', [MainController::class, 'closeTest']);
});
Route::get('/open', [MainController::class, 'openTest']);
