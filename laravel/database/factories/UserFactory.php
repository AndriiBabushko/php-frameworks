<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'username'        => fake()->userName(),
            'email'           => fake()->unique()->safeEmail(),
            'password'        => static::$password ??= Hash::make('password'),
            'profile_picture' => fake()->imageUrl(200, 200, 'people', true),
            'created_at'      => now(),
            'updated_at'      => now(),
        ];
    }
}
