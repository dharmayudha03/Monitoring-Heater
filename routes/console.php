<?php

use Illuminate\Support\Facades\Schedule;
use App\Services\FirebaseSyncService;

// Task scheduler otomatis berjalan 24/7 di server tanpa perlu browser dibuka
Schedule::call(function () {
    app(FirebaseSyncService::class)->syncFromFirebase();
})->everyMinute();

// Kirim reminder otomatis setiap 30 MENIT SEKALI untuk heater ber-status DANGER / WARNING yang belum diganti
Schedule::command('heater:send-danger-reminders')->everyThirtyMinutes();
