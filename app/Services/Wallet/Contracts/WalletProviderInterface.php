<?php

namespace App\Services\Wallet\Contracts;

use App\Models\Employee;

interface WalletProviderInterface
{
    public function isConfigured(): bool;

    /**
     * Returns the URL the "Add to Wallet" button should point to.
     * Apple → internal route that streams the .pkpass
     * Google → external JWT-signed URL at pay.google.com
     */
    public function getSaveUrl(Employee $employee): string;
}
