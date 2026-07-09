@extends('layouts.admin')

@section('page_title', 'History Log')

@section('content')
    <div class="content-wrapper" style="background-color: #F4F7F6;">
        <section class="content pt-4">
            <div class="container-fluid">

                <!-- Page Header & Export Action Buttons (Fixed 1-Row Layout) -->
                <div class="d-flex align-items-center justify-content-between mb-4 flex-nowrap"
                    style="gap: 12px; min-height: 48px;">
                    <div style="flex: 1; min-width: 0;">
                        <h5 class="font-weight-bold text-dark mb-1 text-truncate"
                            style="font-size: clamp(14px, 1.2vw, 18px);">Riwayat Logs Monitoring</h5>
                        <p class="text-muted small mb-0 text-truncate" style="font-size: clamp(10px, 0.9vw, 12px);">Arsip
                            data histori pengukuran sensor arus heater (Data Live ESP32 Sensor).</p>
                    </div>
                    <div class="d-flex align-items-center flex-nowrap" style="gap: 6px; flex-shrink: 0;">
                        <button type="button" class="btn btn-sm btn-success rounded-pill font-weight-bold shadow-sm"
                            data-toggle="modal" data-target="#modalPreviewExcel"
                            style="white-space: nowrap; font-size: 11px; padding: 5px 12px;">
                            <i class="fas fa-file-excel mr-1"></i>
                            <span class="d-none d-md-inline">Preview & Export Dataset</span>
                            <span class="d-inline d-md-none">Excel</span>
                        </button>
                        <a id="btnHistoryCetakPdf"
                            href="{{ route('history.export.pdf', array_merge(request()->all(), ['action' => 'print'])) }}"
                            target="_blank" class="btn btn-sm btn-primary rounded-pill font-weight-bold shadow-sm"
                            style="white-space: nowrap; font-size: 11px; padding: 5px 12px;">
                            <i class="fas fa-print mr-1"></i>
                            <span class="d-none d-md-inline">Cetak PDF</span>
                            <span class="d-inline d-md-none">Cetak</span>
                        </a>
                        <a id="btnHistoryDownloadPdf"
                            href="{{ route('history.export.pdf', array_merge(request()->all(), ['action' => 'download'])) }}"
                            target="_blank" class="btn btn-sm btn-danger rounded-pill font-weight-bold shadow-sm"
                            style="white-space: nowrap; font-size: 11px; padding: 5px 12px;">
                            <i class="fas fa-file-download mr-1"></i>
                            <span class="d-none d-md-inline">Download PDF</span>
                            <span class="d-inline d-md-none">PDF</span>
                        </a>
                    </div>
                </div>

                <!-- Filter Card -->
                <div class="card shadow-sm border-0 rounded-lg mb-4">
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('history.index') }}" class="form-row align-items-end">
                            <div class="col-md-3 mb-2">
                                <label class="small font-weight-bold text-muted">Filter Heater</label>
                                <select name="heater_code" class="form-control form-control-sm rounded-pill">
                                    <option value="">-- Semua Heater --</option>
                                    @foreach ($heaters as $h)
                                        <option value="{{ $h->heater_code }}"
                                            {{ request('heater_code') == $h->heater_code ? 'selected' : '' }}>
                                            {{ $h->heater_code }} - {{ $h->heater_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-2">
                                <label class="small font-weight-bold text-muted">Filter Status</label>
                                <select name="status" class="form-control form-control-sm rounded-pill">
                                    <option value="">-- Semua Status --</option>
                                    <option value="NORMAL" {{ request('status') == 'NORMAL' ? 'selected' : '' }}>NORMAL
                                    </option>
                                    <option value="WARNING" {{ request('status') == 'WARNING' ? 'selected' : '' }}>WARNING
                                    </option>
                                    <option value="DANGER" {{ request('status') == 'DANGER' ? 'selected' : '' }}>DANGER
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <label class="small font-weight-bold text-muted">Dari Tanggal</label>
                                <input type="date" name="date_from" value="{{ request('date_from') }}"
                                    class="form-control form-control-sm rounded-pill">
                            </div>

                            <div class="col-md-2 mb-2">
                                <label class="small font-weight-bold text-muted">Sampai Tanggal</label>
                                <input type="date" name="date_to" value="{{ request('date_to') }}"
                                    class="form-control form-control-sm rounded-pill">
                            </div>

                            <div class="col-md-2 mb-2">
                                <button type="submit" class="btn btn-sm btn-primary rounded-pill btn-block">
                                    <i class="fas fa-search mr-1"></i> Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- History Table Card -->
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="table-responsive">
                        <table class="table table-hover text-center align-middle mb-0" style="font-size: 13px;">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th class="py-3 border-0">No</th>
                                    <th class="py-3 border-0">Waktu Log</th>
                                    <th class="py-3 border-0">Heater Code</th>
                                    <th class="py-3 border-0">Heater Name</th>
                                    <th class="py-3 border-0">Zone</th>
                                    <th class="py-3 border-0">Current (A)</th>
                                    <th class="py-3 border-0">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $index => $log)
                                    @php
                                        $badge = 'success';
                                        if ($log->status === 'WARNING') {
                                            $badge = 'warning';
                                        }
                                        if ($log->status === 'DANGER') {
                                            $badge = 'danger';
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $logs->firstItem() + $index }}</td>
                                        <td>{{ $log->received_at ? $log->received_at->format('d-m-Y H:i:s') : '-' }}</td>
                                        <td class="font-weight-bold">{{ $log->heater->heater_code ?? '-' }}</td>
                                        <td>{{ $log->heater->heater_name ?? '-' }}</td>
                                        <td>{{ $log->heater->zone ?? '-' }}</td>
                                        <td>{{ number_format($log->current, 2) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $badge }} font-weight-bold px-2 py-1">
                                                {{ $log->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-5 text-muted">Data log history tidak ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($logs->hasPages())
                        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                            <small class="text-muted">Menampilkan {{ $logs->firstItem() }} - {{ $logs->lastItem() }} dari
                                {{ $logs->total() }} data</small>
                            <div>
                                {{ $logs->links() }}
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </section>
    </div>

    <!-- Modal Preview Dataset (Excel / CSV) -->
    <div class="modal fade" id="modalPreviewExcel" tabindex="-1" role="dialog" aria-labelledby="modalPreviewExcelLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content rounded-lg border-0 shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold" id="modalPreviewExcelLabel">
                        <i class="fas fa-file-excel mr-2"></i> Preview & Pilih Format Export Dataset
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <!-- Summary info -->
                    <div class="alert alert-light border mb-4">
                        <div class="row text-center">
                            <div class="col-4 border-right">
                                <small class="text-muted d-block font-weight-bold">TOTAL RECORD</small>
                                <strong class="h6 font-weight-bold text-dark mb-0">{{ $logs->total() }} Data</strong>
                            </div>
                            <div class="col-4 border-right">
                                <small class="text-muted d-block font-weight-bold">FILTER HEATER</small>
                                <strong
                                    class="h6 font-weight-bold text-dark mb-0">{{ request('heater_code') ?: 'Semua Unit' }}</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block font-weight-bold">FILTER STATUS</small>
                                <strong
                                    class="h6 font-weight-bold text-dark mb-0">{{ request('status') ?: 'Semua Status' }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Format Selection -->
                    <div class="card border mb-4 bg-light">
                        <div class="card-body p-3">
                            <label class="font-weight-bold text-dark small mb-2"><i
                                    class="fas fa-cog text-primary mr-1"></i> Pilih Format File Yang Diinginkan:</label>
                            <div class="d-flex align-items-center">
                                <div class="custom-control custom-radio mr-4">
                                    <input type="radio" id="formatExcel" name="export_format_choice" value="excel"
                                        class="custom-control-input" checked>
                                    <label class="custom-control-label font-weight-bold text-success" for="formatExcel"
                                        style="cursor: pointer;">
                                        <i class="fas fa-file-excel fa-lg mr-1"></i> Microsoft Excel (.xlsx)
                                    </label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="formatCsv" name="export_format_choice" value="csv"
                                        class="custom-control-input">
                                    <label class="custom-control-label font-weight-bold text-info" for="formatCsv"
                                        style="cursor: pointer;">
                                        <i class="fas fa-file-csv fa-lg mr-1"></i> CSV File (.csv)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="font-weight-bold text-muted small mb-2">Prinjau Struktur Tabel Data:</p>
                    <div class="table-responsive border rounded" style="max-height: 200px;">
                        <table class="table table-sm table-striped text-center mb-0" style="font-size: 11px;">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>Waktu Log</th>
                                    <th>Kode Heater</th>
                                    <th>Nama Heater</th>
                                    <th>Zona</th>
                                    <th>Current (A)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($logs->take(5) as $i => $l)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $l->received_at ? $l->received_at->format('d-m-Y H:i:s') : '-' }}</td>
                                        <td>{{ $l->heater->heater_code ?? '-' }}</td>
                                        <td>{{ $l->heater->heater_name ?? '-' }}</td>
                                        <td>{{ $l->heater->zone ?? '-' }}</td>
                                        <td>{{ number_format($l->current, 2) }}</td>
                                        <td>{{ $l->status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4"
                        data-dismiss="modal">Batal</button>
                    <button type="button" id="btnConfirmDownload"
                        class="btn btn-success rounded-pill px-4 font-weight-bold">
                        <i class="fas fa-download mr-1"></i> Download File
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Preview PDF -->
    <div class="modal fade" id="modalPreviewPdf" tabindex="-1" role="dialog" aria-labelledby="modalPreviewPdfLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content rounded-lg border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="modalPreviewPdfLabel">
                        <i class="fas fa-file-pdf mr-2"></i> Preview Dokumen Laporan (PDF)
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <!-- Preview Document Box -->
                    <div class="bg-white p-4 border rounded shadow-sm">
                        <div class="d-flex align-items-center pb-3 mb-3 border-bottom"
                            style="gap: 15px; border-bottom: 2px solid #0284c7 !important;">
                            <img src="{{ asset('images/logo.png') }}" alt="PT IRC INOAC Indonesia"
                                style="max-height: 40px; width: auto;">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0">LAPORAN HISTORI MONITORING HEATER</h6>
                                <small class="text-muted">PT IRC INOAC INDONESIA — AUTOMOTIVE & INDUSTRIAL RUBBER
                                    PARTS</small>
                            </div>
                        </div>

                        <div class="table-responsive" style="max-height: 220px;">
                            <table class="table table-sm table-bordered text-center mb-0" style="font-size: 11px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Waktu Log</th>
                                        <th>Kode Heater</th>
                                        <th>Nama Heater</th>
                                        <th>Zona</th>
                                        <th>Current (A)</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($logs->take(5) as $i => $l)
                                        @php
                                            $badge = 'badge-success';
                                            if ($l->status === 'WARNING') {
                                                $badge = 'badge-warning';
                                            }
                                            if ($l->status === 'DANGER') {
                                                $badge = 'badge-danger';
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $l->received_at ? $l->received_at->format('d-m-Y H:i:s') : '-' }}</td>
                                            <td class="font-weight-bold">{{ $l->heater->heater_code ?? '-' }}</td>
                                            <td>{{ $l->heater->heater_name ?? '-' }}</td>
                                            <td>{{ $l->heater->zone ?? '-' }}</td>
                                            <td>{{ number_format($l->current, 2) }} A</td>
                                            <td><span class="badge {{ $badge }}">{{ $l->status }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between mt-4 pt-3 border-top small text-muted">
                            <div>
                                <p class="mb-4">Dicetak Oleh,</p>
                                <strong>( {{ Auth::user()->name ?? 'Operator' }} )</strong>
                            </div>
                            <div class="text-right">
                                <p class="mb-4">Disetujui Manager,</p>
                                <strong>( Manager Production )</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4"
                        data-dismiss="modal">Batal</button>
                    <a href="{{ route('history.export.pdf', array_merge(request()->all(), ['action' => 'print'])) }}"
                        target="_blank" class="btn btn-primary rounded-pill px-3 font-weight-bold">
                        <i class="fas fa-print mr-1"></i> Cetak PDF
                    </a>
                    <a href="{{ route('history.export.pdf', array_merge(request()->all(), ['action' => 'download'])) }}"
                        target="_blank" class="btn btn-danger rounded-pill px-3 font-weight-bold">
                        <i class="fas fa-file-download mr-1"></i> Download PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function getFilterParams() {
            const formEl = document.querySelector('form[action*="history"]');
            if (!formEl) return new URLSearchParams(window.location.search);

            const formData = new FormData(formEl);
            const params = new URLSearchParams();
            for (const [key, value] of formData.entries()) {
                if (value !== '') {
                    params.set(key, value);
                }
            }
            return params;
        }

        function updatePdfExportLinks() {
            const paramsPrint = getFilterParams();
            paramsPrint.set('action', 'print');
            const printUrl = "{{ route('history.export.pdf') }}?" + paramsPrint.toString();

            const paramsDownload = getFilterParams();
            paramsDownload.set('action', 'download');
            const downloadUrl = "{{ route('history.export.pdf') }}?" + paramsDownload.toString();

            const btnPrint = document.getElementById('btnHistoryCetakPdf');
            const btnDownload = document.getElementById('btnHistoryDownloadPdf');
            if (btnPrint) btnPrint.href = printUrl;
            if (btnDownload) btnDownload.href = downloadUrl;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const btnConfirm = document.getElementById('btnConfirmDownload');
            if (btnConfirm) {
                btnConfirm.addEventListener('click', function() {
                    const format = document.querySelector('input[name="export_format_choice"]:checked')
                        .value;
                    const baseUrl = "{{ route('history.export.excel') }}";

                    const params = getFilterParams();
                    params.set('format', format);

                    const downloadUrl = `${baseUrl}?${params.toString()}`;
                    window.location.href = downloadUrl;

                    // Close modal
                    $('#modalPreviewExcel').modal('hide');
                });
            }

            document.querySelectorAll('form[action*="history"] input, form[action*="history"] select').forEach(
            el => {
                el.addEventListener('change', updatePdfExportLinks);
            });
        });
    </script>
@endsection
