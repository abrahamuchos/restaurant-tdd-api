<?php

namespace Tests\Feature\Menu;

use App\Models\Dish;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateMenuTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Restaurant $restaurant;
    protected Dish $dish;
    protected array $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dishes = Dish::factory(10)->create();
        $this->restaurant = $this->dishes[0]->restaurant;
        $this->user = $this->restaurant->user;
        $this->data = [
            'name' => 'Menu 1',
            'description' => 'Menu 1 description',
            'restaurantId' => $this->restaurant->id,
            'dishes' => $this->dishes->pluck('id')->toArray(),
        ];
    }

    public function test_authenticated_can_create_menu_for_their_restaurant()
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
                'dishes' => [
                    '*' => ['id', 'name', 'description', 'price', 'restaurantId']
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


}
