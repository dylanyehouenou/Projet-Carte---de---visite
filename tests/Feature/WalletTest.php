<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Services\Wallet\Apple\AppleWalletProvider;
use App\Services\Wallet\Apple\PassBuilder;
use App\Services\Wallet\Google\GoogleWalletProvider;
use App\Services\Wallet\Google\ObjectBuilder;
use App\Services\Wallet\WalletManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    // ── PassBuilder (pure data, no I/O) ───────────────────────────────────────

    public function test_pass_builder_includes_employee_name(): void
    {
        $emp  = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'is_active' => true]);
        $data = (new PassBuilder())->build($emp, 'pass.fr.mmi-e', 'TEAM123');

        $this->assertEquals('pass.fr.mmi-e', $data['passTypeIdentifier']);
        $this->assertEquals('TEAM123', $data['teamIdentifier']);
        $this->assertStringContainsString('ALICE', strtoupper($data['generic']['primaryFields'][0]['value']));
    }

    public function test_pass_builder_uses_qr_token_as_serial(): void
    {
        $emp  = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'is_active' => true]);
        $data = (new PassBuilder())->build($emp, 'pass.fr.mmi-e', 'TEAM123');

        $this->assertEquals($emp->qr_token, $data['serialNumber']);
    }

    public function test_pass_builder_serial_stable_after_employee_update(): void
    {
        $emp   = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'is_active' => true]);
        $token = $emp->qr_token;

        $emp->update(['job_title' => 'Directrice', 'phone' => '0600000001']);

        $data = (new PassBuilder())->build($emp->fresh(), 'pass.fr.mmi-e', 'TEAM123');
        $this->assertEquals($token, $data['serialNumber']);
    }

    public function test_pass_builder_barcode_uses_public_url(): void
    {
        $emp  = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'is_active' => true]);
        $data = (new PassBuilder())->build($emp, 'pass.fr.mmi-e', 'TEAM123');

        $this->assertEquals($emp->publicUrl(), $data['barcodes'][0]['message']);
        $this->assertEquals('PKBarcodeFormatQR', $data['barcodes'][0]['format']);
    }

    public function test_pass_builder_omits_empty_fields(): void
    {
        $emp  = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'is_active' => true]);
        $data = (new PassBuilder())->build($emp, 'pass.fr.mmi-e', 'TEAM123');

        $backKeys = array_column($data['generic']['backFields'], 'key');
        $this->assertNotContains('email', $backKeys);
        $this->assertNotContains('phone', $backKeys);
        $this->assertNotContains('linkedin', $backKeys);
    }

    public function test_pass_builder_includes_present_fields(): void
    {
        $emp  = Employee::create([
            'first_name'   => 'Alice',
            'last_name'    => 'Martin',
            'is_active'    => true,
            'email'        => 'alice@mmi-e.fr',
            'phone'        => '0600000001',
            'linkedin_url' => 'https://linkedin.com/in/alice',
        ]);
        $data = (new PassBuilder())->build($emp, 'pass.fr.mmi-e', 'TEAM123');

        $backKeys = array_column($data['generic']['backFields'], 'key');
        $this->assertContains('email',    $backKeys);
        $this->assertContains('phone',    $backKeys);
        $this->assertContains('linkedin', $backKeys);
    }

    // ── ObjectBuilder (pure data, no I/O) ─────────────────────────────────────

    public function test_object_builder_id_is_stable(): void
    {
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'is_active' => true]);
        $obj = (new ObjectBuilder())->build($emp, '3388000000022', 'mmie-carte-visite');

        $expectedId = '3388000000022.employee-' . $emp->id;
        $this->assertEquals($expectedId, $obj['id']);
    }

    public function test_object_builder_id_stable_after_update(): void
    {
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'is_active' => true]);
        $idBefore = (new ObjectBuilder())->build($emp, '3388000000022', 'mmie-carte-visite')['id'];

        $emp->update(['job_title' => 'Directrice', 'phone' => '0600000001']);

        $idAfter = (new ObjectBuilder())->build($emp->fresh(), '3388000000022', 'mmie-carte-visite')['id'];
        $this->assertEquals($idBefore, $idAfter);
    }

    public function test_object_builder_barcode_uses_public_url(): void
    {
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'is_active' => true]);
        $obj = (new ObjectBuilder())->build($emp, '3388000000022', 'mmie-carte-visite');

        $this->assertEquals($emp->publicUrl(), $obj['barcode']['value']);
        $this->assertEquals('QR_CODE', $obj['barcode']['type']);
    }

    public function test_object_builder_omits_empty_links(): void
    {
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'is_active' => true]);
        $obj = (new ObjectBuilder())->build($emp, '3388000000022', 'mmie-carte-visite');

        $ids = array_column($obj['linksModuleData']['uris'], 'id');
        $this->assertNotContains('email',    $ids);
        $this->assertNotContains('phone',    $ids);
        $this->assertNotContains('linkedin', $ids);
        $this->assertContains('card_url',    $ids);
    }

    public function test_object_builder_state_is_active(): void
    {
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'is_active' => true]);
        $obj = (new ObjectBuilder())->build($emp, '3388000000022', 'mmie-carte-visite');

        $this->assertEquals('ACTIVE', $obj['state']);
    }

    // ── WalletManager configuration ───────────────────────────────────────────

    public function test_apple_provider_not_configured_without_credentials(): void
    {
        Config::set('wallet.apple.team_id', '');
        Config::set('wallet.apple.pass_type_id', '');

        $provider = new AppleWalletProvider(new PassBuilder());
        $this->assertFalse($provider->isConfigured());
    }

    public function test_google_provider_not_configured_without_credentials(): void
    {
        Config::set('wallet.google.issuer_id', '');

        $provider = new GoogleWalletProvider(new ObjectBuilder());
        $this->assertFalse($provider->isConfigured());
    }

    public function test_wallet_manager_reports_unconfigured_state(): void
    {
        Config::set('wallet.apple.team_id', '');
        Config::set('wallet.google.issuer_id', '');

        $manager = app(WalletManager::class);
        $this->assertFalse($manager->isAppleConfigured());
        $this->assertFalse($manager->isGoogleConfigured());
    }

    // ── HTTP endpoints (unconfigured → 503) ───────────────────────────────────

    public function test_apple_wallet_endpoint_returns_503_when_not_configured(): void
    {
        Config::set('wallet.apple.team_id', '');
        Config::set('wallet.apple.pass_type_id', '');

        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'is_active' => true]);
        $this->get(route('card.apple-wallet', $emp->slug))->assertStatus(503);
    }

    public function test_google_wallet_endpoint_returns_503_when_not_configured(): void
    {
        Config::set('wallet.google.issuer_id', '');

        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'is_active' => true]);
        $this->get(route('card.google-wallet', $emp->slug))->assertStatus(503);
    }

    public function test_apple_wallet_returns_404_for_inactive_employee(): void
    {
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'is_active' => false]);
        $this->get(route('card.apple-wallet', $emp->slug))->assertNotFound();
    }

    public function test_google_wallet_returns_404_for_inactive_employee(): void
    {
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'is_active' => false]);
        $this->get(route('card.google-wallet', $emp->slug))->assertNotFound();
    }

    public function test_apple_wallet_returns_404_for_unknown_slug(): void
    {
        $this->get(route('card.apple-wallet', 'no-one'))->assertNotFound();
    }

    public function test_google_wallet_returns_404_for_unknown_slug(): void
    {
        $this->get(route('card.google-wallet', 'no-one'))->assertNotFound();
    }

    // ── No wallet buttons visible when not configured ─────────────────────────

    public function test_card_shows_no_wallet_buttons_when_not_configured(): void
    {
        Config::set('wallet.apple.team_id', '');
        Config::set('wallet.apple.pass_type_id', '');
        Config::set('wallet.google.issuer_id', '');

        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'is_active' => true]);
        $response = $this->get(route('card.show', $emp->slug));

        $response->assertDontSee('apple-wallet');
        $response->assertDontSee('google-wallet');
    }

    // ── No credentials in HTTP responses ─────────────────────────────────────

    public function test_apple_503_response_contains_no_secret(): void
    {
        Config::set('wallet.apple.team_id', '');
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'is_active' => true]);
        $content = $this->get(route('card.apple-wallet', $emp->slug))->getContent();

        $this->assertStringNotContainsString('private_key', $content);
        $this->assertStringNotContainsString('certificate', $content);
    }

    public function test_google_503_response_contains_no_secret(): void
    {
        Config::set('wallet.google.issuer_id', '');
        $emp = Employee::create(['first_name' => 'Alice', 'last_name' => 'Martin', 'is_active' => true]);
        $content = $this->get(route('card.google-wallet', $emp->slug))->getContent();

        $this->assertStringNotContainsString('private_key', $content);
        $this->assertStringNotContainsString('service_account', $content);
    }
}
