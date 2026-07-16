<?php

namespace App\Http\Controllers;

use App\Models\Heater;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        $heaters = Heater::where('is_active', true)->orderBy('heater_code')->get();
        $sysSetting = \App\Models\Setting::first() ?: \App\Models\Setting::create([
            'normal_min' => 11.00,
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
        return view('monitoring.index', compact('heaters', 'sysSetting'));
    }
}
