<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginateRestaurantTest extends TestCase
{
    use RefreshDatabase;

    protected int $perPage;
    protected User|Collection|Model $user;
    protected Restaurant|Collection|Model $restaurant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->perPage = 15;
        $this->user = User::factory()->create();
        $this->restaurant = Restaurant::factory(150)->create([
            'user_id' => $this->user->id
        ]);
    }

    public function test_authenticated_user_can_paginate_their_restaurant()
    {
        $response = $this->apiAs($this->user, 'get', "$this->apiBase/restaurants");

        $response->assertStatus(200);
        $response->assertJsonCount($this->perPage, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
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
            ],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'links' => [
                    '*' => [
                        'url',
                        'label',
                        'active'
                    ]
                ],
                'path',
                'per_page',
                'to',
                'total'
            ],
            'links' => [
                'first',
                'last',
                'prev',
                'next'
            ]
        ]);
    }

    public function test_authenticated_user_can_paginate_with_filters()
    {
        $this->perPage = 5;

        $response = $this->apiAs($this->user, 'get', "$this->apiBase/restaurants?perPage=$this->perPage");

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
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
            ],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'links' => [
                    '*' => [
                        'url',
                        'label',
                        'active'
                    ]
                ],
                'path',
                'per_page',
                'to',
                'total'
            ],
            'links' => [
                'first',
                'last',
                'prev',
                'next'
            ]
        ]);
    }

    public function test_authenticated_user_can_paginate_with_change_page():void
    {
        $page = 2;
        $response = $this->apiAs($this->user, 'get', "$this->apiBase/restaurants?page=$page");

        $response->assertStatus(200);
        $response->assertJson([
            'meta' => [
                'current_page' => $page,
            ],
        ]);
    }

    public function test_unauthenticated_user_cannot_paginate_their_restaurant()
    {
        $response = $this->getJson("$this->apiBase/restaurants");

        $response->assertStatus(401);
    }

}
