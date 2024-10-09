<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateRestaurantTest extends TestCase
{
    use RefreshDatabase;

    private array $data = [
        'userId' => 1,
        'name' => 'Restaurant Test',
        'code' => 'RT001',
        'description' => 'Description Test',
        'address' => 'Address Test',
        'phone' => '123456789',
        'email' => 'test@test.com',
        'website' => 'https://www.restaruant-test.com',
        'openingHour' => '08:00',
        'closingHour' => '22:00',
        'image' => null,
        'logo' => null
    ];

    public function setUp(): void
    {
        parent::setUp();
        User::factory()->create();
    }

    public function test_create_restaurant(): void
    {
        $user = User::find(1);

        $response = $this->apiAs($user, 'post', "$this->apiBase/restaurants", $this->data);


        $response->assertStatus(201);
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
            'code' => $this->data['code'],
            'description' => $this->data['description'],
            'address' => $this->data['address'],
            'phone' => $this->data['phone'],
            'email' => $this->data['email'],
            'website' => $this->data['website'],
            'opening_hour' => $this->data['openingHour'],
            'closing_hour' => $this->data['closingHour'],
            'image' => $this->data['image'],
            'logo' => $this->data['logo'],
            'user_id' => $this->data['userId']
        ]);
    }

    public function test_user_must_be_required(): void
    {
        $this->data['userId'] = null;
        $user = User::factory()->create();

        $response = $this->apiAs($user, 'post', "$this->apiBase/restaurants", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['userId']]);
    }

    public function test_name_must_be_required(): void
    {
        $this->data['name'] = null;
        $user = User::factory()->create();

        $response = $this->apiAs($user, 'post', "$this->apiBase/restaurants", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['name']]);
    }

    public function test_name_max_length_must_be_65(): void
    {
        $this->data['name'] = str_repeat('a', 66);
        $user = User::factory()->create();

        $response = $this->apiAs($user, 'post', "$this->apiBase/restaurants", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['name']]);

    }

    public function test_code_must_be_required(): void
    {
        $this->data['code'] = null;
        $user = User::factory()->create();

        $response = $this->apiAs($user, 'post', "$this->apiBase/restaurants", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['code']]);
    }

    public function test_code_must_be_unique(): void
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $this->data['code'] = $restaurant->code;

        $response = $this->apiAs($user, 'post', "$this->apiBase/restaurants", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['code']]);
    }

    public function test_address_must_be_required(): void
    {
        $this->data['address'] = null;
        $user = User::factory()->create();

        $response = $this->apiAs($user, 'post', "$this->apiBase/restaurants", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['address']]);
    }

    public function test_address_max_length_must_be_255(): void
    {
        $this->data['address'] = str_repeat('b', 256);
        $user = User::factory()->create();

        $response = $this->apiAs($user, 'post', "$this->apiBase/restaurants", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['address']]);
    }

    public function test_phone_must_be_required(): void
    {
        $this->data['phone'] = null;
        $user = User::factory()->create();

        $response = $this->apiAs($user, 'post', "$this->apiBase/restaurants", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['phone']]);
    }

    public function test_phone_max_length_must_be_65(): void
    {
        $this->data['phone'] = str_repeat('1', 66);
        $user = User::factory()->create();

        $response = $this->apiAs($user, 'post', "$this->apiBase/restaurants", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['phone']]);
    }

    public function test_email_must_be_required(): void
    {
        $this->data['email'] = null;
        $user = User::factory()->create();

        $response = $this->apiAs($user, 'post', "$this->apiBase/restaurants", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['email']]);
    }

    public function test_email_max_length_must_be_255(): void
    {
        $this->data['email'] = str_repeat('a', 256);
        $user = User::factory()->create();

        $response = $this->apiAs($user, 'post', "$this->apiBase/restaurants", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['email']]);
    }

    public function test_email_must_be_unique(): void
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $this->data['email'] = $restaurant->email;

        $response = $this->apiAs($user, 'post', "$this->apiBase/restaurants", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['email']]);
    }

    public function test_description_max_length_must_be_255(): void
    {
        $this->data['description'] = str_repeat('a', 256);
        $user = User::factory()->create();

        $response = $this->apiAs($user, 'post', "$this->apiBase/restaurants", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['description']]);
    }

    public function test_opening_hours_must_be_required(): void
    {
        $this->data['openingHour'] = null;
        $user = User::factory()->create();

        $response = $this->apiAs($user, 'post', "$this->apiBase/restaurants", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['openingHour']]);

    }

    public function test_opening_hours_must_be_time_format(): void
    {
        $this->data['openingHour'] = '12:00:00';
        $user = User::factory()->create();

        $response = $this->apiAs($user, 'post', "$this->apiBase/restaurants", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['openingHour']]);
    }
    public function test_closing_hours_must_be_required(): void
    {
        $this->data['closingHour'] = null;
        $user = User::factory()->create();

        $response = $this->apiAs($user, 'post', "$this->apiBase/restaurants", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['closingHour']]);

    }

    public function test_closing_hours_must_be_time_format(): void
    {
        $this->data['closingHour'] = '12:00:00';
        $user = User::factory()->create();

        $response = $this->apiAs($user, 'post', "$this->apiBase/restaurants", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['closingHour']]);
    }
}
