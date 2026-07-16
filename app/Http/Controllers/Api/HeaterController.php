<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHeaterLogRequest;
use App\Services\HeaterService;
use Illuminate\Http\JsonResponse;

class HeaterController extends Controller
{
    protected HeaterService $heaterService;

    public function __construct(HeaterService $heaterService)
    {
        $this->heaterService = $heaterService;
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'HeaterController OK'
        ]);
    }

    public function store(StoreHeaterLogRequest $request): JsonResponse
    {
        $result = $this->heaterService->store(
            $request->validated()
        );

        return ApiResponse::success(
            $result,
            'Data berhasil disimpan'
        );
    }

    public function bulkStore(\Illuminate\Http\Request $request): JsonResponse
    {
        $data = $request->validate([
            'logs' => 'required|array',
            'logs.*.heater_code' => 'required|string',
            'logs.*.current' => 'required|numeric|min:0',
            'logs.*.voltage' => 'nullable|numeric',
            'logs.*.temperature' => 'nullable|numeric',
            'logs.*.adc_value' => 'nullable|integer',
        ]);

        $results = [];
        foreach ($data['logs'] as $logData) {
            $results[] = $this->heaterService->store($logData);
        }

        return ApiResponse::success(
            $results,
            'Data bulk berhasil disimpan'
        );
    }

    public function wifiStatus(\Illuminate\Http\Request $request): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|string|in:CONNECTED,DISCONNECTED'
        ]);

        $status = $data['status'];
        
        if ($status === 'CONNECTED') {
            $wasAlerted = \Illuminate\Support\Facades\Cache::get('esp32_offline_alert_sent', false);
            if ($wasAlerted) {
                $msg = "🟢 <b>KONEKSI PULIH (ESP32 ONLINE)</b>\n\n"
                     . "Perangkat monitoring IoT Tungyu Heater kini telah terhubung kembali (<b>ONLINE</b>)!\n"
                     . "Aliran data telemetri bulk dilanjutkan dengan sukses.\n\n"
                     . "📅 <b>Waktu Pulih:</b> " . now()->format('d-m-Y H:i:s') . "\n\n"
                     . "✅ <i>Sistem monitoring berjalan normal kembali. Terima kasih!</i>";

                app(\App\Services\TelegramService::class)->sendMessage($msg);
                \Illuminate\Support\Facades\Cache::forget('esp32_offline_alert_sent');
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status WiFi berhasil dicatat'
        ]);
    }

    public function getAll()
    {
        $data = $this->heaterService->getAllHeaters();

        return ApiResponse::success(
            $data,
            'Data heater berhasil diambil'
        );
    }

    public function detail($heater_code)
    {
        $data = $this->heaterService->getDetailHeater($heater_code);

        return ApiResponse::success(
            $data,
            'Detail heater berhasil diambil'
        );
    }

    public function chartData(\Illuminate\Http\Request $request)
    {
        $data = $this->heaterService->getChartData($request);

        return ApiResponse::success(
            $data,
            'Data grafik berhasil diambil'
        );
    }

    public function latestAlerts()
    {
        $data = $this->heaterService->getLatestAlerts();

        return ApiResponse::success(
            $data,
            'Data alert terbaru berhasil diambil'
        );
    }

    public function getSystemConfig()
    {
        $settings = \App\Models\Setting::first() ?: \App\Models\Setting::create([
            'normal_min' => 9.00,
            'warning_min' => 7.60,
            'm_ct1' => 1.425,
            'm_ct2' => 1.467,
            'm_ct3' => 1.297,
            'm_ct4' => 1.192,
            'm_ct5' => 1.372,
            'm_ct6' => 1.157,
            'upper_baseline' => 11.00,
            'lower_baseline' => 11.00,
            'telegram_enabled' => true,
            'sampling_interval' => 5
        ]);

        return response()->json([
            'normal_min' => (float)$settings->normal_min,
            'warning_min' => (float)$settings->warning_min,
            'upper_baseline' => (float)$settings->upper_baseline,
            'lower_baseline' => (float)$settings->lower_baseline,
            'm_ct1' => (float)$settings->m_ct1,
            'm_ct2' => (float)$settings->m_ct2,
            'm_ct3' => (float)$settings->m_ct3,
            'm_ct4' => (float)$settings->m_ct4,
            'm_ct5' => (float)$settings->m_ct5,
            'm_ct6' => (float)$settings->m_ct6,
        ]);
    }
}