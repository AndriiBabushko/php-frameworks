<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(): \Illuminate\Http\JsonResponse
    {
        $users = $this->userService->getAllUsers();
        return response()->json($users);
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        $user = $this->userService->getUser($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->only(['username', 'email', 'password', 'profilePicture']);
        try {
            $user = $this->userService->createUser($data);
            return response()->json($user, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $data = $request->only(['username', 'email', 'password', 'profilePicture']);
        try {
            $user = $this->userService->updateUser($id, $data);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            return response()->json($user);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 400);
        }
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $deleted = $this->userService->deleteUser($id);
        if (!$deleted) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json(['message' => 'User deleted successfully']);
    }
}
