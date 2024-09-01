<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UpdatePasswordTest extends TestCase
{
    use RefreshDatabase;

    private array $data = [
        'currentPassword' => 'password',
        'newPassword' => '12345678',
        'newPasswordConfirmation' => '12345678'
    ];

    public function setUp(): void
    {
        parent::setUp();
        User::factory()->create();
    }


    public function test_an_unauthenticated_user_cannot_update_a_password(): void
    {
        $response = $this->patchJson("$this->apiBase/password", $this->data);

        $response->assertUnauthorized();
    }

    public function test_user_can_be_update_their_password(): void
    {
        $user = User::find(1);

        $response = $this->apiAs($user, 'patch', "$this->apiBase/password", $this->data);

        $response->assertStatus(204);
        $this->assertTrue(Hash::check($this->data['newPassword'], $user->fresh()->password));
    }

    public function test_new_password_must_be_require(): void
    {
        $data['newPassword'] = '';

        $response = $this->apiAs(User::find(1), 'patch', "$this->apiBase/password", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['newPassword'], 'message']);
    }

    public function test_new_password_must_be_confirmed(): void
    {
        $data['newPasswordConfirmation'] = '123456789';

        $response = $this->apiAs(User::find(1), 'patch', "$this->apiBase/password", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['newPassword'], 'message']);

    }

    public function test_current_password_must_be_correct(): void
    {
        $data['currentPassword'] = '123456789';

        $response = $this->apiAs(User::find(1), 'patch', "$this->apiBase/password", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['currentPassword'], 'message']);
    }




}
