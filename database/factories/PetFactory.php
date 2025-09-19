<?php
namespace Database\Factories;

use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'owner_id' => Owner::factory(), // Automatically create an owner
            'name' => fake()->firstName(),
            'species' => fake()->randomElement(['Dog', 'Cat']),
            'breed' => fake()->randomElement(['Golden Retriever', 'Siamese', 'Boxer', 'Poodle', 'Tabby']),
            'birth_date' => fake()->dateTimeBetween('-10 years', '-1 month'),
            'gender' => fake()->randomElement(['Male', 'Female']),
            'allergies' => fake()->randomElement(['No allergies', 'Pollen', 'Flea bites']),
        ];
    }
}
