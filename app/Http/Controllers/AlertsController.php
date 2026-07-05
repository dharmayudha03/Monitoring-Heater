<?php

namespace App\Http\Controllers;

use App\Models\HeaterLog;
use Illuminate\Http\Request;

class AlertsController extends Controller
{
    public function index(Request $request)
    {
        $query = HeaterLog::with('heater')
            ->whereIn('status', ['WARNING', 'DANGER'])
            ->orderBy('received_at', 'desc');

        if ($request->filled('severity')) {
            $query->where('status', $request->severity);
        }

        $alerts = $query->paginate(15)->withQueryString();

        return view('alerts.index', compact('alerts'));
    }
}
