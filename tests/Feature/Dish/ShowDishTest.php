<?php

namespace Tests\Feature\Dish;

use App\Models\Dish;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowDishTest extends TestCase
{

    use RefreshDatabase;

    protected Dish $dish;
    protected Restaurant $restaurant;
    protected \App\Models\User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dish = Dish::factory()->create();
        $this->restaurant = $this->dish->restaurant;
        $this->user = $this->restaurant->user;
    }

    public function test_authenticated_user_can_see_a_dish_of_his_restaurant()
    {
        $response = $this->apiAs(
            $this->user,
            'get',
            "$this->apiBase/{$this->restaurant->id}/dishes/{$this->dish->id}"
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'restaurantId',
                'name',
                'description',
                'price',
                'createdAt',
                'updatedAt',
                'restaurant' => [
                    'id',
                    'userId',
                    'code',
                    'name'
                ],
            ],
        ]);
    }

    public function test_authenticated_user_cannot_see_a_dish_of_another_restaurant()
    {
        $otherRestaurant = Restaurant::factory()->create();

        $response = $this->apiAs(
            $this->user,
            'get',
            "$this->apiBase/{$otherRestaurant->id}/dishes/{$this->dish->id}"
        );

        $response->assertStatus(403);
    }

    public function test_authenticated_user_cannot_see_a_dish_of_another_user()
    {
        $plate = Dish::factory()->create();

        $response = $this->apiAs(
            $this->user,
            'get',
            "$this->apiBase/{$plate->restaurant->id}/dishes/{$plate->id}"
        );

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_see_a_dish_of_a_restaurant()
    {
        $response = $this->getJson("$this->apiBase/{$this->restaurant->id}/dishes/{$this->dish->id}");

        $response->assertStatus(401);
    }

    public function test_authenticated_user_cannot_see_a_dish_that_does_not_exist()
    {
        $dishId = 9999;

        $response = $this->apiAs(
            $this->user,
            'get',
            "$this->apiBase/{$this->restaurant->id}/dishes/$dishId"
        );

        $response->assertStatus(404);
    }

    public function test_authenticated_user_cannot_see_a_dish_of_a_restaurant_that_does_not_exist()
    {
        $restaurantId = 9999;

        $response = $this->apiAs(
            $this->user,
            'get',
            "$this->apiBase/$restaurantId/dishes/{$this->dish->id}"
        );

        $response->assertStatus(404);
    }

}
