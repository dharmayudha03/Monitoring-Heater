<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Heater;

class HeaterSeeder extends Seeder
{
    public function run(): void
    {
        $heaters = [
            [
                'heater_code' => 'CT01',
                'heater_name' => 'CT01 - Upper RS',
                'zone' => 'Upper Mold RS',
                'description' => 'Monitoring Heater Cetakan Atas Fasa R-S (2 Heater)'
            ],
            [
                'heater_code' => 'CT02',
                'heater_name' => 'CT02 - Upper ST',
                'zone' => 'Upper Mold ST',
                'description' => 'Monitoring Heater Cetakan Atas Fasa S-T (2 Heater)'
            ],
            [
                'heater_code' => 'CT03',
                'heater_name' => 'CT03 - Upper TR',
                'zone' => 'Upper Mold TR',
                'description' => 'Monitoring Heater Cetakan Atas Fasa T-R (2 Heater)'
            ],
            [
                'heater_code' => 'CT04',
                'heater_name' => 'CT04 - Lower RS',
                'zone' => 'Lower Mold RS',
                'description' => 'Monitoring Heater Cetakan Bawah Fasa R-S (2 Heater)'
            ],
            [
                'heater_code' => 'CT05',
                'heater_name' => 'CT05 - Lower ST',
                'zone' => 'Lower Mold ST',
                'description' => 'Monitoring Heater Cetakan Bawah Fasa S-T (2 Heater)'
            ],
            [
                'heater_code' => 'CT06',
                'heater_name' => 'CT06 - Lower TR',
                'zone' => 'Lower Mold TR',
                'description' => 'Monitoring Heater Cetakan Bawah Fasa T-R (2 Heater)'
            ],
        ];

        foreach ($heaters as $h) {
            Heater::updateOrCreate(
                ['heater_code' => $h['heater_code']],
                [
                    'heater_name' => $h['heater_name'],
                    'zone' => $h['zone'],
                    'description' => $h['description'],
                    'is_active' => true
                ]
            );
        }

        // Clean up any stray old heaters beyond CT06 if they exist
        Heater::whereNotIn('heater_code', array_column($heaters, 'heater_code'))->delete();
    }
}