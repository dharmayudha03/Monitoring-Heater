<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseSyncService;

class SyncAndNotifyCommand extends Command
{
    protected $signature = 'heater:sync';
    protected $description = 'Background task untuk sinkronisasi data ESP32 Firebase & kirim alert Telegram 24/7 tanpa butuh browser dibuka';

    public function handle(FirebaseSyncService $firebaseSyncService)
    {
        $this->info('Memulai sinkronisasi otomatis dari Firebase ESP32...');
        $result = $firebaseSyncService->syncFromFirebase();
        
        if ($result['success']) {
            $this->info('Sinkronisasi Berhasil: ' . ($result['message'] ?? 'OK'));
        } else {
            $this->error('Sinkronisasi Gagal: ' . ($result['message'] ?? 'Error'));
        }

        return 0;
    }
}
