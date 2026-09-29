<?php

namespace Tests\Feature;

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrTest extends TestCase
{
    use RefreshDatabase;

    public function test_qr_endpoint_returns_png_image(): void
    {
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin']);
        $response = $this->get(route('card.qr', $emp->slug));
        $response->assertOk()->assertHeader('Content-Type', 'image/png');
    }

    public function test_qr_png_is_a_valid_image_with_real_dimensions(): void
    {
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin']);
        $png = $this->get(route('card.qr', $emp->slug))->getContent();

        // Must be a valid PNG (correct magic bytes)
        $this->assertEquals("\x89PNG\r\n\x1a\n", substr($png, 0, 8), 'Response is not a valid PNG');

        // Must be a decodable image with reasonable dimensions (not a 1x1 error stub)
        $info = getimagesizefromstring($png);
        $this->assertNotFalse($info, 'PNG could not be decoded as an image');
        $this->assertEquals('image/png', $info['mime']);
        $this->assertGreaterThan(50, $info[0], 'QR image width is too small');
        $this->assertGreaterThan(50, $info[1], 'QR image height is too small');
    }

    public function test_qr_is_stable_after_employee_update(): void
    {
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin']);
        $token1 = $emp->qr_token;
        $emp->update(['job_title' => 'Directrice', 'phone' => '0600000001']);
        $this->assertEquals($token1, $emp->fresh()->qr_token);
    }

    public function test_qr_url_matches_public_card_url(): void
    {
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin']);

        // The QR code must encode the canonical public URL
        $this->assertEquals(url('/' . $emp->slug), $emp->publicUrl());
    }

    public function test_full_stability_scenario(): void
    {
        // 1. Créer un collaborateur
        $emp = Employee::create([
            'first_name' => 'Marie',
            'last_name'  => 'Dupont',
            'email'      => 'marie@mmi-e.fr',
        ]);

        // 2. Capturer les identifiants stables
        $slugBefore  = $emp->slug;
        $tokenBefore = $emp->qr_token;
        $urlBefore   = $emp->publicUrl();

        $this->assertNotEmpty($slugBefore);
        $this->assertNotEmpty($tokenBefore);

        // 3. Modifier plusieurs champs (simulation import CSV + modification manuelle)
        $emp->update([
            'phone'      => '0600000001',
            'email'      => 'marie.dupont@mmi-e.fr',
            'job_title'  => 'Directrice',
            'department' => 'Direction',
        ]);

        $emp->refresh();

        // 4. Vérifier la stabilité complète
        $this->assertEquals($slugBefore,  $emp->slug,      'slug changed after update');
        $this->assertEquals($tokenBefore, $emp->qr_token,  'qr_token changed after update');
        $this->assertEquals($urlBefore,   $emp->publicUrl(), 'public URL changed after update');

        // 5. La carte publique répond toujours à la même URL
        $this->get($urlBefore)->assertOk();

        // 6. Le QR est accessible et valide
        $png = $this->get(route('card.qr', $emp->slug))->getContent();
        $this->assertEquals("\x89PNG\r\n\x1a\n", substr($png, 0, 8));
    }
}
