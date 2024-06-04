<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ClientFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'tva' => fake()->phoneNumber(),
            'company' => fake()->company(),
            'adresse' => fake()->address(),
            'code_postal' => fake()->phoneNumber(),
            'ville' => fake()->city(),
        ];
    }

}
