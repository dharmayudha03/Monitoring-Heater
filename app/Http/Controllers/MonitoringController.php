<?php

namespace App\Http\Controllers;

use App\Models\Heater;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        $heaters = Heater::where('is_active', true)->with('latestLog')->orderBy('heater_code')->get();
        $sysSetting = \App\Models\Setting::first() ?: \App\Models\Setting::create([
            'normal_min' => 9.00,
            'warning_min' => 7.60,
            'm_ct1' => 2.681,
            'm_ct2' => 2.480,
            'm_ct3' => 3.013,
            'm_ct4' => 3.171,
            'm_ct5' => 3.199,
            'm_ct6' => 2.989,
            'upper_baseline' => 10.939,
            'lower_baseline' => 10.939,
            'telegram_enabled' => true,
            'sampling_interval' => 5
        ]);
        return view('monitoring.index', compact('heaters', 'sysSetting'));
    }
}
