<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@irc.co.id'],
            [
                'name' => 'Administrator System',
                'role' => 'admin',
                'password' => Hash::make('admin'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@irc.co.id'],
            [
                'name' => 'Operator Monitoring',
                'role' => 'operator',
                'password' => Hash::make('user'),
            ]
        );
    }
}
