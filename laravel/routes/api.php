<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::post('/', [UserController::class, 'store']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

Route::prefix('reactions')->group(function () {
    Route::get('/', [ReactionController::class, 'index']);
    Route::get('/{id}', [ReactionController::class, 'show']);
    Route::post('/', [ReactionController::class, 'store']);
    Route::put('/{id}', [ReactionController::class, 'update']);
    Route::delete('/{id}', [ReactionController::class, 'destroy']);
});

Route::prefix('comments')->group(function () {
    Route::get('/', [CommentController::class, 'index']);
    Route::get('/{id}', [CommentController::class, 'show']);
    Route::post('/', [CommentController::class, 'store']);
    Route::put('/{id}', [CommentController::class, 'update']);
    Route::delete('/{id}', [CommentController::class, 'destroy']);
});

Route::prefix('notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::get('/{id}', [NotificationController::class, 'show']);
    Route::post('/', [NotificationController::class, 'store']);
    Route::put('/{id}', [NotificationController::class, 'update']);
    Route::delete('/{id}', [NotificationController::class, 'destroy']);
});

Route::post('register', [AuthController::class,'register']);
Route::post('login',    [AuthController::class,'login']);

Route::middleware('auth:api')->group(function(){
    Route::get('posts',        [PostController::class,'index'])->middleware('role:ROLE_CLIENT,ROLE_MANAGER,ROLE_ADMIN');
    Route::get('posts/{id}',   [PostController::class,'show'])->middleware('role:ROLE_CLIENT,ROLE_MANAGER,ROLE_ADMIN');
    Route::post('posts',       [PostController::class,'store'])->middleware('role:ROLE_MANAGER,ROLE_ADMIN');
    Route::put('posts/{id}',   [PostController::class,'update'])->middleware('role:ROLE_MANAGER,ROLE_ADMIN');
    Route::delete('posts/{id}',[PostController::class,'destroy'])->middleware('role:ROLE_ADMIN');
});

