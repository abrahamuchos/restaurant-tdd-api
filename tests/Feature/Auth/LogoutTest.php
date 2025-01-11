<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;
    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['email' => 'abraham@mail.com']);
    }

    public function test_unauthenticated_user_cannot_logout()
    {
        $response = $this->get("{$this->apiBase}/logout");

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_logout()
    {
        $response = $this->apiAs(
          $this->user,
          'get',
          "{$this->apiBase}/logout"
        );

        $response->assertStatus(204);
    }

}
