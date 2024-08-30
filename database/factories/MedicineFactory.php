<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medicine>
 */
class MedicineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Sample arrays for generating pseudo-scientific names
        $prefixes = ['Apo', 'Benzo', 'Corti', 'Dextro', 'Erythro', 'Fluoro', 'Gluco', 'Hydro'];
        $suffixes = ['mycin', 'pril', 'cillin', 'zole', 'mab', 'statin', 'vir', 'caine'];

        // Combining prefix, middle part, and suffix to create a name
        $name = $this->faker->randomElement($prefixes)
            . $this->faker->lexify('??')  // Adds a random middle part like 'xx'
            . $this->faker->randomElement($suffixes);

        return [
            'name' => $name,
            // 'description' => $this->faker->sentence(), // Random description
            // 'price' => $this->faker->randomFloat(2, 5, 100), // Random price between 5 and 100
        ];
    }
}
