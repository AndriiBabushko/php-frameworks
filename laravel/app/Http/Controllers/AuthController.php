<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controller;
use App\Entities\User;

class AuthController extends Controller
{
    public function register(Request $r, EntityManagerInterface $em)
    {
        $data = $r->validate([
            'username'        => 'required|string|min:3|max:50',
            'email'           => 'required|email|unique:App\Entities\User,email',
            'password'        => 'required|string|min:8',
            'profile_picture' => 'nullable|url|max:255',
        ]);

        $user = new User();
        $user->setUsername($data['username'])
            ->setEmail($data['email'])
            ->setPassword(Hash::make($data['password']))
            ->setCreatedAt(new \DateTimeImmutable())
            ->setProfilePicture($data['profile_picture'] ?? null)
            ->setRoles(['ROLE_MANAGER']);

        $em->persist($user);
        $em->flush();

        $token = auth('api')->login($user);

        return response()->json([
            'token'      => $token,
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ], 201);
    }

    public function login(Request $r)
    {
        $creds = $r->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if (! $token = auth('api')->attempt($creds)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'token'      => $token,
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
