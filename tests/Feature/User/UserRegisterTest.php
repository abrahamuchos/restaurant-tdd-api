<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserRegisterTest extends TestCase
{
    use RefreshDatabase;

    private array $data = [
        'email' => 'test@test.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'name' => 'Test User',
        'lastName' => 'Last Test User',
    ];

    public function test_user_can_register(): void
    {
        $this->withoutExceptionHandling();
        $response = $this->postJson("{$this->apiBase}/register", $this->data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => $this->data['email'], 'name' => $this->data['name'] ]);
    }

    public function test_email_must_be_required()
    {
        $this->data['email'] = '';

        $response = $this->postJson("{$this->apiBase}/register", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['email' => []]]);

    }

    public function test_email_must_be_unique(): void
    {
        User::factory()->create(['email' => $this->data['email']]);

        $response = $this->postJson("{$this->apiBase}/register", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['email' => []]]);
    }

    public function test_email_must_be_valid(): void
    {
        $this->data['email'] = 'invalid-email';

        $response = $this->postJson("{$this->apiBase}/register", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['email' => []]]);
    }

    public function test_password_must_be_confirmed(): void
    {
        $this->data['password_confirmation'] = 'different-password';

        $response = $this->postJson("{$this->apiBase}/register", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['password' => []]]);
    }

    public function test_password_must_be_required(): void
    {
        $this->data['password'] = '';

        $response = $this->postJson("{$this->apiBase}/register", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['password' => []]]);
    }

    public function test_password_must_be_min_8_chars(): void
    {
        $this->data['password'] = '1234567';

        $response = $this->postJson("{$this->apiBase}/register", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['password' => []]]);
    }

    public function test_name_must_be_required(): void
    {
        $this->data['name'] = '';

        $response = $this->postJson("{$this->apiBase}/register", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['name' => []]]);
    }

    public function test_name_mus_be_max_65_chars(): void
    {
        $this->data['name'] = '1234567890123456789012345678901234567890123456789012345678901234567890';

        $response = $this->postJson("{$this->apiBase}/register", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['name' => []]]);
    }

    public function test_name_must_be_string(): void
    {
        $this->data['name'] = 123;

        $response = $this->postJson("{$this->apiBase}/register", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['name' => []]]);
    }

    public function test_last_name_must_be_required(): void
    {
        $this->data['lastName'] = '';

        $response = $this->postJson("{$this->apiBase}/register", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['lastName' => []]]);
    }

    public function test_last_name_mus_be_max_65_chars(): void
    {
        $this->data['lastName'] = '1234567890123456789012345678901234567890123456789012345678901234567890';

        $response = $this->postJson("{$this->apiBase}/register", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['lastName' => []]]);
    }

    public function test_last_name_must_be_string(): void
    {
        $this->data['lastName'] = 123;

        $response = $this->postJson("{$this->apiBase}/register", $this->data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['lastName' => []]]);
    }
}
