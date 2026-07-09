<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ReplacementController;
use App\Http\Controllers\AlertsController;
use App\Http\Controllers\TelegramLogController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;

// Auth Routes (Guest Only)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes (Require Login)
Route::middleware(['auth'])->group(function () {

    // Dashboard (All Roles)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Monitoring Heater (All Roles)
    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');

    // History & Exports (All Roles)
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/history/export/excel', [HistoryController::class, 'exportExcel'])->name('history.export.excel');
    Route::get('/history/export/pdf', [HistoryController::class, 'exportPdf'])->name('history.export.pdf');

    // Alerts (All Roles)
    Route::get('/alerts', [AlertsController::class, 'index'])->name('alerts.index');

    // Reports (All Roles)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');

    // ADMIN ONLY ROUTES
    Route::middleware([\App\Http\Middleware\AdminMiddleware::class])->group(function () {
        // Replacement
        Route::get('/replacement', [ReplacementController::class, 'index'])->name('replacement.index');
        Route::post('/replacement', [ReplacementController::class, 'store'])->name('replacement.store');
        Route::get('/replacement/export/excel', [ReplacementController::class, 'exportExcel'])->name('replacement.export.excel');
        Route::get('/replacement/export/pdf', [ReplacementController::class, 'exportPdf'])->name('replacement.export.pdf');

        // Telegram Logs & Live Send Test
        Route::get('/telegram-logs', [TelegramLogController::class, 'index'])->name('telegram.index');
        Route::post('/telegram-logs/test', [TelegramLogController::class, 'sendTest'])->name('telegram.test');
        Route::post('/telegram-logs/remind-danger', [TelegramLogController::class, 'sendDangerReminders'])->name('telegram.remind_danger');

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/system', [SettingController::class, 'updateSystem'])->name('settings.system.update');
        Route::post('/settings/calibrate', [SettingController::class, 'calibrateSensor'])->name('settings.calibrate');

        // SUPER ADMIN ONLY: User & Password Management
        Route::middleware([\App\Http\Middleware\SuperAdminMiddleware::class])->group(function () {
            Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
            Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
            Route::put('/users/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
            Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
        });
    });
});