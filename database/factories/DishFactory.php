<?php

namespace Database\Factories;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Random\RandomException;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dish>
 */
class DishFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws RandomException
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'restaurant_id' => fn () => Restaurant::factory()->create(),
            'description' => fake()->text(100),
            'price' => fake()->randomFloat(2, 1, 100),
            'is_available' => random_int(0, 1),
        ];
    }
}
