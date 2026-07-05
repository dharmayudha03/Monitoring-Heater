<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Heater;

class HeaterSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            $code = 'CT' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $name = 'Heater ' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $zone = 'Zone ' . $i;

            Heater::firstOrCreate(
                ['heater_code' => $code],
                [
                    'heater_name' => $name,
                    'zone' => $zone,
                    'description' => 'Monitoring Line ' . $i,
                    'is_active' => true
                ]
            );
        }
    }
}