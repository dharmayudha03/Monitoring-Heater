<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHeaterLogRequest;
use App\Services\HeaterService;
use App\Services\FirebaseSyncService;
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

    public function getAll()
    {
        // Auto-sync live ESP32 sensor readings from Firebase
        try {
            app(FirebaseSyncService::class)->syncFromFirebase();
        } catch (\Exception $e) {}

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
}