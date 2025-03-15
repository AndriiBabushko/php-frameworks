<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // GET /api/users
    public function index(): JsonResponse
    {
        $users = User::all();
        return response()->json(['data' => $users]);
    }

    // GET /api/users/{id}
    public function show($id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        return response()->json(['data' => $user]);
    }

    // POST /api/users
    public function store(Request $request): JsonResponse
    {
        // Валідація даних
        $data = $request->validate([
            'username'        => 'required|string|max:50',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|string|min:6',
            'profile_picture' => 'nullable|string'
        ]);

        // Хешування пароля
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return response()->json(['data' => $user], 201);
    }

    // PUT /api/users/{id}
    public function update(Request $request, $id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $data = $request->validate([
            'username'        => 'sometimes|required|string|max:50',
            'email'           => 'sometimes|required|email|unique:users,email,'.$id,
            'password'        => 'sometimes|required|string|min:6',
            'profile_picture' => 'nullable|string'
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        return response()->json(['data' => $user]);
    }

    // DELETE /api/users/{id}
    public function destroy($id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
