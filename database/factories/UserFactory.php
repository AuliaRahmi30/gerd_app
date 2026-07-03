<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Password default factory
     */
    protected static ?string $password;

    /**
     * Define model default state
     */
    public function definition(): array
    {
        return [

            'name' => fake()->name(),

            'email' => fake()->unique()->safeEmail(),

            'password' => static::$password ??= Hash::make('password'),

            'chat_id' => null,

            'remember_token' => Str::random(10),
        ];
    }
}