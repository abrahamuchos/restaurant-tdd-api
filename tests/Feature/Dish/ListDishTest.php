<?php

namespace Tests\Feature\Dish;

use App\Models\Dish;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\HigherOrderCollectionProxy;
use Tests\TestCase;

class ListDishTest extends TestCase
{
    use RefreshDatabase;

    protected int $perPage;
    protected Restaurant|Collection|Model $restaurant;
    protected User|HigherOrderCollectionProxy $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->perPage = 15;
        $this->restaurant = Restaurant::factory()->create();
        $this->user = $this->restaurant->user;
        Dish::factory(15)->create([
            'restaurant_id' => $this->restaurant->id,
        ]);
    }

    public function test_authenticated_user_can_list_their_dishes(): void
    {
        $response = $this->apiAs($this->user, 'get', "$this->apiBase/restaurants/{$this->restaurant->id}/dishes");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'isAvailable',
                    'createdAt',
                    'updatedAt',
                ]
            ]
        ]);
    }

    public function test_authenticated_user_cannot_list_dishes_of_other_restaurants(): void
    {
        $otherRestaurant = Restaurant::factory()->create();

        $response = $this->apiAs(
            $this->user,
            'get',
            "$this->apiBase/restaurants/$otherRestaurant->id/dishes"
        );

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_list_dishes(): void
    {
        $response = $this->getJson("$this->apiBase/restaurants/{$this->restaurant->id}/dishes");

        $response->assertStatus(401);
    }

    public function test_auth_user_can_list_dishes_with_pagination(): void
    {
        $response = $this->apiAs($this->user, 'get', "$this->apiBase/restaurants/{$this->restaurant->id}/dishes");

        $response->assertStatus(200);
        $response->assertJsonCount($this->perPage, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'isAvailable',
                    'createdAt',
                    'updatedAt',
                ]
            ],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'links' => [
                    '*' => [
                        'url',
                        'label',
                        'active'
                    ]
                ],
                'path',
                'per_page',
                'to',
                'total'
            ],
            'links' => [
                'first',
                'last',
                'prev',
                'next'
            ]
        ]);
    }

    public function test_auth_user_can_list_dishes_with_change_page(): void
    {
        $page = 2;

        $response = $this->apiAs(
            $this->user,
            'get',
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes?page=$page"
        );

        $response->assertStatus(200);
        $response->assertJson([
            'meta' => [
                'current_page' => $page,
            ],
        ]);
    }

    public function test_auth_user_can_list_dishes_with_change_per_page(): void
    {
        $perPage = 17;

        $response = $this->apiAs(
            $this->user,
            'get',
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes?perPage=$perPage"
        );

        $response->assertStatus(200);
        $response->assertJson([
            'meta' => [
                'per_page' => $perPage,
            ],
        ]);
    }

}
