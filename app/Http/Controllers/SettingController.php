<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class SettingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $sysSetting = Setting::first();
        if (!$sysSetting) {
            $sysSetting = Setting::create([
                'normal_min' => 9.00,
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
        }

        return view('settings.index', compact('user', 'sysSetting'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'new_password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
            $user->plain_password = $request->new_password;
        }

        $user->save();

        return redirect()->route('settings.index')->with('success', 'Profil Pengguna berhasil diperbarui!');
    }

    public function updateSystem(Request $request)
    {
        $request->validate([
            'normal_min' => 'required|numeric',
            'warning_min' => 'required|numeric',
            'upper_baseline' => 'required|numeric',
            'lower_baseline' => 'required|numeric',
        ]);

        $sysSetting = Setting::first();
        if (!$sysSetting) {
            $sysSetting = new Setting();
        }

        $sysSetting->normal_min = $request->normal_min;
        $sysSetting->warning_min = $request->warning_min;
        $sysSetting->upper_baseline = $request->upper_baseline;
        $sysSetting->lower_baseline = $request->lower_baseline;
        $sysSetting->save();

        // Push Configuration updates directly to Firebase so the ESP32 fetches them instantly
        try {
            Http::withoutVerifying()
                ->patch('https://ctfh-f0c6d-default-rtdb.firebaseio.com/konfigurasi_sistem.json?auth=AIzaSyBK41jJSMb0u4SnaibRqRA1gelzjh40zIo', [
                    'upper_baseline' => (float)$request->upper_baseline,
                    'lower_baseline' => (float)$request->lower_baseline,
                ]);
        } catch (\Exception $e) {}

        return redirect()->route('settings.index')->with('success', 'Konfigurasi Batas & Baseline Sistem berhasil diperbarui!');
    }

    public function calibrateSensor(Request $request)
    {
        $request->validate([
            'heater_code' => 'required|string|in:CT01,CT02,CT03,CT04,CT05,CT06',
            'type' => 'required|in:auto,manual',
            'actual_current' => 'nullable|numeric|min:0.01',
            'manual_multiplier' => 'nullable|numeric|min:0.001',
            'current_esp_reading' => 'nullable|numeric',
        ]);

        $sysSetting = Setting::first() ?: Setting::create([
            'normal_min' => 9.00,
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

        // Map heater_code to multiplier setting field
        $fieldMap = [
            'CT01' => 'm_ct1',
            'CT02' => 'm_ct2',
            'CT03' => 'm_ct3',
            'CT04' => 'm_ct4',
            'CT05' => 'm_ct5',
            'CT06' => 'm_ct6',
        ];

        $field = $fieldMap[$request->heater_code];
        $oldMultiplier = (float) $sysSetting->$field;

        if ($request->type === 'auto') {
            $espReading = (float) $request->current_esp_reading;
            if ($espReading <= 0.05) {
                return redirect()->back()->with('error', 'Gagal Kalibrasi Otomatis: Arus pembacaan ESP32 bernilai 0 atau terlalu kecil. Silakan gunakan Kalibrasi Manual.');
            }
            $actual = (float) $request->actual_current;
            $newMultiplier = $oldMultiplier * ($actual / $espReading);
        } else {
            $newMultiplier = (float) $request->manual_multiplier;
        }

        // Save new multiplier
        $sysSetting->$field = round($newMultiplier, 3);
        $sysSetting->save();

        // Instantly recalculate the current displayed for immediate UI feedback
        $heater = \App\Models\Heater::where('heater_code', $request->heater_code)->first();
        if ($heater) {
            $latestLog = $heater->latestLog;
            if ($latestLog && $oldMultiplier > 0) {
                $oldCurrent = (float)$latestLog->current;
                $newCurrent = $oldCurrent * ($newMultiplier / $oldMultiplier);

                if ($newCurrent >= $sysSetting->normal_min) {
                    $newStatus = 'NORMAL';
                } elseif ($newCurrent >= $sysSetting->warning_min) {
                    $newStatus = 'WARNING';
                } else {
                    $newStatus = 'DANGER';
                }

                $latestLog->update([
                    'current' => round($newCurrent, 2),
                    'status' => $newStatus,
                ]);
            }
        }



        return redirect()->back()->with('success', "Sensor {$request->heater_code} berhasil dikalibrasi! Multiplier diubah dari {$oldMultiplier} menjadi " . round($newMultiplier, 3));
    }
}
