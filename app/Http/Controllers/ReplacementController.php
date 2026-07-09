<?php

namespace App\Http\Controllers;

use App\Models\Heater;
use App\Models\HeaterLog;
use App\Models\Replacement;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReplacementController extends Controller
{
    public function index()
    {
        $replacements = Replacement::with('heater')->orderBy('replacement_date', 'desc')->paginate(10);

        $dangerHeaters = Heater::where('is_active', true)->with('latestLog')->get()->filter(function ($h) {
            return $h->latest_log && $h->latest_log->status === 'DANGER';
        });

        $allHeaters = Heater::where('is_active', true)->orderBy('heater_code')->get();

        return view('replacement.index', compact('replacements', 'dangerHeaters', 'allHeaters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'heater_id' => 'required|exists:heaters,id',
            'replaced_unit' => 'required|string|in:Heater 1,Heater 2,Kedua Heater (Heater 1 & 2)',
            'reason' => 'nullable|string',
            'replaced_by' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $heater = Heater::findOrFail($request->heater_id);
        $oldCode = $request->input('old_heater_code', $heater->heater_code . '-OLD');
        $newCode = $request->input('new_heater_code', $heater->heater_code);

        $notes = "Unit Diganti: " . $request->replaced_unit;
        if ($request->notes) {
            $notes .= " | " . $request->notes;
        }

        $replacement = Replacement::create([
            'heater_id' => $heater->id,
            'old_heater_code' => $oldCode,
            'new_heater_code' => $newCode,
            'reason' => $request->reason ?? 'Status DANGER - Penggantian Unit Heater',
            'replaced_by' => $request->replaced_by,
            'replacement_date' => Carbon::now(),
            'notes' => $notes,
        ]);

        if ($newCode !== $heater->heater_code) {
            $heater->update(['heater_code' => $newCode]);
        }

        HeaterLog::create([
            'heater_id' => $heater->id,
            'adc_value' => 1000,
            'current' => 10.93,
            'voltage' => null,
            'temperature' => null,
            'status' => 'NORMAL',
            'received_at' => Carbon::now(),
        ]);

        // Send Telegram Notification
        try {
            $telegramService = app(TelegramService::class);
            $msg = "🛠️ <b>PENGGANTIAN HEATER SUKSES</b>\n\nKode Heater: <b>{$heater->heater_code}</b>\nUnit Diganti: <b>{$request->replaced_unit}</b>\nTeknisi: {$request->replaced_by}\nAlasan: " . ($request->reason ?: 'Penggantian Elemen Heater') . "\nStatus: NORMAL (10.93 A)";
            $telegramService->sendMessage($msg, $heater->id);
        } catch (\Exception $e) {
        }

        return redirect()->back()->with('success', "Heater {$heater->heater_code} ({$request->replaced_unit}) berhasil diganti dan status dikembalikan ke NORMAL!");
    }

    public function exportExcel(Request $request)
    {
        $replacements = Replacement::with('heater')->orderBy('replacement_date', 'desc')->get();
        $format = $request->input('format', 'excel');

        if ($format === 'excel') {
            $filename = "laporan_replacement_heater_" . date('Y-m-d_H-i-s') . ".xlsx";
            $headers = ['No', 'Tanggal Penggantian', 'Kode Heater', 'Nama Heater', 'Zona', 'Kode Lama', 'Kode Baru', 'Teknisi / Petugas', 'Alasan Penggantian', 'Catatan Maintenance'];

            $rows = [];
            foreach ($replacements as $index => $rep) {
                $rows[] = [
                    $index + 1,
                    $rep->replacement_date ? $rep->replacement_date->format('d-m-Y H:i') : '-',
                    $rep->heater->heater_code ?? '-',
                    $rep->heater->heater_name ?? '-',
                    $rep->heater->zone ?? '-',
                    $rep->old_heater_code,
                    $rep->new_heater_code,
                    $rep->replaced_by,
                    $rep->reason,
                    $rep->notes ?: '-'
                ];
            }

            return \App\Helpers\SimpleXLSXWriter::download(
                $filename,
                $headers,
                $rows,
                'LAPORAN RIWAYAT PENGGANTIAN HEATER (REPLACEMENT LOGS)'
            );
        } else {
            // CSV format
            $filename = "laporan_replacement_heater_" . date('Y-m-d_H-i-s') . ".csv";
            $headers = [
                "Content-Type" => "text/csv; charset=UTF-8",
                "Content-Disposition" => "attachment; filename=\"$filename\"",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            ];

            $columns = ['No', 'Tanggal Penggantian', 'Kode Heater', 'Nama Heater', 'Zona', 'Kode Lama', 'Kode Baru', 'Teknisi / Petugas', 'Alasan Penggantian', 'Catatan Maintenance'];

            $callback = function () use ($replacements, $columns) {
                $file = fopen('php://output', 'w');
                fputs($file, "\xEF\xBB\BF"); // Add UTF-8 BOM for Excel compatibility
                fputcsv($file, $columns);

                foreach ($replacements as $index => $rep) {
                    fputcsv($file, [
                        $index + 1,
                        $rep->replacement_date ? $rep->replacement_date->format('d-m-Y H:i') : '-',
                        $rep->heater->heater_code ?? '-',
                        $rep->heater->heater_name ?? '-',
                        $rep->heater->zone ?? '-',
                        $rep->old_heater_code,
                        $rep->new_heater_code,
                        $rep->replaced_by,
                        $rep->reason,
                        $rep->notes ?: '-'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }
    }

    public function exportPdf(Request $request)
    {
        $replacements = Replacement::with('heater')->orderBy('replacement_date', 'desc')->get();
        return view('replacement.pdf', compact('replacements'));
    }
}
