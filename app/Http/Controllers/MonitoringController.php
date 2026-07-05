<?php

namespace App\Http\Controllers;

use App\Models\Heater;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        $heaters = Heater::where('is_active', true)->with('latestLog')->orderBy('heater_code')->get();
        return view('monitoring.index', compact('heaters'));
    }
}
