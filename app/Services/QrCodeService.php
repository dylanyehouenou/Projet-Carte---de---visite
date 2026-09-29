<?php

namespace App\Services;

use App\Models\Employee;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use RuntimeException;

class QrCodeService
{
    public function generatePng(Employee $employee): string
    {
        $qrCode = new QrCode(
            data: $employee->publicUrl(),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 300,
            margin: 10,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255),
        );

        $png = (new PngWriter())->write($qrCode)->getString();

        if (substr($png, 0, 8) !== "\x89PNG\r\n\x1a\n") {
            throw new RuntimeException('QR code generation produced an invalid PNG');
        }

        return $png;
    }
}
