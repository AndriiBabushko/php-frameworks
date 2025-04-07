<?php

namespace App\Http\Controllers;

use App\Services\ReactionService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class ReactionController extends Controller
{
    private ReactionService $reactionService;

    public function __construct(ReactionService $reactionService)
    {
        $this->reactionService = $reactionService;
    }

    public function index(): \Illuminate\Http\JsonResponse
    {
        $reactions = $this->reactionService->getAllReactions();
        return response()->json($reactions);
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        $reaction = $this->reactionService->getReaction($id);
        if (!$reaction) {
            return response()->json(['message' => 'Reaction not found'], 404);
        }
        return response()->json($reaction);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->only(['type', 'post_id', 'user_id']);
        try {
            $reaction = $this->reactionService->createReaction($data);
            return response()->json($reaction, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $data = $request->only(['type', 'post_id', 'user_id']);
        try {
            $reaction = $this->reactionService->updateReaction($id, $data);
            if (!$reaction) {
                return response()->json(['message' => 'Reaction not found'], 404);
            }
            return response()->json($reaction);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $deleted = $this->reactionService->deleteReaction($id);
        if (!$deleted) {
            return response()->json(['message' => 'Reaction not found'], 404);
        }
        return response()->json(['message' => 'Reaction deleted successfully']);
    }
}
