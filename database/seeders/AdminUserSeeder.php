<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $username = env('SEED_ADMIN_USERNAME', 'admin');
        $email    = env('SEED_ADMIN_EMAIL', 'admin@example.com');
        $password = env('SEED_ADMIN_PASSWORD', 'ChangeMe!123');

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
