<?php

namespace App\Services\Wallet\Google;

use App\Models\Employee;
use App\Services\Wallet\Contracts\WalletProviderInterface;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GoogleWalletProvider implements WalletProviderInterface
{
    public function __construct(private readonly ObjectBuilder $builder) {}

    public function isConfigured(): bool
    {
        $issuerId = config('wallet.google.issuer_id');
        $keyPath  = config('wallet.google.service_account_key_path');

        if (empty($issuerId)) {
            return false;
        }

        if (!file_exists($keyPath)) {
            return false;
        }

        return true;
    }

    public function getSaveUrl(Employee $employee): string
    {
        return route('card.google-wallet', $employee->slug);
    }

    /**
     * Generates the "Add to Google Wallet" save URL containing a signed JWT.
     * Must only be called when isConfigured() returns true.
     *
     * @throws RuntimeException if the service account key cannot be read or parsed
     */
    public function generateSaveUrl(Employee $employee): string
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('Google Wallet is not configured.');
        }

        $issuerId    = config('wallet.google.issuer_id');
        $classSuffix = config('wallet.google.class_suffix');
        $keyPath     = config('wallet.google.service_account_key_path');

        $serviceAccount = json_decode(file_get_contents($keyPath), true);

        if (empty($serviceAccount['client_email']) || empty($serviceAccount['private_key'])) {
            throw new RuntimeException('Invalid Google service account key file.');
        }

        $genericObject = $this->builder->build($employee, $issuerId, $classSuffix);

        $payload = [
            'iss'     => $serviceAccount['client_email'],
            'aud'     => 'google',
            'typ'     => 'savetowallet',
            'iat'     => time(),
            'payload' => [
                'genericObjects' => [$genericObject],
            ],
        ];

        $jwt = JWT::encode($payload, $serviceAccount['private_key'], 'RS256');

        Log::info('google_wallet.jwt_generated', [
            'employee_id' => $employee->id,
            'slug'        => $employee->slug,
            'object_id'   => $issuerId . '.employee-' . $employee->id,
        ]);

        return 'https://pay.google.com/gp/v/save/' . $jwt;
    }
}
