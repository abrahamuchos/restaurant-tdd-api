<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UpdateUserTest extends TestCase
{
    use RefreshDatabase;

    private array $data = [
        'name' => 'Abraham',
        'lastName' => 'Gonzalez',
    ];

    public function setUp(): void
    {
        parent::setUp();
        User::factory()->create(['email' => 'abraham@mail.com']);
    }

    public function test_an_unauthenticated_user_cannot_update_a_user(): void
    {
        $response = $this->patchJson("$this->apiBase/profile", $this->data);

        $response->assertUnauthorized();
    }

    public function test_user_can_be_update_their_data(): void
    {
        $user = User::find(1);

        $response = $this->apiAs($user, 'patch', "$this->apiBase/profile", $this->data);

        $response->assertStatus(204);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $this->data['name'],
            'last_name' => $this->data['lastName'],
        ]);
    }

    public function test_user_cannot_update_their_password(): void
    {
        $user = User::find(1);
        $data['password'] = 'new-password';

        $response = $this->apiAs($user, 'patch', "$this->apiBase/profile", $this->data);

        $response->assertStatus(204);
        $this->assertFalse(Hash::make($data['password']) === $user->fresh()->password);
    }

    public function test_user_cannot_update_their_email(): void
    {
        $user = User::find(1);
        $data['email'] = 'new-email@mail.com';

        $response = $this->apiAs($user, 'patch', "$this->apiBase/profile", $this->data);

        $response->assertStatus(204);
        $this->assertFalse($user->fresh()->email === $data['email']);
    }

    public function test_name_must_be_required(): void
    {
        $data['name'] = '';
        $user = User::find(1);

        $response = $this->apiAs($user, 'patch', "$this->apiBase/profile", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['name']]);
    }

    public function test_last_name_must_be_required(): void
    {
        $data['lastName'] = '';
        $user = User::find(1);

        $response = $this->apiAs($user, 'patch', "$this->apiBase/profile", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['lastName']]);
    }

}
