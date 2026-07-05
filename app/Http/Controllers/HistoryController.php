<?php

namespace App\Http\Controllers;

use App\Models\Heater;
use App\Models\HeaterLog;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = HeaterLog::with('heater')->orderBy('received_at', 'desc');

        if ($request->filled('heater_code')) {
            $query->whereHas('heater', function ($q) use ($request) {
                $q->where('heater_code', $request->heater_code);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('received_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('received_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(15)->withQueryString();
        $heaters = Heater::orderBy('heater_code')->get();

        return view('history.index', compact('logs', 'heaters'));
    }

    public function exportExcel(Request $request)
    {
        $query = HeaterLog::with('heater')->orderBy('received_at', 'desc');

        if ($request->filled('heater_code')) {
            $query->whereHas('heater', function ($q) use ($request) {
                $q->where('heater_code', $request->heater_code);
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('received_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('received_at', '<=', $request->date_to);
        }

        $logs = $query->get();
        $format = $request->input('format', 'excel');

        if ($format === 'excel') {
            $filename = "history_logs_" . date('Y-m-d_H-i-s') . ".xlsx";
            $headers = ['No', 'Waktu Log', 'Kode Heater', 'Nama Heater', 'Zona', 'Current (A)', 'Status'];

            $rows = [];
            foreach ($logs as $index => $log) {
                $rows[] = [
                    $index + 1,
                    $log->received_at ? $log->received_at->format('d-m-Y H:i:s') : '-',
                    $log->heater->heater_code ?? '-',
                    $log->heater->heater_name ?? '-',
                    $log->heater->zone ?? '-',
                    number_format($log->current, 2),
                    $log->status
                ];
            }

            return \App\Helpers\SimpleXLSXWriter::download(
                $filename,
                $headers,
                $rows,
                'LAPORAN HISTORI MONITORING HEATER'
            );
        } else {
            // CSV format
            $filename = "history_logs_" . date('Y-m-d_H-i-s') . ".csv";
            $headers = [
                "Content-Type" => "text/csv; charset=UTF-8",
                "Content-Disposition" => "attachment; filename=\"$filename\"",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            ];

            $columns = ['No', 'Waktu Log', 'Kode Heater', 'Nama Heater', 'Zona', 'Current (A)', 'Status'];

            $callback = function () use ($logs, $columns) {
                $file = fopen('php://output', 'w');
                fputs($file, "\xEF\xBB\BF"); // Add UTF-8 BOM for Excel compatibility
                fputcsv($file, $columns);

                foreach ($logs as $index => $log) {
                    fputcsv($file, [
                        $index + 1,
                        $log->received_at ? $log->received_at->format('d-m-Y H:i:s') : '-',
                        $log->heater->heater_code ?? '-',
                        $log->heater->heater_name ?? '-',
                        $log->heater->zone ?? '-',
                        $log->current,
                        $log->status
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }
    }

    public function exportPdf(Request $request)
    {
        $query = HeaterLog::with('heater')->orderBy('received_at', 'desc');

        if ($request->filled('heater_code')) {
            $query->whereHas('heater', function ($q) use ($request) {
                $q->where('heater_code', $request->heater_code);
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('received_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('received_at', '<=', $request->date_to);
        }

        $logs = $query->get();

        return view('history.pdf', compact('logs'));
    }
}
