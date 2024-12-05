<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_not_access_when_not_authenticated_json_request()
    {
        $response = $this->getJson("$this->apiBase/restaurants");

        $response->assertStatus(401);
        $response->assertJsonStructure(['message', 'errors' => ['code']]);
    }

    public function test_user_can_not_access_when_not_authenticated_not_json_request()
    {
        $response = $this->get(route('restaurants.index'));

        $response->assertStatus(401);
        $response->assertJsonStructure(['message', 'errors' => ['code']]);
    }
}
