<?php

namespace Tests\Unit\Models;

use App\Models\Dish;
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
}
