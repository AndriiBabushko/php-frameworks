<?php

namespace App\Http\Controllers;

use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    private PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index(): \Illuminate\Http\JsonResponse
    {
        $posts = $this->postService->getAllPosts();
        return response()->json($posts);
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        $post = $this->postService->getPost($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        return response()->json($post);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->only(['title', 'content', 'imageUrl', 'author_id']);
        try {
            $post = $this->postService->createPost($data);
            return response()->json($post, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $data = $request->only(['title', 'content', 'imageUrl', 'author_id']);
        try {
            $post = $this->postService->updatePost($id, $data);
            if (!$post) {
                return response()->json(['message' => 'Post not found'], 404);
            }
            return response()->json($post);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $deleted = $this->postService->deletePost($id);
        if (!$deleted) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        return response()->json(['message' => 'Post deleted successfully']);
    }
}
