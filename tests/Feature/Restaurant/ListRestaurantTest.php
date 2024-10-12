<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListRestaurantTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->perPage = 15;
        $this->user = User::factory()->create();
        Restaurant::factory($this->perPage + 10)->create([
            'user_id' => $this->user->id,
        ]);
        Restaurant::factory(150)->create();
    }



    public function test_authenticated_user_must_see_their_restaurant_list(): void
    {
        $response = $this->apiAs($this->user, 'get', "$this->apiBase/restaurants");

        $response->assertStatus(200);
        $response->assertJsonCount($this->perPage, 'data');
    }

    public function test_authenticated_user_without_restaurant_must_see_empty_restaurant_list(): void
    {
        $otherUser = User::factory()->create();

        $response = $this->apiAs($otherUser, 'get', "$this->apiBase/restaurants");

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }

    public function test_unauthenticated_user_cannot_see_restaurant_list(): void
    {
        $response = $this->getJson("$this->apiBase/restaurants");

        $response->assertStatus(401);
    }


}
