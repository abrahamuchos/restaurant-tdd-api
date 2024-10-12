<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteRestaurantTest extends TestCase
{
    use RefreshDatabase;
    protected User|Collection|Model $user;
    protected Restaurant|Collection|Model $restaurant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create([
            'user_id'   => $this->user->id,
        ]);
        Restaurant::factory(10)->create();
    }

    public function test_authenticated_user_can_delete_their_restaurant() : void
    {
        $response = $this->apiAs($this->user, 'delete', "$this->apiBase/restaurants/{$this->restaurant->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('restaurants', ['id' => $this->restaurant->id]);
    }

    public function test_authenticated_user_cannot_delete_other_user_restaurant() : void
    {
        $otherUser = User::factory()->create();

        $response = $this->apiAs($otherUser, 'delete', "$this->apiBase/restaurants/{$this->restaurant->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('restaurants', ['id' => $this->restaurant->id]);
    }

    public function test_unauthenticated_user_cannot_delete_restaurant() : void
    {
        $response = $this->deleteJson("$this->apiBase/restaurants/{$this->restaurant->id}");

        $response->assertStatus(401);
        $this->assertDatabaseHas('restaurants', ['id' => $this->restaurant->id]);
    }
}
