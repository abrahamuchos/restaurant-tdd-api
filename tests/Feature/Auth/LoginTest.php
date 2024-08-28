<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     * @return void
     */
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

    public function test_email_must_be_required()
    {
        $credentials = [
            'email' => null,
            'password' => 'password'
        ];
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['email']]);
    }

    public function test_email_must_be_valid()
    {
        $credentials = [
            'email' => 'not-an-email',
            'password' => 'password'
        ];

        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['email']]);
    }

    public function test_password_must_be_required()
    {
        $credentials = [
            'email' => 'abraham@mail.com',
            'password' => null
        ];
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['password']]);
    }

    public function test_password_must_be_string()
    {
        $credentials = [
            'email' => 'abraham@mail.com',
            'password' => 123456
        ];
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['password']]);
    }
}
