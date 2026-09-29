<?php

namespace App\Services\Wallet;

use App\Models\Employee;
use App\Services\Wallet\Apple\AppleWalletProvider;
use App\Services\Wallet\Google\GoogleWalletProvider;

/**
 * Façade unique pour les deux providers Wallet.
 * Injecter WalletManager partout — les providers restent des détails internes.
 */
class WalletManager
{
    public function __construct(
        private readonly AppleWalletProvider  $apple,
        private readonly GoogleWalletProvider $google,
    ) {}

    public function isAppleConfigured(): bool
    {
        return $this->apple->isConfigured();
    }

    public function isGoogleConfigured(): bool
    {
        return $this->google->isConfigured();
    }

    public function appleProvider(): AppleWalletProvider
    {
        return $this->apple;
    }

    public function googleProvider(): GoogleWalletProvider
    {
        return $this->google;
    }
}
