<?php

namespace Tests\Feature\Dish;

use App\Models\Dish;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchDishTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Restaurant $restaurant;
    protected array $data;

    protected function setUp(): void
    {
        parent::setUp();
        $this->data = [
            'name' => 'Test Dish',
            'description' => 'Test Description',
        ];
        $this->restaurant = Restaurant::factory()->create();
        $this->user = $this->restaurant->user;
        Dish::factory()
            ->create([
                'restaurant_id' => $this->restaurant->id,
            ]);
        Dish::factory(150)->create([
            'restaurant_id' => $this->restaurant->id,
        ]);
        $this->dish = Dish::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            ...$this->data,
        ]);
    }

    public function test_an_authenticated_user_can_search_menu_by_name()
    {
        $toSearch = 'Test Di';
        $response = $this->apiAs(
            $this->user,
            'GET',
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes?search=$toSearch"
        );

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.name', $this->dish->name);
    }

    public function test_an_authenticated_user_can_search_menu_by_description()
    {
        $toSearch = 'Test Di';
        $response = $this->apiAs(
            $this->user,
            'GET',
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes?search=$toSearch"
        );

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.description', $this->dish->description);
    }
}
