<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        
        return [
            'user_id' =>  random_int(1,50),
            'contact' => fake()->phoneNumber,
            'gender' => fake()->boolean(50) ? 'M' : 'F',
            'dob' => fake()->date(),
            'address' => fake()->address,
            'specialization_id' =>  random_int(1,50),
            'status' =>  random_int(0,1),
            'created_by' =>  random_int(1,50),
        ];

    }
}
