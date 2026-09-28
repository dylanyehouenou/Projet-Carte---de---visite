<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    public function test_slug_is_generated_on_create(): void
    {
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin']);
        $this->assertNotEmpty($emp->slug);
        $this->assertEquals('alice-martin', $emp->slug);
    }

    public function test_qr_token_is_generated_on_create(): void
    {
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin']);
        $this->assertNotEmpty($emp->qr_token);
    }

    public function test_slug_is_unique_for_homonyms(): void
    {
        $emp1 = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin']);
        $emp2 = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin']);
        $this->assertNotEquals($emp1->slug, $emp2->slug);
    }

    public function test_slug_is_stable_after_update(): void
    {
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin']);
        $originalSlug = $emp->slug;
        $emp->update(['job_title' => 'Directrice', 'email' => 'alice@mmi-e.fr']);
        $this->assertEquals($originalSlug, $emp->fresh()->slug);
    }

    public function test_qr_token_is_stable_after_update(): void
    {
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin']);
        $originalToken = $emp->qr_token;
        $emp->update(['phone' => '0600000001', 'department' => 'RH']);
        $this->assertEquals($originalToken, $emp->fresh()->qr_token);
    }

    public function test_admin_can_view_employee_list(): void
    {
        Employee::create(['first_name' => 'Bob', 'last_name' => 'Dupont']);
        $this->actingAs($this->admin)
            ->get(route('admin.employees.index'))
            ->assertOk()
            ->assertSee('DUPONT');
    }

    public function test_admin_can_update_employee(): void
    {
        $emp = Employee::create(['first_name' => 'Bob', 'last_name' => 'Dupont']);
        $this->actingAs($this->admin)
            ->put(route('admin.employees.update', $emp), [
                'first_name' => 'Bob',
                'last_name'  => 'Dupont',
                'job_title'  => 'Responsable',
            ])
            ->assertRedirect(route('admin.employees.show', $emp));
        $this->assertEquals('Responsable', $emp->fresh()->job_title);
    }

    public function test_admin_can_toggle_active(): void
    {
        $emp = Employee::create(['first_name' => 'Bob', 'last_name' => 'Dupont', 'is_active' => true]);
        $this->actingAs($this->admin)
            ->post(route('admin.employees.toggle', $emp))
            ->assertRedirect();
        $this->assertFalse($emp->fresh()->is_active);
    }
}
