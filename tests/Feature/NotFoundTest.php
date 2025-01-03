<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotFoundTest extends TestCase
{
    use RefreshDatabase;

    public function test_not_found()
    {
        $response = $this->get('/not-found');

        $response->assertStatus(404);
        $response->assertJsonStructure(['message', 'errors' => ['code']]);
    }
}
