<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            HeaterSeeder::class,
            SettingSeeder::class,
            HeaterLogSeeder::class,
            ReplacementSeeder::class,
            TelegramLogSeeder::class,
        ]);
    }
}