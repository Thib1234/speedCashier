<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ProductFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'price' => fake()->randomNumber(3, false),
            'price_htva' => fake()->randomNumber(3, false),
            'stock' => fake()->randomNumber(1, false),
            'purchase_price' => fake()->randomNumber(1, false),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
}
