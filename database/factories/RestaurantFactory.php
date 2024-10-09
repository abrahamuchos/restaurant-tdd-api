<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Random\RandomException;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Restaurant>
 */
class RestaurantFactory extends Factory
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
            'user_id' => User::factory(),
//            'user_id' => fn() => User::factory()->create(),
            'code' => fake()->unique()->randomNumber(6),
            'name' => fake()->company,
            'description' => fake()->text,
            'address' => fake()->address,
            'phone' => fake()->phoneNumber,
            'email' => fake()->companyEmail,
            'website' => fake()->url,
            'opening_hour' => fake()->time('H:i:s'),
            'closing_hour' =>  fake()->time('H:i:s'),
            'logo' => random_int(0, 1) ? fake()->imageUrl : null,
            'image' => random_int(0, 1) ? fake()->imageUrl : null,
        ];
    }
}
