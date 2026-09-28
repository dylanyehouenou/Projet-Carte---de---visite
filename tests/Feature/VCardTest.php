<?php

namespace Tests\Feature;

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_vcard_download_returns_vcf(): void
    {
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'is_active' => true, 'email' => 'alice@mmi-e.fr']);
        $response = $this->get(route('card.vcard', $emp->slug));
        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/vcard; charset=utf-8');
        $this->assertStringContainsString('BEGIN:VCARD', $response->getContent());
    }

    public function test_vcard_contains_present_fields(): void
    {
        $emp = Employee::create([
            'first_name' => 'Alice',
            'last_name'  => 'Martin',
            'is_active'  => true,
            'email'      => 'alice@mmi-e.fr',
            'phone'      => '0600000001',
        ]);
        $content = $this->get(route('card.vcard', $emp->slug))->getContent();
        $this->assertStringContainsString('EMAIL', $content);
        $this->assertStringContainsString('TEL', $content);
    }

    public function test_vcard_has_no_empty_fields(): void
    {
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'is_active' => true]);
        $content = $this->get(route('card.vcard', $emp->slug))->getContent();
        $this->assertStringNotContainsString('EMAIL', $content);
        $this->assertStringNotContainsString('TEL', $content);
        $this->assertStringNotContainsString('URL;TYPE=LinkedIn', $content);
    }

    public function test_inactive_card_vcard_returns_404(): void
    {
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'is_active' => false]);
        $this->get(route('card.vcard', $emp->slug))->assertNotFound();
    }
}
