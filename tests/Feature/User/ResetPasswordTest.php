<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    private string $token = '';

    public function setUp(): void
    {
        parent::setUp();
        User::factory()->create(['email' => 'abraham@mail.com']);
    }

    /**
     * @throws \Exception
     */
    public function test_send_reset_password_notification(): void
    {
        Notification::fake();
        $data = ['email' => 'abraham@mail.com'];

        $response = $this->postJson("$this->apiBase/reset-password", $data);

        $response->assertOk();
        $response->assertJsonStructure(['message']);
        Notification::assertSentTo(User::first(), ResetPasswordNotification::class);
    }

    public function test_send_reset_password_notification_with_invalid_email(): void
    {
        $data = ['email' => 'invalid-email@mail.com.br'];

        $response = $this->postJson("$this->apiBase/reset-password", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['email']]);
    }

    /**
     * @throws \Exception
     */
    public function test_can_reset_password_with_valid_token(): void
    {
        Notification::fake();
        $user = User::find(1);
        $data = [
            'password' => 'new-password',
            'passwordConfirmation' => 'new-password',
            'email' => $user->email,
        ];

        $this->postJson("$this->apiBase/reset-password", $data);
        Notification::assertSentTo($user, function (ResetPasswordNotification $notification) {
            $urlParts = parse_url($notification->url);
            parse_str($urlParts['query'], $queryParams);
            $this->token = $queryParams['token'];

            return str_contains($notification->url, env('APP_URL_FRONTEND') . '?token=');
        });
        $response = $this->patchJson("$this->apiBase/reset-password?token=$this->token", $data);

        $response->assertStatus(204);
        $this->assertTrue(Hash::check($data['password'], $user->fresh()->password));
    }

    public function test_can_not_reset_password_with_invalid_token(): void
    {
        $user = User::find(1);
        $data = [
            'password' => 'new-password',
            'passwordConfirmation' => 'new-password',
            'email' => $user->email,
        ];
        $failedToken = 'invalid-token';
        $this->postJson("$this->apiBase/reset-password", $data);

        $response = $this->patchJson("$this->apiBase/reset-password?token=$failedToken", $data);

        $response->assertStatus(403);
        $response->assertJsonStructure(['error', 'message', 'code', 'details']);
    }

    public function test_password_must_be_required()
    {
        $user = User::find(1);
        $this->token = 'faker-token';
        $data = [
            'password' => '',
            'passwordConfirmation' => 'new-password',
            'email' => $user->email,
        ];

        $response = $this->patchJson("$this->apiBase/reset-password?token=$this->token", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['password']]);
    }

    public function test_passwordConfirmation_must_be_required(): void
    {
        $user = User::find(1);
        $this->token = 'faker-token';
        $data = [
            'password' => 'new-password',
            'passwordConfirmation' => '',
            'email' => $user->email,
        ];

        $response = $this->patchJson("$this->apiBase/reset-password?token=$this->token", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['password']]);
    }

    public function test_password_and_password_confirmation_must_be_equals(): void
    {
        $user = User::find(1);
        $this->token = 'faker-token';
        $data = [
            'password' => 'new-password',
            'passwordConfirmation' => 'other-password',
            'email' => $user->email,
        ];

        $response = $this->patchJson("$this->apiBase/reset-password?token=$this->token", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['password']]);
    }

    public function test_password_must_be_at_least_8_characters(): void
    {
        $user = User::find(1);
        $this->token = 'faker-token';
        $data = [
            'password' => '1234567',
            'passwordConfirmation' => '1234567',
            'email' => $user->email,
        ];

        $response = $this->patchJson("$this->apiBase/reset-password?token=$this->token", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['password']]);
    }

    public function test_token_must_be_required(): void
    {
        $user = User::find(1);
        $data = [
            'password' => 'new-password',
            'passwordConfirmation' => 'other-password',
            'email' => $user->email,
        ];

        $response = $this->patchJson("$this->apiBase/reset-password", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['token']]);
    }

    public function test_email_must_be_required(): void
    {
        $this->token = 'faker-token';
        $data = [
            'password' => 'new-password',
            'passwordConfirmation' => 'new-password',
            'email' => '',
        ];

        $response = $this->patchJson("$this->apiBase/reset-password?token=$this->token", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['email']]);
    }
}
