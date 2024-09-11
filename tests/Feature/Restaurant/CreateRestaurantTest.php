<?php

namespace Tests\Feature\Restaurant;

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
}
