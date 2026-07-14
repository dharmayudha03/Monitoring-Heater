<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Heater;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class CheckConnectionCommand extends Command
{
    protected $signature = 'heater:check-connection';
    protected $description = 'Periksa status koneksi ESP32 (Watchdog) dan kirim alert Telegram jika offline';

    public function handle(TelegramService $telegramService)
    {
        $this->info('Memeriksa status koneksi ESP32...');

        $latestActivity = Heater::where('is_active', true)->max('last_received_at');

        if (!$latestActivity) {
            $this->info('Belum ada aktivitas data heater di database.');
            return 0;
        }

        $lastTime = Carbon::parse($latestActivity);
        $minutesDiff = $lastTime->diffInMinutes(now());

        $this->info('Aktivitas terakhir: ' . $lastTime->format('d-m-Y H:i:s') . ' (' . $minutesDiff . ' menit yang lalu)');

        // Jika tidak ada data masuk selama lebih dari 12 menit (ESP32 terputus)
        if ($minutesDiff >= 12) {
            $hasAlerted = Cache::get('esp32_offline_alert_sent', false);

            if (!$hasAlerted) {
                $msg = "🚨 <b>KONEKSI TERPUTUS (ESP32 OFFLINE)</b>\n\n"
                     . "Perangkat monitoring IoT Tungyu Heater terdeteksi <b>OFFLINE</b> / terputus!\n"
                     . "Sistem tidak menerima data telemetri selama lebih dari 12 menit.\n\n"
                     . "📅 <b>Data Terakhir Diterima:</b> " . $lastTime->format('d-m-Y H:i:s') . " (" . $minutesDiff . " menit yang lalu)\n\n"
                     . "⚠️ <i>Mohon periksa koneksi Wi-Fi di area produksi atau catu daya perangkat ESP32!</i>";

                $telegramService->sendMessage($msg);
                Cache::forever('esp32_offline_alert_sent', true);
                
                $this->warn('Alert Telegram ESP32 Offline telah dikirim!');
            } else {
                $this->info('ESP32 masih offline, alert sudah pernah dikirim.');
            }
        } else {
            // Jika koneksi online kembali, periksa apakah sebelumnya pernah mengirim alert offline
            $wasAlerted = Cache::get('esp32_offline_alert_sent', false);

            if ($wasAlerted) {
                $msg = "🟢 <b>KONEKSI PULIH (ESP32 ONLINE)</b>\n\n"
                     . "Perangkat monitoring IoT Tungyu Heater kini telah terhubung kembali (<b>ONLINE</b>)!\n"
                     . "Aliran data telemetri bulk dilanjutkan dengan sukses.\n\n"
                     . "📅 <b>Waktu Pulih:</b> " . now()->format('d-m-Y H:i:s') . "\n\n"
                     . "✅ <i>Sistem monitoring berjalan normal kembali. Terima kasih!</i>";

                $telegramService->sendMessage($msg);
                Cache::forget('esp32_offline_alert_sent');
                
                $this->info('Alert Telegram ESP32 Online (Pulih) telah dikirim!');
            } else {
                $this->info('Koneksi ESP32 berjalan normal.');
            }
        }

        return 0;
    }
}
