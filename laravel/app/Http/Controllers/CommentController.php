<?php

namespace App\Http\Controllers;

use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    private CommentService $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function index(): \Illuminate\Http\JsonResponse
    {
        $comments = $this->commentService->getAllComments();
        return response()->json($comments);
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        $comment = $this->commentService->getComment($id);
        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }
        return response()->json($comment);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        // Очікуємо ключі: content, post_id, user_id
        $data = $request->only(['content', 'post_id', 'user_id']);
        try {
            $comment = $this->commentService->createComment($data);
            return response()->json($comment, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $data = $request->only(['content', 'post_id', 'user_id']);
        try {
            $comment = $this->commentService->updateComment($id, $data);
            if (!$comment) {
                return response()->json(['message' => 'Comment not found'], 404);
            }
            return response()->json($comment);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $deleted = $this->commentService->deleteComment($id);
        if (!$deleted) {
            return response()->json(['message' => 'Comment not found'], 404);
        }
        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
