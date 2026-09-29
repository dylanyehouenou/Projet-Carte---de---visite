<?php

namespace App\Services\Wallet\Google;

use App\Models\Employee;

/**
 * Builds the Google Wallet Generic Object payload.
 * Pure data — no I/O, no JWT, fully testable in isolation.
 */
class ObjectBuilder
{
    public function build(Employee $employee, string $issuerId, string $classSuffix): array
    {
        $objectId = $issuerId . '.employee-' . $employee->id;
        $classId  = $issuerId . '.' . $classSuffix;

        $object = [
            'id'                => $objectId,
            'classId'           => $classId,
            'genericType'       => 'GENERIC_TYPE_UNSPECIFIED',
            'hexBackgroundColor' => config('wallet.google.background_color', '#003189'),
            'state'             => 'ACTIVE',
            'cardTitle'         => [
                'defaultValue' => ['language' => 'fr', 'value' => "MMI'e"],
            ],
            'header'            => [
                'defaultValue' => ['language' => 'fr', 'value' => $employee->fullName()],
            ],
            'barcode'           => [
                'type'          => 'QR_CODE',
                'value'         => $employee->publicUrl(),
                'alternateText' => $employee->slug,
            ],
        ];

        // Logo (optional — must be a publicly accessible URL)
        $logoUrl = config('wallet.google.logo_url');
        if (!empty($logoUrl)) {
            $object['logo'] = [
                'sourceUri' => ['uri' => $logoUrl],
                'contentDescription' => ['defaultValue' => ['language' => 'fr', 'value' => "Logo MMI'e"]],
            ];
        }

        // Text modules (shown on the pass front)
        $textModules = [];

        if ($employee->job_title) {
            $textModules[] = ['id' => 'title', 'header' => 'FONCTION', 'body' => $employee->job_title];
        }

        if ($employee->department) {
            $textModules[] = ['id' => 'dept', 'header' => 'SERVICE', 'body' => $employee->department];
        }

        if (!empty($textModules)) {
            $object['textModulesData'] = $textModules;
        }

        // Links module (back of pass)
        $uris = [
            [
                'uri'         => $employee->publicUrl(),
                'description' => 'Carte virtuelle',
                'id'          => 'card_url',
            ],
        ];

        if ($employee->email) {
            $uris[] = ['uri' => 'mailto:' . $employee->email, 'description' => 'Email', 'id' => 'email'];
        }

        if ($employee->phone) {
            $uris[] = ['uri' => 'tel:' . $employee->phone, 'description' => 'Téléphone', 'id' => 'phone'];
        }

        if ($employee->linkedin_url) {
            $uris[] = ['uri' => $employee->linkedin_url, 'description' => 'LinkedIn', 'id' => 'linkedin'];
        }

        if ($employee->calendly_url) {
            $uris[] = ['uri' => $employee->calendly_url, 'description' => 'Prendre rendez-vous', 'id' => 'calendly'];
        }

        $object['linksModuleData'] = ['uris' => $uris];

        return $object;
    }
}
