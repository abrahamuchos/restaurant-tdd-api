<?php

namespace Tests\Feature\Menu;

use App\Models\Menu;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchMenuTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Restaurant $restaurant;
    protected array $data;

    protected function setUp(): void
    {
        parent::setUp();
        $this->data = [
            'name' => 'Test Menu',
            'description' => 'Test Description',
        ];
        $this->restaurant = Restaurant::factory()->create();
        $this->user = $this->restaurant->user;
        Menu::factory(150)
            ->create([
                'restaurant_id' => $this->restaurant->id,
            ]);
        $this->menu = Menu::factory()
            ->create([
                'restaurant_id' => $this->restaurant->id,
                ...$this->data,
            ]);
    }

    public function test_an_authenticated_user_can_search_menu_by_name()
    {
        $toSearch = 'Test Me';
        $response = $this->apiAs(
            $this->user,
            'GET',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus?search=$toSearch"
        );

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.name', $this->menu->name);
    }

    public function test_an_authenticated_user_can_search_menu_by_description()
    {
        $toSearch = 'Test De';
        $response = $this->apiAs(
            $this->user,
            'GET',
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus?search=$toSearch"
        );

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.description', $this->menu->description);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'type',
                    'id',
                    'name',
                    'description',
                    'links',
                    'relationships'
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



}
