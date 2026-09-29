<?php

return [

    // ── Apple Wallet ──────────────────────────────────────────────────────────
    'apple' => [
        // Create at: https://developer.apple.com → Certificates, Identifiers & Profiles → Identifiers → Pass Type IDs
        'team_id'           => env('APPLE_TEAM_ID', ''),
        'pass_type_id'      => env('APPLE_PASS_TYPE_IDENTIFIER', ''),

        // Absolute paths on the server (never committed to Git)
        // See docs/WALLET_IMPLEMENTATION.md for generation instructions
        'certificate_path'  => env('APPLE_CERTIFICATE_PATH', storage_path('app/private/wallet/apple/certificate.p12')),
        'key_password'      => env('APPLE_CERTIFICATE_PASSWORD', ''),
        'wwdr_path'         => env('APPLE_WWDR_PATH', storage_path('app/private/wallet/apple/wwdr.pem')),

        // Pass appearance
        'background_color'  => 'rgb(0, 49, 137)',    // MMI'e blue
        'foreground_color'  => 'rgb(255, 255, 255)',
        'label_color'       => 'rgb(180, 210, 255)',

        // Logo image path (PNG, ≤500 KB). Falls back to a generated placeholder.
        'logo_path'         => env('APPLE_LOGO_PATH', storage_path('app/private/wallet/apple/logo.png')),
        'logo2x_path'       => env('APPLE_LOGO2X_PATH', storage_path('app/private/wallet/apple/logo@2x.png')),
        'icon_path'         => env('APPLE_ICON_PATH', storage_path('app/private/wallet/apple/icon.png')),
        'icon2x_path'       => env('APPLE_ICON2X_PATH', storage_path('app/private/wallet/apple/icon@2x.png')),
    ],

    // ── Google Wallet ─────────────────────────────────────────────────────────
    'google' => [
        // Create at: https://pay.google.com/business/console
        'issuer_id'          => env('GOOGLE_WALLET_ISSUER_ID', ''),

        // Class suffix (combined with issuer_id → "{issuer_id}.{class_suffix}")
        'class_suffix'       => env('GOOGLE_WALLET_CLASS_SUFFIX', 'mmie-carte-visite'),

        // Service account JSON key file path (never committed to Git)
        // Download from Google Cloud Console → IAM → Service Accounts → Keys
        'service_account_key_path' => env('GOOGLE_WALLET_SA_KEY_PATH',
            storage_path('app/private/wallet/google/service-account.json')),

        // Public logo URL visible in the pass (must be publicly accessible)
        'logo_url'           => env('GOOGLE_WALLET_LOGO_URL', ''),

        // Pass appearance
        'background_color'   => '#003189',
    ],

];
