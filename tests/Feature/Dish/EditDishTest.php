<?php

namespace Tests\Feature\Dish;

use App\Models\Dish;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditDishTest extends TestCase
{
    use RefreshDatabase;

    protected Dish $dish;
    protected Restaurant $restaurant;
    protected \App\Models\User $user;
    protected array $data;

    public function setUp(): void
    {
        parent::setUp();

        $this->dish = Dish::factory()->create();
        $this->restaurant = $this->dish->restaurant;
        $this->user = $this->restaurant->user;
        $this->data = [
            'restaurantId' => $this->restaurant->id,
            'name' => 'Test Dish Updated',
            'description' => 'Test Description was updated !!',
            'price' => '12.99',
            'isAvailable' => 1,
        ];
    }

    public function test_authenticate_user_can_put_for_their_dish(): void
    {
        $response = $this->apiAs(
            $this->user,
            'put',
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes/{$this->dish->id}",
            $this->data
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('dishes', [
            'restaurant_id' => $this->restaurant->id,
            'name' => $this->data['name'],
            'description' => $this->data['description'],
            'price' => $this->data['price'],
            'is_available' => $this->data['isAvailable'],
        ]);
    }

    public function test_authenticate_user_can_patch_for_their_dish(): void
    {
        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes/{$this->dish->id}",
            [
                'name' => $this->data['name'],
                'price' => $this->data['price'],
            ]
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('dishes', [
            'name' => $this->data['name'],
            'price' => $this->data['price'],
        ]);
    }

    public function test_unauthenticated_user_cannot_update_for_their_dish(): void
    {
        $response = $this->putJson(
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes/{$this->dish->id}",
            $this->data
        );

        $response->assertStatus(401);
    }

    public function test_authenticated_user_cannot_update_for_another_user_dish(): void
    {
        $otherUser = \App\Models\User::factory()->create();

        $response = $this->apiAs(
            $otherUser,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes/{$this->dish->id}",
            [
                'name' => $this->data['name'],
                'price' => $this->data['price'],
            ]
        );

        $response->assertStatus(403);
        $this->assertDatabaseMissing('dishes', [
            'name' => $this->data['name'],
            'price' => $this->data['price'],
        ]);
    }

    public function test_restaurant_must_exist_to_update_dish(): void
    {
        $nonExistentRestaurantId = 999;

        $response = $this->apiAs(
            $this->user,
            'put',
            "$this->apiBase/$nonExistentRestaurantId/dishes/{$this->dish->id}",
            $this->data
        );

        $response->assertStatus(404);
        $this->assertDatabaseMissing('dishes', [
            'name' => $this->data['name'],
            'price' => $this->data['price'],
        ]);
    }


    public function test_restaurant_id_must_be_required_to_updated_dish(): void
    {
        $this->data['restaurantId'] = null;

        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes/{$this->dish->id}",
            [
                'name' => $this->data['name'],
                'price' => $this->data['price'],
                'restaurantId' => $this->data['restaurantId'],
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['restaurantId']]);
        $this->assertDatabaseMissing('dishes', [
            'name' => $this->data['name'],
            'price' => $this->data['price'],
        ]);
    }

    public function test_name_must_be_required_to_update_dish(): void
    {
        $this->data['name'] = null;

        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes/{$this->dish->id}",
            [
                'name' => $this->data['name'],
                'price' => $this->data['price'],
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['name']]);
        $this->assertDatabaseMissing('dishes', [
            'name' => $this->data['name'],
            'price' => $this->data['price'],
        ]);
    }

    public function test_name_must_be_max_65_characters_to_create_dish(): void
    {
        $this->data['name'] = \Str::random(66);

        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes/{$this->dish->id}",
            [
                'name' => $this->data['name'],
                'price' => $this->data['price'],
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['name']]);
        $this->assertDatabaseMissing('dishes', [
            'name' => $this->data['name'],
            'price' => $this->data['price'],
        ]);
    }

    public function test_name_must_be_string_to_create_dish(): void
    {
        $this->data['name'] = 3478346;

        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes/{$this->dish->id}",
            [
                'name' => $this->data['name'],
                'price' => $this->data['price'],
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['name']]);
        $this->assertDatabaseMissing('dishes', [
            'name' => $this->data['name'],
            'price' => $this->data['price'],
        ]);
    }

    public function test_description_must_be_string_to_create_dish(): void
    {
        $this->data['description'] = 3478346;

        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes/{$this->dish->id}",
            [
                'description' => $this->data['description'],
                'price' => $this->data['price'],
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['description']]);
        $this->assertDatabaseMissing('dishes', [
            'description' => $this->data['description'],
            'price' => $this->data['price'],
        ]);
    }

    public function test_description_must_be_100_characters_to_create_dish(): void
    {
        $this->data['description'] = \Str::random(101);

        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes/{$this->dish->id}",
            [
                'description' => $this->data['description'],
                'price' => $this->data['price'],
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['description']]);
        $this->assertDatabaseMissing('dishes', [
            'description' => $this->data['description'],
            'price' => $this->data['price'],
        ]);
    }

    public function test_price_must_be_decimal_with_two_decimal_to_create_dish(): void
    {
        $this->data['price'] = '10.000';

        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes/{$this->dish->id}",
            [
                'description' => $this->data['description'],
                'price' => $this->data['price'],
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['price']]);
        $this->assertDatabaseMissing('dishes', [
            'description' => $this->data['description'],
            'price' => $this->data['price'],
        ]);
    }

    public function test_price_must_be_required_to_create_dish(): void
    {
        $this->data['price'] = null;

        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes/{$this->dish->id}",
            [
                'description' => $this->data['description'],
                'price' => $this->data['price'],
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['price']]);
        $this->assertDatabaseMissing('dishes', [
            'description' => $this->data['description'],
            'price' => $this->data['price'],
        ]);
    }

    public function test_price_must_be_numeric_to_create_dish(): void
    {
        $this->data['price'] = 'text';

        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes/{$this->dish->id}",
            [
                'description' => $this->data['description'],
                'price' => $this->data['price'],
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['price']]);
        $this->assertDatabaseMissing('dishes', [
            'description' => $this->data['description'],
            'price' => $this->data['price'],
        ]);
    }

    public function test_price_must_be_greater_than_zero_to_create_dish(): void
    {
        $this->data['price'] = '0.00';

        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes/{$this->dish->id}",
            [
                'description' => $this->data['description'],
                'price' => $this->data['price'],
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['price']]);
        $this->assertDatabaseMissing('dishes', [
            'description' => $this->data['description'],
            'price' => $this->data['price'],
        ]);
    }

    public function test_price_must_be_decimal_positives_to_create_dish(): void
    {
        $this->data['price'] = '-0.01';

        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes/{$this->dish->id}",
            [
                'description' => $this->data['description'],
                'price' => $this->data['price'],
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['price']]);
        $this->assertDatabaseMissing('dishes', [
            'description' => $this->data['description'],
            'price' => $this->data['price'],
        ]);
    }

    public function test_is_available_must_be_boolean_to_create_dish(): void
    {
        $this->data['isAvailable'] = 'text';

        $response = $this->apiAs(
            $this->user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}/dishes/{$this->dish->id}",
            [
                'name' => $this->data['name'],
                'isAvailable' => $this->data['isAvailable'],
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['isAvailable']]);
        $this->assertDatabaseMissing('dishes', [
            'name' => $this->data['name'],
            'isAvailable' => $this->data['isAvailable'],
        ]);
    }

}
