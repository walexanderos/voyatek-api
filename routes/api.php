<?php

use App\Http\Controllers\API\BlogController;
use App\Http\Controllers\API\PostController;
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

Route::middleware('token')->prefix('blogs')->group(function () {
    Route::get('/', [BlogController::class, 'index']);
    Route::post('/', [BlogController::class, 'store']);
    Route::get('/{slug}', [BlogController::class, 'show']);
    Route::put('/{slug}', [BlogController::class, 'update']);
    Route::delete('/{slug}', [BlogController::class, 'destroy']);

    Route::get('/{blog_slug}/posts', [PostController::class, 'index']);
    Route::post('/{blog_slug}/posts', [PostController::class, 'store']);
    Route::get('/{blog_slug}/posts/{post_slug}', [PostController::class, 'show']);
    Route::post('/{blog_slug}/posts/{post_slug}/update', [PostController::class, 'update']);
    Route::delete('/{blog_slug}/posts/{post_slug}', [PostController::class, 'destroy']);
    Route::post('{blog_slug}/posts/{post_slug}/comments', [PostController::class, 'addComment']);
    Route::post('{blog_slug}/posts/{post_slug}/likes', [PostController::class, 'likePost']);
});
