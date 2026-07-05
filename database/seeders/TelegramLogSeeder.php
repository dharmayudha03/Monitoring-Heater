<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Heater;
use App\Models\TelegramLog;
use Carbon\Carbon;

class TelegramLogSeeder extends Seeder
{
    public function run(): void
    {
        $ct5 = Heater::where('heater_code', 'CT05')->first();
        $ct3 = Heater::where('heater_code', 'CT03')->first();

        if ($ct5) {
            TelegramLog::create([
                'heater_id' => $ct5->id,
                'message' => "🚨 ALERT DANGER! Heater CT05 mengalami penurunan arus kritis (3.21 A) dan suhu tinggi (68 °C). Perlu penggantian unit heater!",
                'status' => 'SUCCESS',
                'sent_at' => Carbon::now()->subMinutes(10),
            ]);
        }

        if ($ct3) {
            TelegramLog::create([
                'heater_id' => $ct3->id,
                'message' => "⚠️ ALERT WARNING! Heater CT03 terdeteksi arus 6.21 A (di bawah ambang batas normal 8.00 A).",
                'status' => 'SUCCESS',
                'sent_at' => Carbon::now()->subMinutes(15),
            ]);
        }
    }
}
