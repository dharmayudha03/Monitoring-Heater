<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Heater;
use App\Models\Replacement;
use Carbon\Carbon;

class ReplacementSeeder extends Seeder
{
    public function run(): void
    {
        $heater2 = Heater::where('heater_code', 'CT02')->first();
        $heater9 = Heater::where('heater_code', 'CT09')->first();

        if ($heater2) {
            Replacement::create([
                'heater_id' => $heater2->id,
                'old_heater_code' => 'CT02-OLD',
                'new_heater_code' => 'CT02',
                'reason' => 'Elemen pemanas putus / DANGER Overheat',
                'replaced_by' => 'Agus (Maintenance Team)',
                'replacement_date' => Carbon::now()->subDays(5),
                'notes' => 'Penggantian sparepart elemen pemanas tipe HTR-220V-1000W.'
            ]);
        }

        if ($heater9) {
            Replacement::create([
                'heater_id' => $heater9->id,
                'old_heater_code' => 'CT09-OLD',
                'new_heater_code' => 'CT09',
                'reason' => 'Arus drop mendadak (DANGER)',
                'replaced_by' => 'Budi (Teknisi Listrik)',
                'replacement_date' => Carbon::now()->subDays(12),
                'notes' => 'Penggantian set modul heater dan sensor arus.'
            ]);
        }
    }
}
