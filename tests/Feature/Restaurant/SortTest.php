<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SortTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        Restaurant::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'A name',
            'description' => 'A description'
        ]);
        Restaurant::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'C name',
            'description' => 'C description'
        ]);
        Restaurant::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'B name',
            'description' => 'B description'
        ]);
        Restaurant::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'D name',
            'description' => 'D description'
        ]);
    }

    public function test_sort_by_name_asc()
    {
        $data = [
            'sortBy' => 'name',
            'sortDirection' => 'asc'
        ];
        $response = $this->apiAs(
            $this->user,
            'GET',
            "$this->apiBase/restaurants",
            $data
        );

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.name', 'A name');
        $response->assertJsonPath('data.1.name', 'B name');
        $response->assertJsonPath('data.2.name', 'C name');
        $response->assertJsonPath('data.3.name', 'D name');

    }

    public function test_sort_by_name_desc()
    {
        $data = [
            'sortBy' => 'name',
            'sortDirection' => 'desc'
        ];
        $response = $this->apiAs(
            $this->user,
            'GET',
            "$this->apiBase/restaurants",
            $data
        );

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.name', 'D name');
        $response->assertJsonPath('data.1.name', 'C name');
        $response->assertJsonPath('data.2.name', 'B name');
        $response->assertJsonPath('data.3.name', 'A name');
    }

    public function test_sort_by_description_asc()
    {
        $data = [
            'sortBy' => 'description',
            'sortDirection' => 'asc'
        ];
        $response = $this->apiAs(
            $this->user,
            'GET',
            "$this->apiBase/restaurants",
            $data
        );

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.description', 'A description');
        $response->assertJsonPath('data.1.description', 'B description');
        $response->assertJsonPath('data.2.description', 'C description');
        $response->assertJsonPath('data.3.description', 'D description');
    }

    public function test_sort_by_description_desc()
    {
        $data = [
            'sortBy' => 'description',
            'sortDirection' => 'desc'
        ];
        $response = $this->apiAs(
            $this->user,
            'GET',
            "$this->apiBase/restaurants",
            $data
        );

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.description', 'D description');
        $response->assertJsonPath('data.1.description', 'C description');
        $response->assertJsonPath('data.2.description', 'B description');
        $response->assertJsonPath('data.3.description', 'A description');
    }

}
