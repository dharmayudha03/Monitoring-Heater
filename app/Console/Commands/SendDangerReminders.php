<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Heater;
use App\Models\HeaterLog;
use App\Models\Setting;
use App\Services\TelegramService;
use Carbon\Carbon;

class SendDangerReminders extends Command
{
    protected $signature = 'heater:send-danger-reminders';
    protected $description = 'Kirim reminder notifikasi Telegram setiap 30 menit untuk Heater ber-status DANGER/WARNING yang belum diganti';

    public function handle(TelegramService $telegramService)
    {
        $this->info('Memeriksa heater ber-status DANGER / WARNING yang belum diganti...');

        $dangerHeaters = Heater::where('is_active', true)
            ->whereIn('last_status', ['DANGER', 'WARNING'])
            ->get();

        if ($dangerHeaters->isEmpty()) {
            $this->info('Tidak ada heater dengan status DANGER / WARNING saat ini.');
            return 0;
        }

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

        $count = 0;
        foreach ($dangerHeaters as $heater) {
            $log = $heater->latest_log;

            // Cari log paling awal saat status heater mulai menjadi DANGER/WARNING secara berurutan
            $firstAnomalyLog = HeaterLog::where('heater_id', $heater->id)
                ->whereIn('status', ['DANGER', 'WARNING'])
                ->where('id', '>=', function($query) use ($heater) {
                    $query->select('id')
                        ->from('heater_logs')
                        ->where('heater_id', $heater->id)
                        ->whereNotIn('status', ['DANGER', 'WARNING'])
                        ->orderBy('id', 'desc')
                        ->limit(1);
                })
                ->orderBy('id', 'asc')
                ->first() ?: $log;

            $startDate = $firstAnomalyLog && $firstAnomalyLog->received_at 
                ? Carbon::parse($firstAnomalyLog->received_at) 
                : Carbon::now();

            $sinceFormatted = $startDate->format('d-m-Y H:i:s');
            $hoursDiff = max(0, $startDate->diffInHours(now()));
            $daysDiff = (int) floor($hoursDiff / 24);
            $remainingHours = $hoursDiff % 24;

            $durationStr = $daysDiff > 0 
                ? "{$daysDiff} Hari {$remainingHours} Jam" 
                : "{$hoursDiff} Jam";

            $statusIcon = $log->status === 'DANGER' ? '🚨 DANGER' : '⚠️ WARNING';
            $actionTip = $log->status === 'DANGER' 
                ? "Arus terdegradasi parah di bawah {$settings->warning_min}A! Unit Heater PERLU SEGERA DIGANTI." 
                : "Arus di bawah ambang normal ({$settings->normal_min}A). Mohon lakukan inspeksi unit.";

            $msg = "{$statusIcon} <b>REMINDER MAINTENANCE (30 MENIT)</b>\n\n"
                . "Kode Heater: <b>{$heater->heater_code}</b> ({$heater->heater_name})\n"
                . "Zona: <b>{$heater->zone}</b>\n"
                . "Status Saat Ini: <b>{$log->status} ({$log->current} A)</b>\n"
                . "Arus Nominal: 10.93 A\n\n"
                . "📅 <b>Status {$log->status} Sejak:</b> {$sinceFormatted}\n"
                . "⏱️ <b>Durasi Belum Diganti:</b> {$durationStr}\n\n"
                . "⚠️ <i>{$actionTip} Mohon tim Engineering segera memproses penggantian di halaman Monitoring Heater web!</i>";

            $telegramService->sendMessage($msg, $heater->id);
            $count++;
        }

        $this->info("Berhasil mengirim {$count} reminder notifikasi ke Telegram Group!");
        return 0;
    }
}
