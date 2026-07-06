<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Super Admin (Jon)
        User::updateOrCreate(
            ['email' => 'superadmin@irc.co.id'],
            [
                'name' => 'Super Administrator (Jon)',
                'role' => 'super_admin',
                'password' => Hash::make('superadmin123'),
                'plain_password' => 'superadmin123',
            ]
        );

        // 2. Engineering & Maintenance
        User::updateOrCreate(
            ['email' => 'engineering@irc.co.id'],
            [
                'name' => 'Engineering & Maintenance',
                'role' => 'engineering',
                'password' => Hash::make('engineering123'),
                'plain_password' => 'engineering123',
            ]
        );

        // 3. Operator / Public Viewer
        User::updateOrCreate(
            ['email' => 'user@irc.co.id'],
            [
                'name' => 'Operator & Public Viewer',
                'role' => 'user',
                'password' => Hash::make('user123'),
                'plain_password' => 'user123',
            ]
        );
    }
}
