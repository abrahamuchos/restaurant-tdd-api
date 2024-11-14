<?php

namespace Tests\Feature\Restaurant;

use App\Models\Dish;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

class SearchRestaurantTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Restaurant $restaurant;
    protected array $data;


    protected function setUp(): void
    {
        parent::setUp();
        $this->data = [
            'name' => 'Test Restaurant',
            'description' => 'Test Description',
            'code' => 'TR',
        ];
        $this->user = User::factory()->create();
        Restaurant::factory()->count(100)->create([
            'user_id' => $this->user->id,
        ]);
        $this->restaurant = Restaurant::factory()->create([
            'user_id' => $this->user->id,
            ...$this->data,
        ]);
    }

    public function test_an_authenticated_user_can_search_restaurants_by_name()
    {
        $toSearch = 'Test Res';
        $response = $this->apiAs(
            $this->user,
            'GET',
            "$this->apiBase/restaurants?search=$toSearch"
        );

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.name', $this->restaurant->name);
    }

    public function test_an_authenticated_user_can_search_restaurants_by_description()
    {
        $toSearch = 'Test Des';
        $response = $this->apiAs(
            $this->user,
            'GET',
            "$this->apiBase/restaurants?search=$toSearch"
        );

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.description', $this->restaurant->description);
    }


}
