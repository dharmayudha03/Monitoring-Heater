<?php

namespace App\Services;

use App\Models\Heater;
use App\Models\HeaterLog;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class FirebaseSyncService
{
    protected string $firebaseUrl = 'https://ctfh-f0c6d-default-rtdb.firebaseio.com/monitoring_heater.json';

    /**
     * Benchmark Spek Heater Rangkaian Delta:
     * 30 Ampere = 1 Volt (1000 mV)
     * Arus Nominal Normal = 10.93 Ampere (364.64 mV)
     */
    public function syncFromFirebase(): array
    {
        // Throttle Firebase sync to at most once every 3 seconds to eliminate cURL latency lag
        if (Cache::has('firebase_sync_lock')) {
            return ['success' => true, 'message' => 'Synced from cache lock'];
        }
        Cache::put('firebase_sync_lock', true, 3);

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
                 * KATEGORI STATUS BERDASARKAN RATIO & SPEK DELTA HEATER (10.93 A):
                 * - NORMAL   : Current >= 8.50 A (Mendekati arus nominal 10.93 A)
                 * - REPLACED : Baru saja diganti (Grace period 15 menit menantikan kenaikan sensor ESP32)
                 * - WARNING  : 5.00 A <= Current < 8.50 A (Penurunan performa elemen)
                 * - DANGER   : Current < 5.00 A (Elemen putus / terdegradasi parah, misal 4.26 A)
                 */
                if ($current >= 8.50) {
                    $status = 'NORMAL';
                } elseif ($isRecentlyReplaced) {
                    $status = 'REPLACED'; // Transisi pemasangan heater baru
                } elseif ($current >= 5.00) {
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
                            ? '<b>PERHATIAN Kritis:</b> Arus terdegradasi di bawah 5A (spek nominal 10.93A). Lakukan penggantian unit pada halaman Monitoring Heater!' 
                            : '<b>PERINGATAN:</b> Arus heater di bawah ambang normal (8.5A). Lakukan inspeksi pada halaman Monitoring Heater.';
                        
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
