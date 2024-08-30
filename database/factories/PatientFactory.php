<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => random_int(1, 50),
            'contact' => fake()->phoneNumber,
            'gender' => fake()->boolean(50) ? 'M' : 'F',
            'dob' => fake()->date(),
            'address' => fake()->address,
        ];
    }
}
