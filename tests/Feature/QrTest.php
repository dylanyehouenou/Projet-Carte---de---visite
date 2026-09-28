<?php

namespace Tests\Feature;

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrTest extends TestCase
{
    use RefreshDatabase;

    public function test_qr_endpoint_returns_image(): void
    {
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin']);
        $this->get(route('card.qr', $emp->slug))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');
    }

    public function test_qr_is_stable_after_employee_update(): void
    {
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin']);
        $token1 = $emp->qr_token;
        $emp->update(['job_title' => 'Directrice', 'phone' => '0600000001']);
        $this->assertEquals($token1, $emp->fresh()->qr_token);
    }
}
