@extends('layouts.admin')

@section('page_title', 'Laporan Performansi & Analytics')

@section('content')
    <div class="content-wrapper" style="background-color: #F4F7F6;">
        <section class="content pt-4">
            <div class="container-fluid">

                <!-- Page Header & Export Action Buttons (Fixed 1-Row Layout) -->
                <div class="d-flex align-items-center justify-content-between mb-4 flex-nowrap"
                    style="gap: 12px; min-height: 48px;">
                    <div style="flex: 1; min-width: 0;">
                        <h5 class="font-weight-bold text-dark mb-1 text-truncate"
                            style="font-size: clamp(14px, 1.2vw, 18px);">Laporan Executive & Keandalan Heater</h5>
                        <p class="text-muted small mb-0 text-truncate" style="font-size: clamp(10px, 0.9vw, 12px);">Analisis
                            statistik performansi, tingkat kerusakan, dan riwayat maintenance unit heater.</p>
                    </div>
                    <div class="d-flex align-items-center flex-nowrap" style="gap: 6px; flex-shrink: 0;">
                        <button type="button" class="btn btn-sm btn-success rounded-pill font-weight-bold shadow-sm"
                            data-toggle="modal" data-target="#modalPreviewExcelReport"
                            style="white-space: nowrap; font-size: 11px; padding: 5px 12px;">
                            <i class="fas fa-file-excel mr-1"></i>
                            <span class="d-none d-md-inline">Preview & Export Dataset</span>
                            <span class="d-inline d-md-none">Excel</span>
                        </button>
                        <a href="{{ route('reports.export.pdf', ['action' => 'print']) }}" target="_blank"
                            class="btn btn-sm btn-primary rounded-pill font-weight-bold shadow-sm"
                            style="white-space: nowrap; font-size: 11px; padding: 5px 12px;">
                            <i class="fas fa-print mr-1"></i>
                            <span class="d-none d-md-inline">Cetak PDF</span>
                            <span class="d-inline d-md-none">Cetak</span>
                        </a>
                        <a href="{{ route('reports.export.pdf', ['action' => 'download']) }}" target="_blank"
                            class="btn btn-sm btn-danger rounded-pill font-weight-bold shadow-sm"
                            style="white-space: nowrap; font-size: 11px; padding: 5px 12px;">
                            <i class="fas fa-file-download mr-1"></i>
                            <span class="d-none d-md-inline">Download PDF</span>
                            <span class="d-inline d-md-none">PDF</span>
                        </a>
                    </div>
                </div>

                <!-- Summary KPI Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card shadow-sm border-0 rounded-lg p-3 bg-white h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted font-weight-bold text-uppercase" style="font-size:11px;">Total Unit
                                    Heater Active</small>
                                <span class="badge badge-primary px-2 py-1"><i class="fas fa-fire"></i></span>
                            </div>
                            <h3 class="font-weight-bold text-dark my-1">{{ $totalHeaters }} <span
                                    class="small font-weight-normal text-muted" style="font-size: 14px;">Unit</span></h3>
                            <small class="text-success font-weight-600"><i class="fas fa-check-circle mr-1"></i> 100%
                                Terpasang & Monitoring</small>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card shadow-sm border-0 rounded-lg p-3 bg-white h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted font-weight-bold text-uppercase" style="font-size:11px;">Total
                                    Penggantian (Replacement)</small>
                                <span class="badge badge-danger px-2 py-1"><i class="fas fa-tools"></i></span>
                            </div>
                            <h3 class="font-weight-bold text-danger my-1">{{ $totalReplacements }} <span
                                    class="small font-weight-normal text-muted" style="font-size: 14px;">Kali</span></h3>
                            <small class="text-muted font-weight-600"><i class="fas fa-history mr-1"></i> Akumulasi
                                Maintenance Unit</small>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card shadow-sm border-0 rounded-lg p-3 bg-white h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted font-weight-bold text-uppercase" style="font-size:11px;">Alert
                                    Anomali (Danger & Warning)</small>
                                <span class="badge badge-warning px-2 py-1"><i
                                        class="fas fa-exclamation-triangle text-dark"></i></span>
                            </div>
                            <h3 class="font-weight-bold text-warning my-1">{{ $warningLogsCount + $dangerLogsCount }} <span
                                    class="small font-weight-normal text-muted" style="font-size: 14px;">Event</span></h3>
                            <small class="text-danger font-weight-600"><i class="fas fa-bell mr-1"></i>
                                {{ $dangerLogsCount }} Danger | {{ $warningLogsCount }} Warning</small>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card shadow-sm border-0 rounded-lg p-3 bg-white h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted font-weight-bold text-uppercase" style="font-size:11px;">Total Log
                                    Data Terbaca</small>
                                <span class="badge badge-info px-2 py-1"><i class="fas fa-database"></i></span>
                            </div>
                            <h3 class="font-weight-bold text-primary my-1">{{ number_format($totalLogs) }} <span
                                    class="small font-weight-normal text-muted" style="font-size: 14px;">Log</span></h3>
                            <small class="text-muted font-weight-600"><i class="fas fa-wifi mr-1"></i> Realtime Sensor
                                ESP32</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- 1. Top Problematic Heaters (Peringkat Kerusakan & Replacement) -->
                    <div class="col-lg-7 mb-4">
                        <div class="card shadow-sm border-0 rounded-lg h-100">
                            <div
                                class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                                <h6 class="font-weight-bold text-dark mb-0">
                                    <i class="fas fa-trophy text-warning mr-2"></i> Peringkat Frekuensi Kerusakan &
                                    Replacement
                                </h6>
                                <span class="badge badge-light text-muted font-weight-normal">Analisis Keandalan</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover text-center align-middle mb-0" style="font-size: 13px;">
                                    <thead class="bg-light text-muted">
                                        <tr>
                                            <th class="py-2 border-0">Rank</th>
                                            <th class="py-2 border-0">Kode Heater</th>
                                            <th class="py-2 border-0">Nama Heater</th>
                                            <th class="py-2 border-0">Zona</th>
                                            <th class="py-2 border-0">Total Replacement</th>
                                            <th class="py-2 border-0">Danger Alert</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($topProblematicHeaters as $index => $tph)
                                            @php
                                                $rankBadge = 'badge-secondary';
                                                if ($index == 0 && $tph->total_replacements > 0) {
                                                    $rankBadge = 'badge-danger';
                                                } elseif ($index == 1 && $tph->total_replacements > 0) {
                                                    $rankBadge = 'badge-warning';
                                                } elseif ($index == 2 && $tph->total_replacements > 0) {
                                                    $rankBadge = 'badge-info';
                                                }
                                            @endphp
                                            <tr>
                                                <td>
                                                    <span class="badge {{ $rankBadge }} px-2 py-1 font-weight-bold">
                                                        #{{ $index + 1 }}
                                                    </span>
                                                </td>
                                                <td class="font-weight-bold text-dark">{{ $tph->heater_code }}</td>
                                                <td>{{ $tph->heater_name }}</td>
                                                <td>{{ $tph->zone }}</td>
                                                <td>
                                                    <span
                                                        class="font-weight-bold {{ $tph->total_replacements > 0 ? 'text-danger' : 'text-success' }}">
                                                        {{ $tph->total_replacements }}x Diganti
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="font-weight-bold {{ $tph->danger_alerts_count > 0 ? 'text-danger' : 'text-muted' }}">
                                                        {{ $tph->danger_alerts_count }} Event
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Zone Reliability Breakdown (Analisis Per Zona) -->
                    <div class="col-lg-5 mb-4">
                        <div class="card shadow-sm border-0 rounded-lg h-100">
                            <div class="card-header bg-white border-bottom-0 py-3">
                                <h6 class="font-weight-bold text-dark mb-0">
                                    <i class="fas fa-layer-group text-primary mr-2"></i> Rekap Keandalan Per Zona
                                    Operasional
                                </h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    @foreach ($zoneBreakdown as $zb)
                                        <div class="list-group-item p-3 border-light">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="font-weight-bold text-dark mb-0">
                                                    <i class="fas fa-industry text-secondary mr-2"></i>
                                                    {{ $zb->zone }}
                                                </h6>
                                                <span
                                                    class="badge badge-light px-3 py-1 border font-weight-bold">{{ $zb->total_heaters }}
                                                    Unit Heater</span>
                                            </div>
                                            <div class="row text-center mt-3">
                                                <div class="col-6 border-right">
                                                    <small class="text-muted d-block" style="font-size: 11px;">Total
                                                        Replacement</small>
                                                    <strong class="text-danger font-weight-bold"
                                                        style="font-size: 16px;">{{ $zb->total_replacements }}x</strong>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted d-block" style="font-size: 11px;">Event
                                                        Danger</small>
                                                    <strong class="text-warning font-weight-bold"
                                                        style="font-size: 16px;">{{ $zb->total_danger }} Event</strong>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Main Performance & Health Table -->
                <div class="card shadow-sm border-0 rounded-lg mb-4">
                    <div class="card-header bg-white border-bottom-0 py-3">
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fas fa-heartbeat text-danger mr-2"></i> Tabel Kesehatan & Performa Seluruh Heater
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover text-center align-middle mb-0" style="font-size: 13px;">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th class="py-3 border-0">Kode Heater</th>
                                    <th class="py-3 border-0">Nama Heater</th>
                                    <th class="py-3 border-0">Zona</th>
                                    <th class="py-3 border-0">Nilai Arus Terakhir</th>
                                    <th class="py-3 border-0">Status Kesehatan</th>
                                    <th class="py-3 border-0">Penggantian Terakhir</th>
                                    <th class="py-3 border-0">Rekomendasi Maintenance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($heatersSummary as $hs)
                                    @php
                                        $log = $hs->latestLog;
                                        $status = $log ? $log->status : 'OFFLINE';
                                        $badge = 'success';
                                        if ($status === 'WARNING') {
                                            $badge = 'warning';
                                        }
                                        if ($status === 'DANGER') {
                                            $badge = 'danger';
                                        }
                                        if ($status === 'OFFLINE') {
                                            $badge = 'secondary';
                                        }

                                        $lastRep = $hs->latestReplacement;
                                        $lastRepStr = $lastRep
                                            ? $lastRep->replacement_date->format('d-m-Y H:i')
                                            : 'Belum Ada';
                                    @endphp
                                    <tr>
                                        <td class="font-weight-bold text-dark">{{ $hs->heater_code }}</td>
                                        <td>{{ $hs->heater_name }}</td>
                                        <td>{{ $hs->zone }}</td>
                                        <td>{{ $log ? number_format($log->current, 2) . ' A' : '-' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $badge }} px-3 py-1 font-weight-bold">
                                                {{ $status }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="font-weight-bold text-muted"><i
                                                    class="far fa-calendar-alt mr-1"></i> {{ $lastRepStr }}</small>
                                        </td>
                                        <td class="text-left">
                                            @if ($status === 'DANGER')
                                                <span class="text-danger font-weight-bold"><i
                                                        class="fas fa-tools mr-1"></i> Perlu Penggantian Unit Segera</span>
                                            @elseif($status === 'WARNING')
                                                <span class="text-warning font-weight-bold"><i
                                                        class="fas fa-search mr-1"></i> Inspeksi Modul & Sensor</span>
                                            @else
                                                <span class="text-success"><i class="fas fa-check mr-1"></i> Kondisi
                                                    Prima</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 4. Recent Replacement Audit Trail Table -->
                <div class="card shadow-sm border-0 rounded-lg mb-4">
                    <div
                        class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fas fa-history text-info mr-2"></i> Riwayat Audit Penggantian Heater Terakhir
                            (Replacement Logs)
                        </h6>
                        <small class="text-muted">10 Penggantian Terakhir</small>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover text-center align-middle mb-0" style="font-size: 13px;">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th class="py-3 border-0">Waktu Penggantian</th>
                                    <th class="py-3 border-0">Kode Heater</th>
                                    <th class="py-3 border-0">Kode Lama & Baru</th>
                                    <th class="py-3 border-0">Teknisi / Petugas</th>
                                    <th class="py-3 border-0">Alasan Penggantian</th>
                                    <th class="py-3 border-0">Catatan Maintenance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($recentReplacements) > 0)
                                    @foreach ($recentReplacements as $rep)
                                        <tr>
                                            <td>
                                                <span class="font-weight-bold text-dark">
                                                    {{ $rep->replacement_date ? $rep->replacement_date->format('d-m-Y H:i') : '-' }}
                                                </span>
                                            </td>
                                            <td class="font-weight-bold text-primary">
                                                {{ $rep->heater->heater_code ?? '-' }}</td>
                                            <td>
                                                <span
                                                    class="badge badge-light border px-2 py-1">{{ $rep->old_heater_code }}
                                                    &rarr; {{ $rep->new_heater_code }}</span>
                                            </td>
                                            <td>
                                                <span class="font-weight-bold text-dark"><i
                                                        class="fas fa-user-circle text-secondary mr-1"></i>
                                                    {{ $rep->replaced_by }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge badge-warning px-2 py-1 font-weight-bold">{{ $rep->reason }}</span>
                                            </td>
                                            <td class="text-left text-muted" style="max-width: 250px;">
                                                {{ $rep->notes ?: '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="py-4 text-muted">Belum ada riwayat penggantian heater
                                            yang tercatat.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </section>
    </div>

    <!-- Modal Preview Excel & CSV Report -->
    <div class="modal fade" id="modalPreviewExcelReport" tabindex="-1" role="dialog"
        aria-labelledby="modalPreviewExcelReportLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content rounded-lg border-0 shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold" id="modalPreviewExcelReportLabel">
                        <i class="fas fa-file-excel mr-2"></i> Preview & Export Dataset Laporan (Excel / CSV)
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">

                    <!-- Format Choice -->
                    <div class="card border mb-3 bg-light">
                        <div class="card-body p-3">
                            <label class="font-weight-bold text-dark small mb-2"><i
                                    class="fas fa-cog text-primary mr-1"></i> Pilih Format File Yang Diinginkan:</label>
                            <div class="d-flex align-items-center">
                                <div class="custom-control custom-radio mr-4">
                                    <input type="radio" id="reportFormatExcel" name="report_export_format"
                                        value="excel" class="custom-control-input" checked>
                                    <label class="custom-control-label font-weight-bold text-success"
                                        for="reportFormatExcel" style="cursor: pointer;">
                                        <i class="fas fa-file-excel fa-lg mr-1"></i> Microsoft Excel (.xlsx)
                                    </label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="reportFormatCsv" name="report_export_format"
                                        value="csv" class="custom-control-input">
                                    <label class="custom-control-label font-weight-bold text-info" for="reportFormatCsv"
                                        style="cursor: pointer;">
                                        <i class="fas fa-file-csv fa-lg mr-1"></i> CSV File (.csv)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="font-weight-bold text-muted small mb-2">Prinjau Data Ringkasan Performa Heater:</p>
                    <div class="table-responsive border rounded mb-3" style="max-height: 220px;">
                        <table class="table table-sm table-striped text-center mb-0" style="font-size: 11px;">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama Heater</th>
                                    <th>Zona</th>
                                    <th>Arus Terakhir (A)</th>
                                    <th>Status</th>
                                    <th>Total Replacement</th>
                                    <th>Danger Alert</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($heatersSummary as $index => $hs)
                                    @php
                                        $log = $hs->latestLog;
                                        $status = $log ? $log->status : 'OFFLINE';
                                        $badge = 'success';
                                        if ($status === 'WARNING') {
                                            $badge = 'warning';
                                        }
                                        if ($status === 'DANGER') {
                                            $badge = 'danger';
                                        }
                                        if ($status === 'OFFLINE') {
                                            $badge = 'secondary';
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="font-weight-bold">{{ $hs->heater_code }}</td>
                                        <td>{{ $hs->heater_name }}</td>
                                        <td>{{ $hs->zone }}</td>
                                        <td>{{ $log ? number_format($log->current, 2) . ' A' : '-' }}</td>
                                        <td><span class="badge badge-{{ $badge }}">{{ $status }}</span></td>
                                        <td>{{ $hs->total_replacements }}x</td>
                                        <td class="text-danger font-weight-bold">{{ $hs->danger_alerts_count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4"
                        data-dismiss="modal">Batal</button>
                    <button type="button" id="btnConfirmReportDownload"
                        class="btn btn-success rounded-pill px-4 font-weight-bold">
                        <i class="fas fa-download mr-1"></i> Download File
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setExportFormat(format) {
            if (format === 'excel') {
                document.getElementById('reportFormatExcel').checked = true;
            } else if (format === 'csv') {
                document.getElementById('reportFormatCsv').checked = true;
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const btnConfirm = document.getElementById('btnConfirmReportDownload');
            if (btnConfirm) {
                btnConfirm.addEventListener('click', () => {
                    const format = document.querySelector('input[name="report_export_format"]:checked')
                        .value;
                    let url = "{{ route('reports.export.excel') }}";
                    if (format === 'csv') {
                        url += "?format=csv";
                    }
                    window.location.href = url;
                    $('#modalPreviewExcelReport').modal('hide');
                });
            }
        });
    </script>
@endsection
