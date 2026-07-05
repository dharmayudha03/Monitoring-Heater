<?php

namespace App\Http\Controllers;

use App\Models\Heater;
use App\Models\HeaterLog;
use App\Models\Replacement;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $data = $this->getReportData();
        return view('reports.index', $data);
    }

    private function getReportData()
    {
        $totalHeaters = Heater::where('is_active', true)->count();
        $totalLogs = HeaterLog::count();
        $totalReplacements = Replacement::count();

        $normalLogsCount = HeaterLog::where('status', 'NORMAL')->count();
        $warningLogsCount = HeaterLog::where('status', 'WARNING')->count();
        $dangerLogsCount = HeaterLog::where('status', 'DANGER')->count();

        // 1. All Active Heaters Summary with latest log & latest replacement
        $heatersSummary = Heater::where('is_active', true)
            ->with(['latestLog', 'latestReplacement'])
            ->withCount(['replacements as total_replacements'])
            ->withCount([
                'logs as danger_alerts_count' => function ($q) {
                    $q->where('status', 'DANGER');
                }
            ])
            ->withCount([
                'logs as warning_alerts_count' => function ($q) {
                    $q->where('status', 'WARNING');
                }
            ])
            ->orderBy('heater_code')
            ->get();

        // 2. Top Problematic Heaters (Ranked by replacements count & danger alerts)
        $topProblematicHeaters = Heater::where('is_active', true)
            ->withCount(['replacements as total_replacements'])
            ->withCount([
                'logs as danger_alerts_count' => function ($q) {
                    $q->where('status', 'DANGER');
                }
            ])
            ->withCount([
                'logs as warning_alerts_count' => function ($q) {
                    $q->where('status', 'WARNING');
                }
            ])
            ->orderBy('total_replacements', 'desc')
            ->orderBy('danger_alerts_count', 'desc')
            ->get();

        // 3. Zone Breakdown
        $zoneBreakdown = Heater::where('is_active', true)
            ->get()
            ->groupBy('zone')
            ->map(function ($items, $zone) {
                $heaterIds = $items->pluck('id');
                return (object) [
                    'zone' => $zone,
                    'total_heaters' => $items->count(),
                    'total_replacements' => Replacement::whereIn('heater_id', $heaterIds)->count(),
                    'total_danger' => HeaterLog::whereIn('heater_id', $heaterIds)->where('status', 'DANGER')->count(),
                    'total_warning' => HeaterLog::whereIn('heater_id', $heaterIds)->where('status', 'WARNING')->count(),
                ];
            })->values();

        // 4. Recent Replacement Audit Trail
        $recentReplacements = Replacement::with('heater')
            ->orderBy('replacement_date', 'desc')
            ->limit(10)
            ->get();

        return compact(
            'totalHeaters',
            'totalLogs',
            'totalReplacements',
            'normalLogsCount',
            'warningLogsCount',
            'dangerLogsCount',
            'heatersSummary',
            'topProblematicHeaters',
            'zoneBreakdown',
            'recentReplacements'
        );
    }

    public function exportExcel(Request $request)
    {
        $data = $this->getReportData();
        $format = $request->input('format', 'excel');

        // Prepare Table 1: Status Performansi & Keandalan Elemen Heater
        $rowsT1 = [];
        foreach ($data['heatersSummary'] as $index => $hs) {
            $log = $hs->latestLog;
            $rowsT1[] = [
                $index + 1,
                $hs->heater_code,
                $hs->heater_name,
                $hs->zone,
                $log ? number_format($log->current, 2) . ' A' : '-',
                $log ? $log->status : 'OFFLINE',
                $hs->total_replacements,
                $hs->danger_alerts_count
            ];
        }

        // Prepare Table 2: Breakdown Performansi Per Zona Produksi
        $rowsT2 = [];
        foreach ($data['zoneBreakdown'] as $index => $zb) {
            $rowsT2[] = [
                $index + 1,
                $zb->zone,
                $zb->total_heaters . ' Unit',
                $zb->total_replacements . ' Kali',
                $zb->total_danger . ' Event',
                $zb->total_warning . ' Event'
            ];
        }

        // Prepare Table 3: Top Problematic Heaters
        $rowsT3 = [];
        foreach ($data['topProblematicHeaters'] as $index => $tph) {
            $rowsT3[] = [
                $index + 1,
                $tph->heater_code,
                $tph->heater_name,
                $tph->zone,
                $tph->total_replacements . ' Kali',
                $tph->danger_alerts_count . ' Event',
                $tph->warning_alerts_count . ' Event'
            ];
        }

        // Prepare Table 4: Riwayat Replacement Audit Trail
        $rowsT4 = [];
        foreach ($data['recentReplacements'] as $index => $rep) {
            $rowsT4[] = [
                $index + 1,
                $rep->replacement_date ? $rep->replacement_date->format('d-m-Y H:i') : '-',
                $rep->heater->heater_code ?? '-',
                $rep->heater->heater_name ?? '-',
                $rep->heater->zone ?? '-',
                $rep->old_heater_code . ' -> ' . $rep->new_heater_code,
                $rep->replaced_by,
                $rep->reason
            ];
        }

        $sections = [
            [
                'section_title' => '1. STATUS PERFORMANSI & KEANDALAN ELEMEN HEATER',
                'headers' => ['No', 'Kode Heater', 'Nama Heater', 'Zona', 'Arus Terakhir (A)', 'Status Kesehatan', 'Total Replacement', 'Total Alert Danger'],
                'rows' => $rowsT1
            ],
            [
                'section_title' => '2. BREAKDOWN PERFORMANSI PER ZONA PRODUKSI',
                'headers' => ['No', 'Nama Zona', 'Total Heater Unit', 'Total Replacement', 'Total Event Danger', 'Total Event Warning'],
                'rows' => $rowsT2
            ],
            [
                'section_title' => '3. ANALISIS HEATER DENGAN FREKUENSI KERUSAKAN TINGGI (TOP PROBLEMATIC)',
                'headers' => ['No', 'Kode Heater', 'Nama Heater', 'Zona', 'Total Replacement', 'Alert Danger', 'Alert Warning'],
                'rows' => $rowsT3
            ],
            [
                'section_title' => '4. RIWAYAT MAINTENANCE & PENGGANTIAN HEATER (AUDIT TRAIL)',
                'headers' => ['No', 'Waktu Penggantian', 'Kode Heater', 'Nama Heater', 'Zona', 'Kode Lama -> Baru', 'Teknisi / Petugas', 'Alasan Penggantian'],
                'rows' => $rowsT4
            ]
        ];

        if ($format === 'excel') {
            $filename = "laporan_performansi_heater_" . date('Y-m-d_H-i-s') . ".xlsx";
            return \App\Helpers\SimpleXLSXWriter::downloadMultiTable(
                $filename,
                $sections,
                'LAPORAN EXECUTIVE PERFORMA & KEANDALAN HEATER'
            );
        } else {
            // CSV format
            $filename = "laporan_performansi_heater_" . date('Y-m-d_H-i-s') . ".csv";
            $headers = [
                "Content-Type" => "text/csv; charset=UTF-8",
                "Content-Disposition" => "attachment; filename=\"$filename\"",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            ];

            $callback = function () use ($sections) {
                $file = fopen('php://output', 'w');
                fputs($file, "\xEF\xBB\BF"); // Add UTF-8 BOM for Excel compatibility

                fputcsv($file, ['LAPORAN EXECUTIVE PERFORMA & KEANDALAN HEATER']);
                fputcsv($file, ['PT IRC INOAC INDONESIA - Tanggal: ' . date('d-m-Y H:i:s')]);
                fputcsv($file, []);

                foreach ($sections as $sec) {
                    fputcsv($file, [$sec['section_title']]);
                    fputcsv($file, $sec['headers']);
                    foreach ($sec['rows'] as $r) {
                        fputcsv($file, $r);
                    }
                    fputcsv($file, []);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getReportData();
        return view('reports.pdf', $data);
    }
}
