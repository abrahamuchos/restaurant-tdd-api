<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        User::factory()->create(['email' => 'abraham@mail.com']);
    }

    public function test_user_can_login(): void
    {
        $credentials = [
            'email' => 'abraham@mail.com',
            'password' => 'password',
        ];

        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['token', 'tokenType', 'expiresIn']]);
    }

    public function test_non_existing_user_cannot_login()
    {
        $credentials = [
            'email' => 'not.exists.user@mail.com',
            'password' => 'password',
        ];

        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        $response->assertUnauthorized();
    }

    public function test_email_required()
    {
        $credentials = [
            'email' => null,
            'password' => 'password'
        ];
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        $response->assertStatus(422);
    }

    public function test_password_required()
    {
        $credentials = [
            'email' => 'abraham@mail.com',
            'password' => null
        ];
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        $response->assertStatus(422);
    }
}
