<?php

namespace Database\Seeders;

use App\Models\Dish;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurants = Restaurant::all();

        $restaurants->each(function (Restaurant $restaurant) {
            $dishes = Dish::factory(25)->create([
                'restaurant_id' => $restaurant->id,
            ]);

            $menu = Menu::factory()->create([
                'restaurant_id' => $restaurant->id,
            ]);
            $menu->dishes()->sync($dishes);
        });
    }
}
