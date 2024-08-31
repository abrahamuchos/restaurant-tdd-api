<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    protected string $apiBase = "/api/v1";

    /**
     * @param User|\Illuminate\Database\Eloquent\   $user
     * @param string $method
     * @param string $uri
     * @param array  $data
     *
     * @return TestResponse
     */
    protected function apiAs(User|\Eloquent $user, string $method, string $uri, array $data = []): \Illuminate\Testing\TestResponse
    {
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . JWTAuth::fromUser($user),
        ];

        return $this->json($method, $uri, $data, $headers);
    }

}
