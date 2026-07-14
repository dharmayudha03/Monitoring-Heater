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
            'm_ct1' => 2.681,
            'm_ct2' => 2.480,
            'm_ct3' => 3.013,
            'm_ct4' => 3.171,
            'm_ct5' => 3.199,
            'm_ct6' => 2.989,
            'upper_baseline' => 13.00,
            'lower_baseline' => 13.00,
            'telegram_enabled' => true,
            'sampling_interval' => 5
        ]);
    }
}