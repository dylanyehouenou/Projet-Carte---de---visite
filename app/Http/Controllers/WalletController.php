<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\Wallet\WalletManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class WalletController extends Controller
{
    public function __construct(private readonly WalletManager $wallet) {}

    /**
     * Streams a signed .pkpass file for Apple Wallet.
     * Only active cards are served.
     */
    public function apple(string $slug): Response|RedirectResponse
    {
        $employee = Employee::where('slug', $slug)->where('is_active', true)->firstOrFail();

        if (!$this->wallet->isAppleConfigured()) {
            Log::warning('apple_wallet.not_configured', ['slug' => $slug]);
            abort(503, 'Apple Wallet not configured on this server.');
        }

        try {
            $binary = $this->wallet->appleProvider()->generatePkpass($employee);
        } catch (RuntimeException $e) {
            Log::error('apple_wallet.generation_failed', [
                'slug'  => $slug,
                'error' => $e->getMessage(),
            ]);
            abort(500, 'Could not generate Apple Wallet pass.');
        }

        return response($binary, HttpResponse::HTTP_OK, [
            'Content-Type'        => 'application/vnd.apple.pkpass',
            'Content-Disposition' => 'attachment; filename="' . $employee->slug . '.pkpass"',
            'Cache-Control'       => 'no-store, no-cache',
        ]);
    }

    /**
     * Redirects to the "Add to Google Wallet" save URL (JWT-signed).
     * Only active cards are served.
     */
    public function google(string $slug): RedirectResponse
    {
        $employee = Employee::where('slug', $slug)->where('is_active', true)->firstOrFail();

        if (!$this->wallet->isGoogleConfigured()) {
            Log::warning('google_wallet.not_configured', ['slug' => $slug]);
            abort(503, 'Google Wallet not configured on this server.');
        }

        try {
            $saveUrl = $this->wallet->googleProvider()->generateSaveUrl($employee);
        } catch (RuntimeException $e) {
            Log::error('google_wallet.generation_failed', [
                'slug'  => $slug,
                'error' => $e->getMessage(),
            ]);
            abort(500, 'Could not generate Google Wallet pass.');
        }

        return redirect()->away($saveUrl);
    }
}
