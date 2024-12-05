<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NotFoundTest extends TestCase
{
    public function test_not_found ()
    {
        $response = $this->get('/not-found');

        $response->assertStatus(404);
        $response->assertJsonStructure(['message', 'errors' => ['code']]);
    }
}
