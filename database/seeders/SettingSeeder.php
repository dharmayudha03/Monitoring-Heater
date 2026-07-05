<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::create([
            'normal_min' => 8,
            'warning_min' => 5,
            'telegram_enabled' => true,
            'sampling_interval' => 5
        ]);
    }
}