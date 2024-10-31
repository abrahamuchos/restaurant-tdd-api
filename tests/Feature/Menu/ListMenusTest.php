<?php

namespace Tests\Feature\Menu;

use App\Models\Dish;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ListMenusTest extends TestCase
{
    use RefreshDatabase;

    protected \App\Models\User $user;
    protected Restaurant $restaurant;
    protected Dish|Collection $dishes, $newDishes;
    protected Menu|Collection $menu;
    protected int $perPage;

    protected function setUp(): void
    {
        parent::setUp();
        $this->perPage = 15;
        $this->restaurant = Restaurant::factory()->create();
        $this->user = $this->restaurant->user;
        $this->dishes = Dish::factory()->count(100)->create();
        $this->menu = Menu::factory(150)
            ->hasAttached($this->dishes->random(10))
            ->create([
                'restaurant_id' => $this->restaurant->id,
            ]);
    }

    public function test_unauthenticated_user_cannot_list_menus()
    {
        $response = $this->getJson("$this->apiBase/restaurants/{$this->restaurant->id}/menus");

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_list_their_menus_with_pagination()
    {
        $response = $this->apiAs(
            $this->user,
            'get',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus"
        );

        $response->assertStatus(200);
        $response->assertJsonCount($this->perPage, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'dishes' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'price',
                            'isAvailable',
                        ]
                    ]
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

    public function test_authenticated_user_can_list_their_menus_with_change_page()
    {
        $page = 4;

        $response = $this->apiAs(
            $this->user,
            'get',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus/?page=$page"
        );

        $response->assertStatus(200);
        $response->assertJsonPath('meta.current_page', $page);

    }

    public function test_authenticated_user_cannot_list_other_restaurants_menus()
    {
        $otherRestaurant = Restaurant::factory()->create();
        $otherDishes = Dish::factory()->count(10)->create();
        $this->menu = Menu::factory(150)
            ->hasAttached($otherDishes)
            ->create([
                'restaurant_id' => $otherRestaurant,
            ]);

        $response = $this->apiAs(
            $this->user,
            'get',
            "$this->apiBase/restaurants/{$otherRestaurant->id}/menus"
        );

        $response->assertStatus(403);
    }

}
