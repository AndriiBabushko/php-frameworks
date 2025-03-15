<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    // Діагностичний маршрут для перевірки DATABASE_URL та APP_ENV
    // GET /api/posts/debug
    public function debugDbUrl(): JsonResponse
    {
        return response()->json([
            'db_url'  => env('DATABASE_URL'),
            'APP_ENV' => env('APP_ENV')
        ]);
    }

    // GET /api/posts
    public function index(): JsonResponse
    {
        // Завантажуємо користувача, якщо потрібен зв'язок
        $posts = Post::with('user')->get();
        return response()->json(['data' => $posts]);
    }

    // GET /api/posts/{id}
    public function show($id): JsonResponse
    {
        $post = Post::with('user')->find($id);
        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }
        return response()->json(['data' => $post]);
    }

    // POST /api/posts
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'content'  => 'required|string',
            'image_url'=> 'nullable|string',
            'user_id'  => 'required|exists:users,id'
        ]);

        $post = Post::create($data);
        return response()->json(['data' => $post], 201);
    }

    // PUT /api/posts/{id}
    public function update(Request $request, $id): JsonResponse
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        $data = $request->validate([
            'content'  => 'sometimes|required|string',
            'image_url'=> 'nullable|string'
        ]);

        $post->update($data);
        return response()->json(['data' => $post]);
    }

    // DELETE /api/posts/{id}
    public function destroy($id): JsonResponse
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        $post->delete();
        return response()->json(['message' => 'Post deleted successfully']);
    }
}
