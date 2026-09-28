<?php

namespace Tests\Feature;

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_card_returns_200(): void
    {
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'is_active' => true]);
        $this->get(route('card.show', $emp->slug))->assertOk();
    }

    public function test_disabled_card_returns_410(): void
    {
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'is_active' => false]);
        $this->get(route('card.show', $emp->slug))->assertStatus(410);
    }

    public function test_unknown_slug_returns_404(): void
    {
        $this->get('/slug-inexistant')->assertNotFound();
    }

    public function test_card_shows_employee_name(): void
    {
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'is_active' => true]);
        $this->get(route('card.show', $emp->slug))->assertSee('Alice');
    }

    public function test_card_shows_no_empty_buttons(): void
    {
        $emp = Employee::create([
            'first_name' => 'Alice',
            'last_name'  => 'Martin',
            'is_active'  => true,
            // no email, phone, linkedin, calendly
        ]);
        $response = $this->get(route('card.show', $emp->slug));
        $response->assertDontSee('mailto:');
        $response->assertDontSee('tel:');
    }
}
