<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>
        {{ request('action') === 'download' ? 'Laporan_Replacement_Heater_' . date('Y-m-d') : 'Laporan Replacement Heater - PT IRC INOAC Indonesia' }}
    </title>
    <!-- Font Awesome (Local 100% Offline) -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- html2pdf.js for Direct PDF File Download (Local 100% Offline) -->
    <script src="{{ asset('plugins/html2pdf/html2pdf.bundle.min.js') }}"></script>

    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333333;
            margin: 0;
            padding: 12mm 15mm;
            background-color: #ffffff;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .title {
            font-size: 15px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: -0.3px;
        }

        .subtitle {
            font-size: 10px;
            color: #64748b;
            margin-top: 3px;
            font-weight: 500;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            color: #fff;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-danger {
            background-color: #dc3545;
        }

        .badge-secondary {
            background-color: #6c757d;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            text-align: center;
        }

        table.data-table th {
            background-color: #f8fafc;
            font-weight: bold;
            color: #334155;
        }

        .summary-box {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            background-color: #fafafa;
        }

        .summary-val {
            font-size: 20px;
            font-weight: bold;
            color: #0284c7;
            margin-top: 5px;
        }

        /* Action Toolbar (Sticky at Top on Scroll) */
        .action-toolbar {
            position: sticky;
            top: 10px;
            z-index: 9999;
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .btn-action {
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-print {
            background-color: #0284c7;
            color: #ffffff;
        }

        .btn-print:hover {
            background-color: #0369a1;
        }

        .btn-download {
            background-color: #059669;
            color: #ffffff;
        }

        .btn-download:hover {
            background-color: #047857;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            .no-print {
                display: none !important;
            }

            body {
                margin: 0 !important;
                padding: 12mm 15mm !important;
            }
        }
    </style>
</head>

<body>

    <!-- Action Toolbar (Visible on Screen, Hidden when Printing) -->
    <div class="no-print action-toolbar">
        <div>
            <strong style="font-size: 13px; color: #0f172a;"><i class="fas fa-file-pdf text-danger mr-1"></i> Preview
                Laporan PDF Replacement Heater</strong>
            <span style="color: #64748b; font-size: 11px; margin-left: 8px;">Pilih tindakan cetak atau langsung download
                file PDF:</span>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="fas fa-print"></i> Cetak Dokumen
            </button>
            <button onclick="triggerDownload()" class="btn-action btn-download">
                <i class="fas fa-download"></i> Download PDF Langsung
            </button>
        </div>
    </div>

    <!-- Printable Report Container -->
    <div id="report-document" style="width: 100%; box-sizing: border-box;">
        <!-- Header with Top-Left IRC Logo -->
        <table class="header-table">
            <tr>
                <td style="text-align: left; border: none; vertical-align: middle;">
                    <table style="border: none; width: 100%;">
                        <tr>
                            <td style="width: 60px; border: none; vertical-align: middle;">
                                <img src="{{ asset('images/logo.png') }}" alt="PT IRC INOAC Indonesia"
                                    style="max-height: 48px; width: auto; object-fit: contain;">
                            </td>
                            <td style="border: none; text-align: left; vertical-align: middle; padding-left: 12px;">
                                <div class="title">LAPORAN RIWAYAT PENGGANTIAN HEATER (REPLACEMENT LOGS)</div>
                                <div class="subtitle">PT IRC INOAC INDONESIA — AUTOMOTIVE & INDUSTRIAL RUBBER PARTS
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Ringkasan Kunci (3 Box Sejajar dalam 1 Baris HTML Table - Fixed) -->
        <table
            style="width: 100%; border-collapse: separate; border-spacing: 10px 0; margin-bottom: 20px; table-layout: fixed;">
            <tr>
                <td class="summary-box" style="width: 33.33%;">
                    <div style="font-size: 10px; color: #64748b; font-weight: bold; text-transform: uppercase;">Total
                        Transaksi Replacement</div>
                    <div class="summary-val" style="color: #dc3545;">{{ $replacements->count() }} Kali</div>
                </td>
                <td class="summary-box" style="width: 33.33%;">
                    <div style="font-size: 10px; color: #64748b; font-weight: bold; text-transform: uppercase;">Unit
                        Heater Diganti</div>
                    <div class="summary-val" style="color: #0284c7;">
                        {{ $replacements->pluck('heater_id')->unique()->count() }} Unit</div>
                </td>
                <td class="summary-box" style="width: 33.33%;">
                    <div style="font-size: 10px; color: #64748b; font-weight: bold; text-transform: uppercase;">Status
                        Maintenance</div>
                    <div class="summary-val" style="color: #28a745;">COMPLETED</div>
                </td>
            </tr>
        </table>

        <h4 style="margin-bottom: 8px; color: #0f172a;">1. DAFTAR RIWAYAT PENGGANTIAN ELEMEN HEATER</h4>
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Waktu Penggantian</th>
                    <th>Kode Heater</th>
                    <th>Nama Heater</th>
                    <th>Zona</th>
                    <th>Kode Lama &rarr; Baru</th>
                    <th>Teknisi / Petugas</th>
                    <th>Alasan Penggantian</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($replacements as $idx => $rep)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ $rep->replacement_date ? $rep->replacement_date->format('d-m-Y H:i') : '-' }}</td>
                        <td style="font-weight: bold;">{{ $rep->heater->heater_code ?? '-' }}</td>
                        <td>{{ $rep->heater->heater_name ?? '-' }}</td>
                        <td>{{ $rep->heater->zone ?? '-' }}</td>
                        <td><span class="badge badge-secondary">{{ $rep->old_heater_code }} &rarr;
                                {{ $rep->new_heater_code }}</span></td>
                        <td>{{ $rep->replaced_by }}</td>
                        <td style="text-align: left;">{{ $rep->reason }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 35px; text-align: left; color: #64748b; font-size: 10px; font-weight: 500;">
            Laporan Riwayat Penggantian Heater - PT IRC INOAC Indonesia
        </div>
    </div>

    <!-- Direct Download & Auto Print Handler -->
    <script>
        function triggerDownload() {
            const element = document.getElementById('report-document');
            const filename = "Laporan_Replacement_Heater_{{ date('Y-m-d_H-i') }}.pdf";

            const opt = {
                margin: [10, 10, 10, 10],
                filename: filename,
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2,
                    useCORS: true,
                    logging: false
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                }
            };

            html2pdf().set(opt).from(element).save();
        }

        @if (request('action') === 'download')
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    triggerDownload();
                }, 400);
            });
        @elseif (request('action') === 'print')
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    window.print();
                }, 400);
            });
        @endif
    </script>
</body>

</html>
