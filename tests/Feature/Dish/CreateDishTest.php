<?php

namespace Tests\Feature\Dish;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateDishTest extends TestCase
{
    use RefreshDatabase;

    protected Restaurant|Collection|Model $restaurant;
    protected \App\Models\User|Collection|Model $user;
    protected array $data;

    public function setUp(): void
    {
        parent::setUp();

        $this->restaurant = Restaurant::factory()->create();
        $this->user = $this->restaurant->user;
        $this->data = [
            'restaurantId' => $this->restaurant->id,
            'name' => 'Test Dish',
            'description' => 'Test Description',
            'price' => '10.99',
            'isAvailable' => true,
        ];
    }

    public function test_authenticate_user_can_create_dish_for_their_restaurant(): void
    {
        $response = $this->apiAs(
            $this->user,
            'post',
            "$this->apiBase/{$this->restaurant->id}/dishes", $this->data
        );

        $response->assertStatus(201);
        $this->assertDatabaseHas('dishes', [
            'restaurant_id' => $this->restaurant->id,
            'name' => $this->data['name'],
            'description' => $this->data['description'],
            'price' => $this->data['price'],
            'is_available' => $this->data['isAvailable'],
        ]);
    }

    public function test_unauthenticated_user_cannot_create_dish(): void
    {
        $response = $this->postJson("$this->apiBase/{$this->restaurant->id}/dishes", $this->data);

        $response->assertStatus(401);
        $this->assertDatabaseMissing('dishes', [
            'restaurant_id' => $this->restaurant->id,
            'name' => $this->data['name'],
        ]);
    }

    public function test_authenticated_user_cannot_create_dish_for_another_restaurant(): void
    {
        $otherUser = User::factory()->create();

        $response = $this->apiAs(
            $otherUser,
            'post',
            "$this->apiBase/{$this->restaurant->id}/dishes",
            $this->data
        );

        $response->assertStatus(403);
        $this->assertDatabaseMissing('dishes', [
            'restaurant_id' => $this->restaurant->id,
            'name' => $this->data['name'],
        ]);
    }

    public function test_restaurant_must_exist_to_create_dish(): void
    {
        $nonExistentRestaurantId = 999;

        $response = $this->apiAs(
            $this->user,
            'post',
            "$this->apiBase/{$nonExistentRestaurantId}/dishes",
            $this->data
        );

        $response->assertStatus(404);
        $this->assertDatabaseMissing('dishes', [
            'restaurant_id' => $this->restaurant->id,
            'name' => $this->data['name'],
        ]);
    }

    public function test_restaurant_id_must_be_required_to_create_dish(): void
    {
        $this->data['restaurantId'] = null;

        $response = $this->apiAs(
            $this->user,
            'post',
            "$this->apiBase/{$this->restaurant->id}/dishes",
            $this->data
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['restaurantId']]);
        $this->assertDatabaseMissing('dishes', [
            'restaurant_id' => $this->restaurant->id,
            'name' => $this->data['name'],
        ]);
    }

    public function test_name_must_be_required_to_create_dish(): void
    {
        $this->data['name'] = null;

        $response = $this->apiAs(
            $this->user,
            'post',
            "$this->apiBase/{$this->restaurant->id}/dishes",
            $this->data
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['name']]);
        $this->assertDatabaseMissing('dishes', [
            'restaurant_id' => $this->restaurant->id,
            'name' => $this->data['name'],
        ]);
    }

    public function test_name_must_be_max_65_characters_to_create_dish(): void
    {
        $this->data['name'] = \Str::random(66);

        $response = $this->apiAs(
            $this->user,
            'post',
            "$this->apiBase/{$this->restaurant->id}/dishes",
            $this->data
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['name']]);
        $this->assertDatabaseMissing('dishes', [
            'restaurant_id' => $this->restaurant->id,
            'name' => $this->data['name'],
        ]);
    }

    public function test_name_must_be_string_to_create_dish(): void
    {
        $this->data['name'] = 3478346;

        $response = $this->apiAs(
            $this->user,
            'post',
            "$this->apiBase/{$this->restaurant->id}/dishes",
            $this->data
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['name']]);
        $this->assertDatabaseMissing('dishes', [
            'restaurant_id' => $this->restaurant->id,
            'name' => $this->data['name'],
        ]);
    }

    public function test_description_must_be_string_to_create_dish(): void
    {
        $this->data['description'] = 3478346;

        $response = $this->apiAs(
            $this->user,
            'post',
            "$this->apiBase/{$this->restaurant->id}/dishes",
            $this->data
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['description']]);
        $this->assertDatabaseMissing('dishes', [
            'restaurant_id' => $this->restaurant->id,
            'name' => $this->data['name'],
        ]);
    }

    public function test_description_must_be_100_characters_to_create_dish(): void
    {
        $this->data['description'] = \Str::random(101);

        $response = $this->apiAs(
            $this->user,
            'post',
            "$this->apiBase/{$this->restaurant->id}/dishes",
            $this->data
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['description']]);
        $this->assertDatabaseMissing('dishes', [
            'restaurant_id' => $this->restaurant->id,
            'name' => $this->data['name'],
        ]);
    }

    public function test_price_must_be_decimal_with_two_decimal_to_create_dish(): void
    {
        $this->data['price'] = '10.000';
        $response = $this->apiAs(
            $this->user,
            'post',
            "$this->apiBase/{$this->restaurant->id}/dishes",
            $this->data
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['price']]);
        $this->assertDatabaseMissing('dishes', [
            'restaurant_id' => $this->restaurant->id,
            'name' => $this->data['name'],
        ]);
    }

    public function test_price_must_be_required_to_create_dish(): void
    {
        $this->data['price'] = null;

        $response = $this->apiAs(
            $this->user,
            'post',
            "$this->apiBase/{$this->restaurant->id}/dishes",
            $this->data
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['price']]);
        $this->assertDatabaseMissing('dishes', [
            'restaurant_id' => $this->restaurant->id,
            'name' => $this->data['name'],
        ]);
    }

    public function test_price_must_be_numeric_to_create_dish(): void
    {
        $this->data['price'] = 'text';

        $response = $this->apiAs(
            $this->user,
            'post',
            "$this->apiBase/{$this->restaurant->id}/dishes",
            $this->data
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['price']]);
        $this->assertDatabaseMissing('dishes', [
            'restaurant_id' => $this->restaurant->id,
            'name' => $this->data['name'],
        ]);
    }

    public function test_price_must_be_greater_than_zero_to_create_dish(): void
    {
        $this->data['price'] = '0.00';

        $response = $this->apiAs(
            $this->user,
            'post',
            "$this->apiBase/{$this->restaurant->id}/dishes",
            $this->data
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['price']]);
        $this->assertDatabaseMissing('dishes', [
            'restaurant_id' => $this->restaurant->id,
            'name' => $this->data['name'],
        ]);
    }

    public function test_price_must_be_decimal_positives_to_create_dish(): void
    {
        $this->data['price'] = '-0.01';
        $response = $this->apiAs(
            $this->user,
            'post',
            "$this->apiBase/{$this->restaurant->id}/dishes",
            $this->data
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['price']]);
        $this->assertDatabaseMissing('dishes', [
            'restaurant_id' => $this->restaurant->id,
            'name' => $this->data['name'],
        ]);
    }

    public function test_is_available_must_be_boolean_to_create_dish(): void
    {
        $this->data['isAvailable'] = 'text';

        $response = $this->apiAs(
            $this->user,
            'post',
            "$this->apiBase/{$this->restaurant->id}/dishes",
            $this->data
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['isAvailable']]);
        $this->assertDatabaseMissing('dishes', [
            'restaurant_id' => $this->restaurant->id,
            'name' => $this->data['name'],
        ]);
    }

}
