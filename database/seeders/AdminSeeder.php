<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@mmi-e.fr')],
            [
                'name'      => env('ADMIN_NAME', 'Administrateur MMI\'e'),
                'password'  => Hash::make(env('ADMIN_PASSWORD', 'changeme')),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );
    }
}
