<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $username = env('SEED_ADMIN_USERNAME', 'admin');
        $email    = env('SEED_ADMIN_EMAIL', 'admin@example.com');
        $password = env('SEED_ADMIN_PASSWORD');
        if (blank($password) && app()->environment('production')) {
            throw new RuntimeException('SEED_ADMIN_PASSWORD must be set before seeding the production admin user.');
        }
        $password = $password ?: 'ChangeMe123!';

        User::updateOrCreate(
            ['username' => $username],
            [
                'name'     => 'Administrator',
                'email'    => $email,
                'password' => Hash::make($password),
                'role'     => 'admin',
            ]
        );
    }
}
