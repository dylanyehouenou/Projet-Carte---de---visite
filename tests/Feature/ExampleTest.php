<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_root_redirects_to_admin_or_login(): void
    {
        $this->get('/')->assertRedirect();
    }

    public function test_health_check_returns_200(): void
    {
        $this->get('/up')->assertOk();
    }
}
