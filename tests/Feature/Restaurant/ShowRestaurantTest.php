<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowRestaurantTest extends TestCase
{
    use RefreshDatabase;

    protected User|Collection|Model $user;
    protected Restaurant|Collection|Model $restaurant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->restaurant = Restaurant::factory()->create();
        $this->user = $this->restaurant->user;
    }

    public function test_authenticated_user_can_view_their_own_restaurant():void
    {
        $response = $this->apiAs($this->user, 'get', "$this->apiBase/restaurants/{$this->restaurant->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'address',
                'phone',
                'email',
                'website',
                'openingHour',
                'closingHour',
                'image',
                'createdAt',
                'updatedAt'
            ]
        ]);
    }

    public function test_auth_user_cannot_view_other_user_restaurant():void
    {
        $otherUser = User::factory()->create();

        $response = $this->apiAs($otherUser, 'get', "$this->apiBase/restaurants/{$this->restaurant->id}");

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_view_a_restaurant():void
    {
        $response = $this->getJson("$this->apiBase/restaurants/{$this->restaurant->id}");

        $response->assertStatus(401);
    }

    public function test_restaurant_not_found():void
    {
        $nonExistRestaurantId = 20000;

        $response = $this->apiAs($this->user, 'get', "$this->apiBase/restaurants/$nonExistRestaurantId");

        $response->assertStatus(404);
    }



}
