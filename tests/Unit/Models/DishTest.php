<?php

namespace Tests\Unit\Models;

use App\Models\Dish;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use \Tests\TestCase;

class DishTest extends TestCase
{
    use RefreshDatabase;

    public function test_relationship_dish_with_restaurant(): void
    {
        $dish = Dish::factory()->create();

        $this->assertInstanceOf(Restaurant::class, $dish->restaurant);
    }

    public function test_relationship_dishes_with_menus(): void
    {
        $dish = Dish::factory()->create();
        $menus = Menu::factory(5)->create([
            'restaurant_id' => $dish->first()->restaurant_id
        ]);

        $dish->menus()->attach($menus);

        $this->assertCount(5, $dish->menus);
        $this->assertInstanceOf(Menu::class, $menus->first());
    }
}
