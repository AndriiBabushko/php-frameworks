<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::post('/', [UserController::class, 'store']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

Route::prefix('posts')->group(function () {
    Route::get('/debug', function () {
        return response()->json([
            'db_url'  => env('DB_CONNECTION') . '://' . env('DB_USERNAME') . ':' . env('DB_PASSWORD') . '@' . env('DB_HOST') . ':' . env('DB_PORT') . '/' . env('DB_DATABASE'),
            'APP_ENV' => env('APP_ENV')
        ]);
    });
    Route::get('/', [PostController::class, 'index']);
    Route::get('/{id}', [PostController::class, 'show']);
    Route::post('/', [PostController::class, 'store']);
    Route::put('/{id}', [PostController::class, 'update']);
    Route::delete('/{id}', [PostController::class, 'destroy']);
});
