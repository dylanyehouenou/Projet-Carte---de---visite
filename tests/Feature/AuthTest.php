<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('login.' . request()->ip());
    }

    public function test_login_form_is_accessible(): void
    {
        $this->get(route('admin.login'))->assertOk();
    }

    public function test_valid_admin_can_login(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->post(route('admin.login.post'), ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        User::factory()->create(['email' => 'admin@test.fr', 'role' => 'admin']);
        $this->post(route('admin.login.post'), ['email' => 'admin@test.fr', 'password' => 'wrong'])
            ->assertSessionHasErrors('email');
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'is_active' => false]);
        $this->post(route('admin.login.post'), ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
    }

    public function test_unauthenticated_user_is_redirected_from_admin(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_logout_works(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin)
            ->post(route('admin.logout'))
            ->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_brute_force_is_rate_limited(): void
    {
        // Make 10 failed login attempts (the limit)
        for ($i = 0; $i < 10; $i++) {
            $this->post(route('admin.login.post'), [
                'email'    => 'attacker@test.fr',
                'password' => 'wrong' . $i,
            ]);
        }

        // The 11th attempt must be throttled (429)
        $response = $this->post(route('admin.login.post'), [
            'email'    => 'attacker@test.fr',
            'password' => 'wrong11',
        ]);

        $response->assertStatus(429);
    }
}
