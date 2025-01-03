<?php

namespace Tests\Feature\Menu;

use App\Models\Dish;
use App\Models\Menu;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ShowMenuTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Restaurant $restaurant;
    protected Dish|Collection $dishes;
    protected Menu|Collection $menu;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = Restaurant::factory()->create();
        $this->user = $this->restaurant->user;
        $this->dishes = Dish::factory()->count(15)->create([
            'restaurant_id' => $this->restaurant->id,
        ]);
        $this->menu = Menu::factory()
            ->hasAttached($this->dishes)
            ->create([
                'restaurant_id' => $this->restaurant->id,
            ]);
    }

    public function test_unauthenticated_cannot_see_menu_of_restaurant()
    {
        $response = $this->getJson("$this->apiBase/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}");

        $response->assertStatus(401);
    }

    public function test_authenticated_can_see_menu_of_restaurant()
    {
        $response = $this->apiAs(
            $this->user,
            'GET',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}"
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'name',
                'description',
                'relationships' => [
                    'dishes' => [
                        '*' => ['id', 'name', 'description', 'price', 'restaurantId']
                    ],
                    'restaurant' => [
                        'id',
                        'name'
                    ]
                ],
                'links' => [
                    'self',
                    'parent',
                    'public',
                ]
            ]
        ]);
        $response->assertJsonPath(
            'data.links.self',
            route('restaurants.menus.show', [$this->restaurant->id, $this->menu->id])
        );
        $response->assertJsonPath(
            'data.links.parent',
            route('restaurants.show', $this->restaurant->id)
        );
        $response->assertJsonPath(
            'data.links.public',
            route('public.menus.show', $this->menu->id)
        );
    }



}
