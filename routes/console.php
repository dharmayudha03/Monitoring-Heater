<?php

use Illuminate\Support\Facades\Schedule;

// Kirim reminder otomatis setiap 30 MENIT SEKALI untuk heater ber-status DANGER / WARNING yang belum diganti
Schedule::command('heater:send-danger-reminders')->everyThirtyMinutes();

// Cek koneksi ESP32 (Watchdog) setiap 5 menit
Schedule::command('heater:check-connection')->everyFiveMinutes();
