<?php

namespace App\Services\Wallet\Apple;

use App\Models\Employee;
use App\Services\Wallet\Contracts\WalletProviderInterface;
use PKPass\PKPass;
use RuntimeException;
use Illuminate\Support\Facades\Log;

class AppleWalletProvider implements WalletProviderInterface
{
    public function __construct(private readonly PassBuilder $builder) {}

    public function isConfigured(): bool
    {
        $teamId  = config('wallet.apple.team_id');
        $passId  = config('wallet.apple.pass_type_id');
        $cert    = config('wallet.apple.certificate_path');
        $wwdr    = config('wallet.apple.wwdr_path');

        if (empty($teamId) || empty($passId)) {
            return false;
        }

        if (!file_exists($cert) || !file_exists($wwdr)) {
            return false;
        }

        return true;
    }

    public function getSaveUrl(Employee $employee): string
    {
        return route('card.apple-wallet', $employee->slug);
    }

    /**
     * Generates the binary .pkpass file content.
     * Must only be called when isConfigured() returns true.
     *
     * @throws RuntimeException if generation or signing fails
     */
    public function generatePkpass(Employee $employee): string
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('Apple Wallet is not configured.');
        }

        $passData = $this->builder->build(
            $employee,
            config('wallet.apple.pass_type_id'),
            config('wallet.apple.team_id'),
        );

        $pkpass = new PKPass(
            config('wallet.apple.certificate_path'),
            config('wallet.apple.key_password'),
        );

        $pkpass->setWwdrCertificatePath(config('wallet.apple.wwdr_path'));
        $pkpass->setData($passData);

        // Add pass images (logo, icon) — falls back to generated placeholders
        foreach ($this->resolveImages() as $content => $name) {
            $pkpass->addFileContent($content, $name);
        }

        $binary = $pkpass->create();

        if (empty($binary)) {
            throw new RuntimeException('pkpass generation returned empty content.');
        }

        Log::info('apple_wallet.generated', [
            'employee_id' => $employee->id,
            'slug'        => $employee->slug,
        ]);

        return $binary;
    }

    /**
     * Returns [fileContent => filename] pairs for the pass images.
     * Uses configured files when available, falls back to GD-generated placeholders.
     */
    private function resolveImages(): array
    {
        $pairs = [];

        $imageMap = [
            config('wallet.apple.logo_path')   => 'logo.png',
            config('wallet.apple.logo2x_path') => 'logo@2x.png',
            config('wallet.apple.icon_path')   => 'icon.png',
            config('wallet.apple.icon2x_path') => 'icon@2x.png',
        ];

        foreach ($imageMap as $path => $name) {
            $pairs[file_exists($path) ? file_get_contents($path) : $this->generatePlaceholderPng()] = $name;
        }

        return $pairs;
    }

    private function generatePlaceholderPng(): string
    {
        $img = imagecreatetruecolor(58, 58);
        $blue  = imagecolorallocate($img, 0, 49, 137);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $blue);
        imagestring($img, 2, 10, 22, "M", $white);
        ob_start();
        imagepng($img);
        $png = ob_get_clean();
        imagedestroy($img);
        return $png;
    }
}
