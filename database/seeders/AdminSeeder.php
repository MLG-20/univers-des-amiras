<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::updateOrCreate(
            ['email' => env('ADMIN_SEED_EMAIL', 'admin@amiras.test')],
            [
                'name' => 'Administrateur',
                'password' => env('ADMIN_SEED_PASSWORD', 'password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $admin->syncRoles(['admin']);
    }
}
