<?php

namespace Tests\Feature\Menu;

use App\Models\Dish;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class CreateMenuTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Restaurant $restaurant;
    protected Dish|Collection $dishes;
    protected array $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = Restaurant::factory()->create();
        $this->user = $this->restaurant->user;
        $this->dishes = Dish::factory(10)->create([
            'restaurant_id' => $this->restaurant->id,
        ]);
        $this->data = [
            'name' => 'Menu 1',
            'description' => 'Menu 1 description',
            'restaurantId' => $this->restaurant->id,
            'dishes' => $this->dishes->pluck('id')->toArray(),
        ];
    }

    public function test_unauthenticated_user_cannot_create_menu()
    {
        $response = $this->postJson(
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus",
            $this->data
        );

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_menu_for_their_restaurant()
    {
        $response = $this->apiAs(
            $this->user,
            'post',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus",
            $this->data
        );

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'restaurantId',
                'relationships' => [
                    'dishes' => [
                        '*' => ['id', 'name', 'description', 'price', 'restaurantId']
                    ]
                ]
            ]
        ]);
        $this->assertDatabaseHas('menus', [
            'id' => $response->json('data.id'),
            'name' => $this->data['name'],
            'description' => $this->data['description'],
            'restaurant_id' => $this->data['restaurantId'],
        ]);
    }

    public function test_authenticated_user_can_create_menu_with_empty_dishes()
    {
        $this->data['dishes'] = null;

        $response = $this->apiAs(
            $this->user,
            'post',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus",
            $this->data
        );

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'restaurantId',
                'relationships' => [
                    'dishes' => []
                ]
            ]
        ]);
        $this->assertDatabaseHas('menus', [
            'id' => $response->json('data.id'),
            'name' => $this->data['name'],
            'description' => $this->data['description'],
            'restaurant_id' => $this->data['restaurantId'],
        ]);
    }

    public function test_authenticated_user_can_create_menu_with_dishes()
    {
        $response = $this->apiAs(
            $this->user,
            'post',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus",
            $this->data
        );

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'restaurantId',
                'relationships' => [
                    'dishes' => [
                        '*' => ['id', 'name', 'description', 'price', 'restaurantId']
                    ]
                ]
            ]
        ]);
        $this->assertDatabaseHas('menus', [
            'id' => $response->json('data.id'),
            'name' => $this->data['name'],
            'description' => $this->data['description'],
            'restaurant_id' => $this->data['restaurantId'],
        ]);
        $this->assertDatabaseHas('dish_menu', [
            'menu_id' => $response->json('data.id'),
            'dish_id' => $this->data['dishes'][0],
        ]);
    }

    public function test_authenticated_user_cannot_create_menu_for_another_restaurant()
    {
        $this->data['restaurantId'] = Restaurant::factory()->create()->id;

        $response = $this->apiAs(
            $this->user,
            'post',
            "$this->apiBase/restaurants/{$this->data['restaurantId']}/menus",
            $this->data
        );

        $response->assertStatus(403);
    }

    public function test_authenticated_user_cannot_create_menu_with_invalid_dishes()
    {
        $this->data['dishes'] = [999, 888, 777];

        $response = $this->apiAs(
            $this->user,
            'post',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus",
            $this->data
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors']);
    }

    public function test_authenticated_user_cannot_create_menu_with_duplicates_dishes()
    {
        $this->data['dishes'] = [$this->dishes[0]->id, $this->dishes[0]->id];

        $response = $this->apiAs(
            $this->user,
            'post',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus",
            $this->data
        );

        $response->assertStatus(201);
        $this->assertDatabaseHas('menus', [
            'id' => $response->json('data.id'),
            'name' => $this->data['name'],
            'description' => $this->data['description'],
            'restaurant_id' => $this->data['restaurantId'],
        ]);
        $this->assertDatabaseCount('dish_menu', 1);
    }

    public function test_authenticated_user_cannot_create_menu_with_dishes_from_another_restaurant()
    {
        $dish = Dish::factory()->create();
        $this->data['dishes'] = [$dish->id];

        $response = $this->apiAs(
            $this->user,
            'post',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus",
            $this->data
        );

        $response->assertStatus(422);
    }
}
