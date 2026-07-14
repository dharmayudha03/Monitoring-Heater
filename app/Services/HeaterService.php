<?php

namespace App\Services;

use App\Models\Heater;
use App\Models\HeaterLog;
use App\Models\Setting;
use App\Services\TelegramService;

class HeaterService
{
    public function store(array $data)
    {
        $heater = Heater::where(
            'heater_code',
            $data['heater_code']
        )->first();

        if (!$heater) {
            abort(404, 'Heater tidak ditemukan');
        }

        // Reset/Forget offline alert jika ada data telemetri masuk (berarti online)
        if (\Illuminate\Support\Facades\Cache::get('esp32_offline_alert_sent', false)) {
            $msg = "🟢 <b>KONEKSI PULIH (ESP32 ONLINE)</b>\n\n"
                 . "Perangkat monitoring IoT Tungyu Heater kini telah terhubung kembali (<b>ONLINE</b>)!\n"
                 . "Aliran data telemetri bulk dilanjutkan dengan sukses.\n\n"
                 . "📅 <b>Waktu Pulih:</b> " . now()->format('d-m-Y H:i:s') . "\n\n"
                 . "✅ <i>Sistem monitoring berjalan normal kembali. Terima kasih!</i>";

            app(\App\Services\TelegramService::class)->sendMessage($msg);
            \Illuminate\Support\Facades\Cache::forget('esp32_offline_alert_sent');
        }

        $previousStatus = $heater->last_status ?? 'NORMAL';
        $lastLogTime = $heater->last_received_at;

        $settings = Setting::first() ?: Setting::create([
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

        // Tentukan status berdasarkan setingan dinamis db
        if ($data['current'] < 1.0) {
            $status = 'OFFLINE';
        } elseif ($data['current'] >= $settings->normal_min) {
            $status = 'NORMAL';
        } elseif ($data['current'] >= $settings->warning_min) {
            $status = 'WARNING';
        } else {
            $status = 'DANGER';
        }

        // Selalu catat log histori ke database untuk merekam fluktuasi arus secara detail
        $log = HeaterLog::create([
            'heater_id' => $heater->id,
            'adc_value' => $data['adc_value'] ?? null,
            'current' => $data['current'],
            'voltage' => $data['voltage'] ?? null,
            'temperature' => $data['temperature'] ?? null,
            'status' => $status,
            'received_at' => now(),
        ]);

        // Selalu perbarui status real-time terakhir pada tabel heaters
        $heater->update([
            'last_current' => $data['current'],
            'last_status' => $status,
            'last_received_at' => now(),
        ]);

        // Trigger Automatic Real-Time Telegram Alert for WARNING or DANGER
        if (in_array($status, ['WARNING', 'DANGER']) && $status !== $previousStatus) {
            try {
                $telegramService = app(TelegramService::class);
                $icon = $status === 'DANGER' ? '🚨 DANGER' : '⚠️ WARNING';
                $actionMsg = $status === 'DANGER' 
                    ? "<b>PERHATIAN Kritis:</b> Unit Heater perlu SEGERA DIGANTI (Arus di bawah {$settings->warning_min} A)!" 
                    : "<b>PERINGATAN:</b> Arus heater di bawah ambang normal. Lakukan inspeksi (Arus di bawah {$settings->normal_min} A).";
                
                $msg = "{$icon} <b>DETEKSI SENSOR: STATUS {$status}</b>\n\n"
                     . "Kode Heater: <b>{$heater->heater_code}</b> ({$heater->heater_name})\n"
                     . "Zona: {$heater->zone}\n"
                     . "Arus: <b>{$data['current']} A</b>\n"
                     . "Waktu: " . now()->format('d-m-Y H:i:s') . "\n\n"
                     . "{$actionMsg}";
                
                $telegramService->sendMessage($msg, $heater->id);
            } catch (\Exception $e) {}
        }

        return $log ?? $heater;
    }

    public function getAllHeaters()
    {
        return Heater::where('is_active', true)
            ->orderBy('heater_code')
            ->get();
    }

    public function getDetailHeater($heater_code)
    {
        // Detail heater placeholder/action
    }

    public function getChartData(\Illuminate\Http\Request $request)
    {
        $range = $request->input('range', 'realtime');
        $shift = $request->input('shift', 'shift1');
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $heaters = Heater::where('is_active', true)->orderBy('heater_code')->get();
        $datasets = [];
        $now = now();

        if ($range === 'realtime') {
            foreach ($heaters as $heater) {
                $logs = HeaterLog::where('heater_id', $heater->id)
                    ->orderBy('received_at', 'desc')
                    ->take(20)
                    ->get()
                    ->reverse()
                    ->values();

                $data = $logs->map(function ($log) {
                    return [
                        'time' => $log->received_at->format('H:i:s'),
                        'current' => $log->current
                    ];
                });

                $datasets[] = [
                    'label' => $heater->heater_code,
                    'data' => $data
                ];
            }
            return $datasets;
        }

        // Generate fixed X-axis timeline slots for Shift, Daily, and Monthly
        $slots = [];
        $startWindow = null;
        $endWindow = null;

        if ($range === 'shift') {
            if ($shift === 'shift1') {
                $startWindow = $now->copy()->setTime(7, 0, 0);
                $endWindow = $now->copy()->setTime(15, 0, 0);
                if ($now->lt($startWindow)) {
                    $startWindow->subDay();
                    $endWindow->subDay();
                }
            } elseif ($shift === 'shift2') {
                $startWindow = $now->copy()->setTime(15, 0, 0);
                $endWindow = $now->copy()->setTime(23, 0, 0);
                if ($now->lt($startWindow)) {
                    $startWindow->subDay();
                    $endWindow->subDay();
                }
            } elseif ($shift === 'shift3') {
                if ($now->hour >= 23) {
                    $startWindow = $now->copy()->setTime(23, 0, 0);
                    $endWindow = $now->copy()->addDay()->setTime(7, 0, 0);
                } else {
                    $startWindow = $now->copy()->subDay()->setTime(23, 0, 0);
                    $endWindow = $now->copy()->setTime(7, 0, 0);
                }
            }

            // Generate 30-minute fixed slots across full shift (e.g. 07:00 to 15:00)
            $curr = $startWindow->copy();
            while ($curr->lte($endWindow)) {
                $slots[] = [
                    'label' => $curr->format('H:i'),
                    'start' => $curr->copy(),
                    'end' => $curr->copy()->addMinutes(30)->subSecond()
                ];
                $curr->addMinutes(30);
            }
        } elseif ($range === 'daily') {
            $startWindow = $now->copy()->startOfDay();
            $endWindow = $now->copy()->endOfDay();

            // Generate 1-hour fixed slots from 00:00 to 23:00
            $curr = $startWindow->copy();
            while ($curr->lte($endWindow)) {
                $slots[] = [
                    'label' => $curr->format('H:i'),
                    'start' => $curr->copy(),
                    'end' => $curr->copy()->addHour()->subSecond()
                ];
                $curr->addHour();
            }
        } elseif ($range === 'monthly') {
            $selectedDate = \Carbon\Carbon::createFromDate($year, $month, 1);
            $startWindow = $selectedDate->copy()->startOfMonth();
            $endWindow = $selectedDate->copy()->endOfMonth();

            $daysInMonth = $selectedDate->daysInMonth;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dayStart = \Carbon\Carbon::createFromDate($year, $month, $d)->startOfDay();
                $dayEnd = \Carbon\Carbon::createFromDate($year, $month, $d)->endOfDay();
                $slots[] = [
                    'label' => 'Tgl ' . str_pad($d, 2, '0', STR_PAD_LEFT),
                    'start' => $dayStart,
                    'end' => $dayEnd
                ];
            }
        }

        // Map logs into fixed slots
        foreach ($heaters as $heater) {
            // Fetch logs for this heater up to endWindow
            $logsInWindow = HeaterLog::where('heater_id', $heater->id)
                ->where('received_at', '<=', $endWindow)
                ->orderBy('received_at', 'asc')
                ->get();

            $data = [];
            $lastKnownCurrent = null;

            foreach ($slots as $slot) {
                // If slot start is in the future relative to now, set value to null so line stops at current time!
                if ($slot['start']->gt($now)) {
                    $data[] = [
                        'time' => $slot['label'],
                        'current' => null
                    ];
                    continue;
                }

                // Filter logs falling within this slot window
                $matchingLogs = $logsInWindow->filter(function($log) use ($slot) {
                    return $log->received_at->gte($slot['start']) && $log->received_at->lte($slot['end']);
                });

                if ($matchingLogs->count() > 0) {
                    $lastKnownCurrent = round($matchingLogs->avg('current'), 2);
                    $data[] = [
                        'time' => $slot['label'],
                        'current' => $lastKnownCurrent
                    ];
                } else {
                    // For past/ongoing slots, carry forward the latest known current reading up to this slot
                    $latestBefore = $logsInWindow->filter(function($log) use ($slot) {
                        return $log->received_at->lte($slot['end']);
                    })->last();

                    if ($latestBefore) {
                        $lastKnownCurrent = round($latestBefore->current, 2);
                        $data[] = [
                            'time' => $slot['label'],
                            'current' => $lastKnownCurrent
                        ];
                    } else {
                        // System had not started logging yet before this slot
                        $data[] = [
                            'time' => $slot['label'],
                            'current' => null
                        ];
                    }
                }
            }

            $datasets[] = [
                'label' => $heater->heater_code,
                'data' => $data
            ];
        }

        return $datasets;
    }

    public function getLatestAlerts()
    {
        return HeaterLog::with('heater')
            ->whereIn('status', ['WARNING', 'DANGER'])
            ->orderBy('received_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($log) {
                return [
                    'heater_code' => $log->heater->heater_code,
                    'heater_name' => $log->heater->heater_name,
                    'status' => $log->status,
                    'current' => $log->current,
                    'time' => $log->received_at->format('H:i:s')
                ];
            });
    }
}