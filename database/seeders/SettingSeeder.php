<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::create([
            'normal_min' => 9.00,
            'warning_min' => 7.60,
            'm_ct1' => 1.425,
            'm_ct2' => 1.467,
            'm_ct3' => 1.297,
            'm_ct4' => 1.192,
            'm_ct5' => 1.372,
            'm_ct6' => 1.157,
            'upper_baseline' => 11.00,
            'lower_baseline' => 11.00,
            'telegram_enabled' => true,
            'sampling_interval' => 5
        ]);
    }
}