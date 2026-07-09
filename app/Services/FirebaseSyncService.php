<?php

namespace App\Services;

use App\Models\Heater;
use App\Models\HeaterLog;
use App\Models\Setting;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class FirebaseSyncService
{
    protected string $firebaseUrl = 'https://ctfh-f0c6d-default-rtdb.firebaseio.com/monitoring_heater.json';

    public function syncFromFirebase(): array
    {
        // Throttle Firebase sync to at most once every 3 seconds to eliminate cURL latency lag
        if (Cache::has('firebase_sync_lock')) {
            return ['success' => true, 'message' => 'Synced from cache lock'];
        }
        Cache::put('firebase_sync_lock', true, 3);

        // Get system settings for thresholds (Normal, Warning, Danger)
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

        try {
            $response = Http::withoutVerifying()->timeout(2)->get($this->firebaseUrl);

            if (!$response->successful()) {
                return ['success' => false, 'message' => 'Gagal mengambil data dari Firebase: ' . $response->status()];
            }

            $firebaseData = $response->json();
            if (empty($firebaseData)) {
                return ['success' => false, 'message' => 'Data Firebase kosong.'];
            }

            $activeCodes = [];
            $syncedCount = 0;
            $telegramService = app(TelegramService::class);

            foreach ($firebaseData as $key => $node) {
                // Key format: ct1, ct2, ct3 ... -> CT01, CT02, CT03 ...
                $num = (int) preg_replace('/[^0-9]/', '', $key);
                if (empty($num)) continue;

                $paddedNum = str_pad($num, 2, '0', STR_PAD_LEFT);
                $heaterCode = 'CT' . $paddedNum;
                $heaterName = 'Heater ' . $paddedNum;
                $zoneName = $node['zona'] ?? "Zone {$paddedNum}";
                $current = (float) ($node['arus'] ?? 0);

                $activeCodes[] = $heaterCode;

                $heater = Heater::where('heater_code', $heaterCode)->first();
                if (!$heater) {
                    $heater = Heater::create([
                        'heater_code' => $heaterCode,
                        'heater_name' => $heaterName,
                        'zone' => $zoneName,
                        'is_active' => true,
                    ]);
                } else {
                    $heater->update([
                        'heater_name' => $heaterName,
                        'zone' => $zoneName,
                        'is_active' => true,
                    ]);
                }

                $latestLog = $heater->latestLog;
                $previousStatus = $latestLog ? $latestLog->status : 'NORMAL';

                // Check Grace Period (15 Menit setelah penggantian di web)
                $latestReplacement = $heater->latestReplacement;
                $isRecentlyReplaced = false;
                if ($latestReplacement && $latestReplacement->replacement_date) {
                    $minutesDiff = Carbon::parse($latestReplacement->replacement_date)->diffInMinutes(now());
                    if ($minutesDiff <= 15) {
                        $isRecentlyReplaced = true;
                    }
                }

                /**
                 * KATEGORI STATUS BERDASARKAN PARAMETER THRESHOLD DARI DATABASE:
                 * - NORMAL   : Current >= normal_min (e.g. 9.0 A)
                 * - REPLACED : Baru saja diganti (Grace period 15 menit)
                 * - WARNING  : warning_min (7.6 A) <= Current < normal_min (9.0 A)
                 * - DANGER   : Current < warning_min (7.6 A)
                 */
                if ($current >= $settings->normal_min) {
                    $status = 'NORMAL';
                } elseif ($isRecentlyReplaced) {
                    $status = 'REPLACED'; 
                } elseif ($current >= $settings->warning_min) {
                    $status = 'WARNING';
                } else {
                    $status = 'DANGER';
                }

                // Create log if value changed or > 10s elapsed to keep DB optimized
                if (!$latestLog || abs($latestLog->current - $current) > 0.01 || $status !== $previousStatus || Carbon::parse($latestLog->received_at)->diffInSeconds(now()) >= 10) {
                    $log = HeaterLog::create([
                        'heater_id' => $heater->id,
                        'current' => $current,
                        'voltage' => null,
                        'temperature' => null,
                        'status' => $status,
                        'received_at' => Carbon::now(),
                    ]);
                } else {
                    $log = $latestLog;
                }

                $syncedCount++;

                // Trigger Automatic Real-Time Telegram Alert for WARNING or DANGER (skip if REPLACED)
                if (in_array($status, ['WARNING', 'DANGER']) && $status !== $previousStatus && $status !== 'REPLACED') {
                    try {
                        $icon = $status === 'DANGER' ? '🚨 DANGER' : '⚠️ WARNING';
                        $actionMsg = $status === 'DANGER' 
                            ? "<b>PERHATIAN Kritis:</b> Arus terdegradasi di bawah {$settings->warning_min}A (spek nominal 10.93A). Lakukan penggantian unit pada halaman Monitoring Heater!" 
                            : "<b>PERINGATAN:</b> Arus heater di bawah ambang normal ({$settings->normal_min}A). Lakukan inspeksi pada halaman Monitoring Heater.";
                        
                        $msg = "{$icon} <b>DETEKSI SENSOR ESP32: STATUS {$status}</b>\n\n"
                             . "Kode Heater: <b>{$heater->heater_code}</b> ({$heater->heater_name})\n"
                             . "Zona: <b>{$heater->zone}</b>\n"
                             . "Arus (ESP32): <b>{$log->current} A</b> (Nominal: 10.93 A)\n"
                             . "Waktu: " . now()->format('d-m-Y H:i:s') . "\n\n"
                             . "{$actionMsg}";
                        
                        $telegramService->sendMessage($msg, $heater->id);
                    } catch (\Exception $e) {}
                }
            }

            // Non-aktifkan heater lain yang tidak ada di data Firebase
            Heater::whereNotIn('heater_code', $activeCodes)->update(['is_active' => false]);

            return [
                'success' => true,
                'message' => "Berhasil me-sinkronisasi {$syncedCount} sensor ESP32 dari Firebase!",
                'data' => $firebaseData
            ];

        } catch (\Exception $e) {
            Log::error('Firebase Sync Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}
