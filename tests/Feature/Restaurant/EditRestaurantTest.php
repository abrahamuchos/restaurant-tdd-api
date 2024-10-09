<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditRestaurantTest extends TestCase
{
    use RefreshDatabase;

    private array $data = [
        'userId' => null,
        'code' => 'R302',
        'name' => 'Restaurant Test Edit',
        'description' => 'Description was updated',
        'address' => 'Address was updated',
        'phone' => '1234567890',
        'email' => 'restaurant@test.com',
        'website' => 'https://restaurant.test.com',
        'openingHour' => '08:00',
        'closingHour' => '22:00',
        'logo' => null,
        'image' => null,
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->restaurant = Restaurant::factory()->create([
            'name' => 'Restaurant Test',
            'description' => 'Restaurant to be edited',
        ]);
        $this->data['userId'] = $this->restaurant->user_id;
    }

    public function test_put_restaurant(): void
    {
        $user = User::find($this->restaurant->user_id);

        $response = $this->apiAs($user, 'put', "$this->apiBase/restaurants/{$this->restaurant->id}", $this->data);

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
        $this->assertDatabaseHas('restaurants', [
            'name' => $this->data['name'],
            'description' => $this->data['description']
        ]);
    }

    public function test_patch_restaurant(): void
    {
        $user = User::find($this->restaurant->user_id);

        $response = $this->apiAs(
            $user,
            'patch',
            "$this->apiBase/restaurants/{$this->restaurant->id}",
            [
                'name' => $this->data['name'],
                'description' => $this->data['description']
            ]
        );

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
        $this->assertDatabaseHas(
            'restaurants',
            [
                'name' => $this->data['name'],
                'description' => $this->data['description']
            ]
        );
    }

    public function test_unauthenticated_user_cannot_edit_restaurant(): void
    {
        $response = $this->patchJson("$this->apiBase/restaurants/{$this->restaurant->id}", $this->data);

        $response->assertStatus(401);
    }

    public function test_user_cannot_edit_other_user_restaurant(): void
    {
        $user = User::factory()->create();
        $response = $this->apiAs($user, 'patch', "$this->apiBase/restaurants/{$this->restaurant->id}", $this->data);

        $response->assertStatus(403);
    }

    public function test_put_user_id_must_be_required(): void
    {
        $this->data['userId'] = null;
        $user = User::find($this->restaurant->user_id);

        $response = $this->apiAs($user, 'put', "$this->apiBase/restaurants/{$this->restaurant->id}", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['userId']]);
    }

    public function test_user_id_must_be_exists(): void
    {
        $this->data['userId'] = 2000000;
        $user = User::find($this->restaurant->user_id);

        $response = $this->apiAs($user, 'put', "$this->apiBase/restaurants/{$this->restaurant->id}", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['userId']]);

    }

    public function test_code_must_be_required(): void
    {
        $this->data['code'] = null;
        $user = User::find($this->restaurant->user_id);

        $response = $this->apiAs($user, 'put', "$this->apiBase/restaurants/{$this->restaurant->id}", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['code']]);
    }

    public function test_name_must_be_required(): void
    {
        $this->data['name'] = null;
        $user = User::find($this->restaurant->user_id);

        $response = $this->apiAs($user, 'put', "$this->apiBase/restaurants/{$this->restaurant->id}", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['name']]);
    }

    public function test_name_must_be_string(): void
    {
        $this->data['name'] = 1234;
        $user = User::find($this->restaurant->user_id);

        $response = $this->apiAs($user, 'put', "$this->apiBase/restaurants/{$this->restaurant->id}", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['name']]);
    }

    public function test_name_must_be_max_65_characters(): void
    {
        $this->data['name'] = \Str::random(66);
        $user = User::find($this->restaurant->user_id);

        $response = $this->apiAs($user, 'put', "$this->apiBase/restaurants/{$this->restaurant->id}", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['name']]);
    }

    public function test_description_must_be_required(): void
    {
        $this->data['description'] = null;
        $user = User::find($this->restaurant->user_id);

        $response = $this->apiAs($user, 'put', "$this->apiBase/restaurants/{$this->restaurant->id}", $this->data);
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['description']]);
    }

    public function test_description_must_be_string(): void
    {
        $this->data['description'] = 1234567;
        $user = User::find($this->restaurant->user_id);

        $response = $this->apiAs($user, 'put', "$this->apiBase/restaurants/{$this->restaurant->id}", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['description']]);
    }

    public function test_description_must_be_max_255_characters(): void
    {
        $this->data['description'] = \Str::random(256);
        $user = User::find($this->restaurant->user_id);

        $response = $this->apiAs($user, 'put', "$this->apiBase/restaurants/{$this->restaurant->id}", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['description']]);
    }

    public function test_openingHour_must_be_required(): void
    {
        $this->data['openingHour'] = null;
        $user = User::find($this->restaurant->user_id);

        $response = $this->apiAs($user, 'put', "$this->apiBase/restaurants/{$this->restaurant->id}", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['openingHour']]);
    }

    public function test_openingHour_must_be_date_format(): void
    {
        $this->data['openingHour'] = '1234567';
        $user = User::find($this->restaurant->user_id);

        $response = $this->apiAs($user, 'put', "$this->apiBase/restaurants/{$this->restaurant->id}", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['openingHour']]);
    }

    public function test_closingHour_must_be_required(): void
    {
        $this->data['closingHour'] = null;
        $user = User::find($this->restaurant->user_id);

        $response = $this->apiAs($user, 'put', "$this->apiBase/restaurants/{$this->restaurant->id}", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['closingHour']]);
    }

    public function test_closingHour_must_be_date_format(): void
    {
        $this->data['closingHour'] = '1234567';
        $user = User::find($this->restaurant->user_id);

        $response = $this->apiAs($user, 'put', "$this->apiBase/restaurants/{$this->restaurant->id}", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['closingHour']]);
    }
}



