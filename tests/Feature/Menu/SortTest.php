<?php

namespace Tests\Feature\Menu;

use App\Models\Menu;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SortTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Restaurant $restaurant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->restaurant = Restaurant::factory()->create();
        $this->user = $this->restaurant->user;

        Menu::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'B name',
            'description' => 'B description'
        ]);
        Menu::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'D name',
            'description' => 'D description'
        ]);
        Menu::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'C name',
            'description' => 'C description'
        ]);
        Menu::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'A name',
            'description' => 'A description'
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
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus",
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
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus",
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
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus",
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
            "$this->apiBase/restaurants/{$this->restaurant->id}/menus",
            $data
        );

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.description', 'D description');
        $response->assertJsonPath('data.1.description', 'C description');
        $response->assertJsonPath('data.2.description', 'B description');
        $response->assertJsonPath('data.3.description', 'A description');
    }
}
