<?php

namespace Tests\Feature\Admin;

use App\Enums\Roles;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected User|Collection|Model $admin;
    protected Restaurant|Collection|Model $restaurant;


    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->assignRole(Roles::ADMIN);
        $this->restaurant = Restaurant::factory()->create();
        Restaurant::factory(10)->create();
    }


    public function test_admin_user_can_delete_any_restaurant() : void
    {
        $response = $this->apiAs(
            $this->admin,
            'delete',
            "$this->apiBase/restaurants/{$this->restaurant->id}"
        );

        $response->assertStatus(204);
        $this->assertDatabaseMissing('restaurants', ['id' => $this->restaurant->id]);
    }
}
