<?php

namespace Tests\Unit\Models;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use \Tests\TestCase;

class RestaurantTest extends TestCase
{

    use RefreshDatabase;

    public function test_relationship_restaurants_with_user(): void
    {
        $restaurant = Restaurant::factory()->create();

        $this->assertInstanceOf(User::class, $restaurant->user);
    }
}
