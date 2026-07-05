<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HeaterController;

Route::prefix('v1')->group(function () {

    Route::get('/heaters', [HeaterController::class, 'getAll']);
    Route::get('/heaters/chart-data', [HeaterController::class, 'chartData']);
    Route::get('/heaters/alerts', [HeaterController::class, 'latestAlerts']);
    Route::get('/heaters/{heater_code}', [HeaterController::class, 'detail']);
    Route::post('/heater', [HeaterController::class, 'store']);
});