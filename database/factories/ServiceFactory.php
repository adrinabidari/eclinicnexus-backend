<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'service' => fake()->name(),
            'service' => implode(' ', fake()->words(2)), // Generates a random service name with 2 words
            'charge' => fake()->boolean(50)
                ? fake()->numberBetween(200, 5000) // Generates a random integer between 200 and 5000
                : fake()->randomFloat(2, 200, 1000), // Generates a random float with 2 decimal places between 200 and 1000
        ];
    }
}
