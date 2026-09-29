<?php

namespace App\Services\Wallet\Apple;

use App\Models\Employee;

/**
 * Builds the pass.json array for an Apple Wallet generic pass.
 * Pure data — no I/O, no external dependencies, fully testable.
 */
class PassBuilder
{
    public function build(Employee $employee, string $passTypeId, string $teamId): array
    {
        $pass = [
            'formatVersion'      => 1,
            'passTypeIdentifier' => $passTypeId,
            'serialNumber'       => $employee->qr_token,
            'teamIdentifier'     => $teamId,
            'organizationName'   => "MMI'e",
            'description'        => 'Carte de visite — ' . $employee->fullName(),
            'foregroundColor'    => config('wallet.apple.foreground_color'),
            'backgroundColor'    => config('wallet.apple.background_color'),
            'labelColor'         => config('wallet.apple.label_color'),
            'generic'            => $this->buildGeneric($employee),
            'barcodes'           => [[
                'message'         => $employee->publicUrl(),
                'format'          => 'PKBarcodeFormatQR',
                'messageEncoding' => 'iso-8859-1',
            ]],
        ];

        return $pass;
    }

    private function buildGeneric(Employee $employee): array
    {
        $generic = [
            'primaryFields'   => [
                ['key' => 'name', 'label' => 'NOM', 'value' => $employee->fullName()],
            ],
            'secondaryFields' => [],
            'auxiliaryFields' => [],
            'backFields'      => $this->buildBackFields($employee),
        ];

        if ($employee->job_title) {
            $generic['secondaryFields'][] = [
                'key' => 'title', 'label' => 'FONCTION', 'value' => $employee->job_title,
            ];
        }

        if ($employee->department) {
            $generic['secondaryFields'][] = [
                'key' => 'department', 'label' => 'SERVICE', 'value' => $employee->department,
            ];
        }

        return $generic;
    }

    private function buildBackFields(Employee $employee): array
    {
        $fields = [];

        if ($employee->email) {
            $fields[] = [
                'key'             => 'email',
                'label'           => 'Email',
                'value'           => $employee->email,
                'attributedValue' => '<a href="mailto:' . $employee->email . '">' . $employee->email . '</a>',
            ];
        }

        if ($employee->phone) {
            $fields[] = [
                'key'             => 'phone',
                'label'           => 'Téléphone',
                'value'           => $employee->phone,
                'attributedValue' => '<a href="tel:' . $employee->phone . '">' . $employee->phone . '</a>',
            ];
        }

        if ($employee->website) {
            $fields[] = [
                'key'             => 'website',
                'label'           => 'Site web',
                'value'           => $employee->website,
                'attributedValue' => '<a href="' . $employee->website . '">' . $employee->website . '</a>',
            ];
        }

        if ($employee->linkedin_url) {
            $fields[] = [
                'key'             => 'linkedin',
                'label'           => 'LinkedIn',
                'value'           => $employee->linkedin_url,
                'attributedValue' => '<a href="' . $employee->linkedin_url . '">Voir le profil</a>',
            ];
        }

        if ($employee->calendly_url) {
            $fields[] = [
                'key'             => 'calendly',
                'label'           => 'Rendez-vous',
                'value'           => $employee->calendly_url,
                'attributedValue' => '<a href="' . $employee->calendly_url . '">Prendre rendez-vous</a>',
            ];
        }

        $fields[] = [
            'key'             => 'card_url',
            'label'           => 'Carte virtuelle',
            'value'           => $employee->publicUrl(),
            'attributedValue' => '<a href="' . $employee->publicUrl() . '">Voir la carte</a>',
        ];

        return $fields;
    }
}
