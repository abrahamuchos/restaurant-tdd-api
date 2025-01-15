<?php

namespace Tests\Feature\Dish;

use App\Models\Dish;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteDishTest extends TestCase
{
    use RefreshDatabase;

    protected Dish $dish;
    protected \App\Models\User $user;
    protected \App\Models\Restaurant $restaurant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dish = Dish::factory()->create();
        $this->restaurant = $this->dish->restaurant;
        $this->user = $this->dish->restaurant->user;
    }

    public function test_unauthenticated_user_cannot_delete_dish()
    {
        $response = $this->deleteJson("$this->apiBase/restaurants/{$this->restaurant->id}/dishes/{$this->dish->id}");

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_delete_their_dish()
    {
        $response = $this->apiAs(
            $this->user,
            'delete',
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes/{$this->dish->id}"
        );

        $response->assertStatus(204);
        $this->assertDatabaseMissing('dishes', ['id' => $this->dish->id]);
    }

    public function test_authenticated_user_cannot_delete_other_users_dish()
    {
        $otherDish = Dish::factory()->create();

        $response = $this->apiAs(
          $this->user,
            'delete',
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes/{$otherDish->id}"
        );

        $response->assertStatus(403);
        $this->assertDatabaseHas('dishes', ['id' => $otherDish->id]);
    }

    public function test_authenticated_user_can_delete_dish_but_not_delete_restaurant()
    {
        $response = $this->apiAs(
            $this->user,
            'delete',
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes/{$this->dish->id}"
        );

        $response->assertStatus(204);
        $this->assertDatabaseMissing('dishes', ['id' => $this->dish->id]);
        $this->assertDatabaseHas('restaurants', ['id' => $this->restaurant->id]);
    }
}
