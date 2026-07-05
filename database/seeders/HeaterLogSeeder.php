<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Heater;
use App\Models\HeaterLog;
use Carbon\Carbon;

class HeaterLogSeeder extends Seeder
{
    public function run(): void
    {
        $heaters = Heater::all();
        if ($heaters->isEmpty()) return;

        // Create historical logs for the last 30 minutes
        for ($m = 30; $m >= 0; $m -= 2) {
            $time = Carbon::now()->subMinutes($m);

            foreach ($heaters as $heater) {
                // Determine specific statuses for demo
                // Heater CT05 is DANGER, CT03 & CT08 are WARNING, others NORMAL
                $current = 8.5;
                $voltage = 220;
                $temp = 55;
                $status = 'NORMAL';

                if ($heater->heater_code === 'CT05') {
                    $current = 3.2;
                    $temp = 68;
                    $status = 'DANGER';
                } elseif ($heater->heater_code === 'CT03') {
                    $current = 6.2;
                    $temp = 59;
                    $status = 'WARNING';
                } elseif ($heater->heater_code === 'CT08') {
                    $current = 5.8;
                    $temp = 61;
                    $status = 'WARNING';
                } else {
                    // Small variance for normal heaters
                    $current = round(7.5 + (rand(-5, 15) / 10), 2);
                    $voltage = rand(218, 222);
                    $temp = rand(50, 57);
                }

                HeaterLog::create([
                    'heater_id' => $heater->id,
                    'adc_value' => rand(500, 800),
                    'current' => $current,
                    'voltage' => $voltage,
                    'temperature' => $temp,
                    'status' => $status,
                    'received_at' => $time,
                    'created_at' => $time,
                    'updated_at' => $time,
                ]);
            }
        }
    }
}
