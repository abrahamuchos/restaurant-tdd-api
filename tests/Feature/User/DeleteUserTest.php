<?php

namespace Tests\Feature\User;

use App\Enums\Roles;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteUserTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create()->assignRole(Roles::ADMIN);
        $this->user = User::factory()->create();
    }

    public function test_admin_can_delete_user()
    {
       $response = $this->apiAs(
           $this->admin,
           'delete',
           "$this->apiBase/user/{$this->user->id}"
       );

       $response->assertStatus(204);
       $this->assertDatabaseMissing('users', ['id' => $this->user->id]);
    }

    public function test_user_cannot_delete_user()
    {
        $otherUser = User::factory()->create();

        $response = $this->apiAs(
            $otherUser,
            'delete',
            "$this->apiBase/user/{$this->user->id}"
        );

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', ['id' => $this->user->id]);
    }

}
