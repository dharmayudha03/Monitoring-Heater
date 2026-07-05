@extends('layouts.admin')

@section('page_title', 'Dashboard')

@section('content')
    <div class="content-wrapper" style="background-color: #F4F7F6;">
        <section class="content pt-4">
            <div class="container-fluid">

                <!-- Page Header (Fixed 1-Row Layout) -->
                <div class="d-flex align-items-center justify-content-between mb-4 flex-nowrap"
                    style="gap: 12px; min-height: 48px;">
                    <div style="flex: 1; min-width: 0;">
                        <h5 class="font-weight-bold text-dark mb-1 text-truncate"
                            style="font-size: clamp(14px, 1.2vw, 18px);">Dashboard Real-Time Monitoring</h5>
                        <p class="text-muted small mb-0 text-truncate" style="font-size: clamp(10px, 0.9vw, 12px);">Ringkasan
                            status performansi elemen heater, log arus listrik, dan alert sistem (Data Live ESP32 Sensor).
                        </p>
                    </div>
                </div>

                <!-- 5 Summary KPI Cards (Fixed 1-Row Layout, Never Wraps) -->
                <div class="d-flex flex-nowrap mb-4" style="gap: 12px; overflow-x: auto;">
                    <!-- Total Heater -->
                    <div style="flex: 1; min-width: 170px;">
                        <div class="card shadow-sm border-0 h-100 rounded-lg">
                            <div class="card-body d-flex align-items-center p-3">
                                <div class="bg-primary rounded text-white d-flex align-items-center justify-content-center mr-3"
                                    style="width: 44px; height: 44px; flex-shrink: 0;">
                                    <i class="fas fa-th-large fa-lg"></i>
                                </div>
                                <div style="min-width: 0;">
                                    <small class="text-muted font-weight-bold text-uppercase d-block text-truncate"
                                        style="font-size: 10px;">Total Heater</small>
                                    <h3 class="mb-0 font-weight-bold" id="total-heater"
                                        style="color: #2c3e50; font-size: 18px;">0</h3>
                                    <small class="text-muted d-block text-truncate" style="font-size: 10px;">
                                        Aktif: <span id="heater-active">0</span> | Non: <span id="heater-inactive">0</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Normal -->
                    <div style="flex: 1; min-width: 170px;">
                        <div class="card shadow-sm border-0 h-100 rounded-lg">
                            <div class="card-body d-flex align-items-center p-3">
                                <div class="bg-success rounded text-white d-flex align-items-center justify-content-center mr-3"
                                    style="width: 44px; height: 44px; flex-shrink: 0;">
                                    <i class="fas fa-check-circle fa-lg"></i>
                                </div>
                                <div style="min-width: 0;">
                                    <small class="text-muted font-weight-bold text-uppercase d-block text-truncate"
                                        style="font-size: 10px;">Normal</small>
                                    <h3 class="mb-0 font-weight-bold" id="normal-count"
                                        style="color: #2c3e50; font-size: 18px;">0</h3>
                                    <small class="text-muted font-weight-bold d-block text-truncate" id="normal-pct"
                                        style="font-size: 10px;">0%</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Warning -->
                    <div style="flex: 1; min-width: 170px;">
                        <div class="card shadow-sm border-0 h-100 rounded-lg">
                            <div class="card-body d-flex align-items-center p-3">
                                <div class="bg-warning rounded text-white d-flex align-items-center justify-content-center mr-3"
                                    style="width: 44px; height: 44px; flex-shrink: 0;">
                                    <i class="fas fa-exclamation-circle fa-lg"></i>
                                </div>
                                <div style="min-width: 0;">
                                    <small class="text-muted font-weight-bold text-uppercase d-block text-truncate"
                                        style="font-size: 10px;">Warning</small>
                                    <h3 class="mb-0 font-weight-bold text-dark" id="warning-count"
                                        style="color: #2c3e50; font-size: 18px;">0</h3>
                                    <small class="text-muted font-weight-bold d-block text-truncate" id="warning-pct"
                                        style="font-size: 10px;">0%</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Danger -->
                    <div style="flex: 1; min-width: 170px;">
                        <div class="card shadow-sm border-0 h-100 rounded-lg">
                            <div class="card-body d-flex align-items-center p-3">
                                <div class="bg-danger rounded text-white d-flex align-items-center justify-content-center mr-3"
                                    style="width: 44px; height: 44px; flex-shrink: 0;">
                                    <i class="fas fa-exclamation-triangle fa-lg"></i>
                                </div>
                                <div style="min-width: 0;">
                                    <small class="text-muted font-weight-bold text-uppercase d-block text-truncate"
                                        style="font-size: 10px;">Danger</small>
                                    <h3 class="mb-0 font-weight-bold" id="danger-count"
                                        style="color: #2c3e50; font-size: 18px;">0</h3>
                                    <small class="text-muted font-weight-bold d-block text-truncate" id="danger-pct"
                                        style="font-size: 10px;">0%</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Update Terakhir -->
                    <div style="flex: 1; min-width: 170px;">
                        <div class="card shadow-sm border-0 h-100 rounded-lg">
                            <div class="card-body d-flex align-items-center p-3">
                                <div class="bg-light rounded text-muted d-flex align-items-center justify-content-center mr-3"
                                    style="width: 44px; height: 44px; flex-shrink: 0;">
                                    <i class="far fa-clock fa-lg"></i>
                                </div>
                                <div style="min-width: 0;">
                                    <small class="text-muted font-weight-bold text-uppercase d-block text-truncate"
                                        style="font-size: 10px;">Update Terakhir</small>
                                    <h3 class="mb-0 font-weight-bold text-truncate" id="last-update-time"
                                        style="color: #2c3e50; font-size: 16px;">-</h3>
                                    <small class="text-muted d-block text-truncate" id="last-update-date"
                                        style="font-size: 10px;">-</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= ROW 2: STATUS HEATER TERBARU (TABLE) ================= --}}
                <div class="card shadow-sm border-0 rounded-lg mb-4">
                    <div
                        class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                        <h6 class="font-weight-bold mb-0" style="color: #2c3e50;">Status Heater Terbaru</h6>
                        <div>
                            <button class="btn btn-sm btn-outline-secondary mr-2 rounded-pill px-3" id="btn-refresh">
                                <i class="fas fa-sync-alt mr-1"></i> Refresh
                            </button>
                            <select class="form-control form-control-sm d-inline-block rounded-pill" style="width:100px">
                                <option>5 detik</option>
                                <option>10 detik</option>
                                <option>30 detik</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover text-center mb-0 align-middle" style="font-size: 13px;">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th class="border-0 font-weight-600 py-3">No</th>
                                    <th class="border-0 font-weight-600 py-3">Heater Code</th>
                                    <th class="border-0 font-weight-600 py-3">Heater Name</th>
                                    <th class="border-0 font-weight-600 py-3">Zone</th>
                                    <th class="border-0 font-weight-600 py-3">Current (A)</th>
                                    <th class="border-0 font-weight-600 py-3">Status</th>
                                    <th class="border-0 font-weight-600 py-3">Update Terakhir</th>
                                </tr>
                            </thead>
                            <tbody id="heaterTable">
                                <tr>
                                    <td colspan="7" class="py-5 text-muted">Memuat data monitoring...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white border-top-0 py-3 d-flex justify-content-between align-items-center">
                        <small class="text-muted" id="pagination-info" style="font-size:12px;">Menampilkan
                            data...</small>
                        <ul class="pagination pagination-sm m-0" id="pagination-container">
                            <!-- Pagination digenerate oleh JS -->
                        </ul>
                    </div>
                </div>

                {{-- ================= ROW 3: GRAFIK ARUS (FULL WIDTH - BESAR & PROMINENT) ================= --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm border-0 rounded-lg">
                            <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center flex-nowrap"
                                style="gap: 12px;">
                                <div style="flex: 1; min-width: 0;">
                                    <h6 class="font-weight-bold mb-0 text-truncate" style="color: #2c3e50;"><i
                                            class="fas fa-chart-line text-primary mr-2"></i> Grafik Arus Real-Time (Ampere)
                                    </h6>
                                    <small class="text-muted d-block text-truncate">Tren pengukuran arus listrik (Ampere)
                                        per unit heater secara real-time.</small>
                                </div>
                                <!-- Compact Filter Controls -->
                                <div class="d-flex align-items-center flex-nowrap" style="flex-shrink: 0;">
                                    <span class="small font-weight-bold text-muted mr-2 d-none d-sm-inline"><i
                                            class="fas fa-filter text-primary mr-1"></i> Filter:</span>

                                    <!-- Main Filter Dropdown -->
                                    <select id="mainFilter"
                                        class="form-control form-control-sm rounded-pill border shadow-sm mr-2"
                                        style="width: 135px; font-size: 11px; height: 30px; font-weight: 600; background-color: #f8f9fa;">
                                        <option value="realtime" selected>⚡ Live (5 Mnt)</option>
                                        <option value="shift">🕒 Shift Kerja</option>
                                        <option value="daily">📅 Harian (Hari Ini)</option>
                                        <option value="monthly">📈 Bulanan</option>
                                    </select>

                                    <!-- Sub Filter for Shift (Visible when mainFilter == 'shift') -->
                                    <select id="shiftSubFilter"
                                        class="form-control form-control-sm rounded-pill border shadow-sm mr-2 d-none"
                                        style="width: 155px; font-size: 11px; height: 30px; font-weight: 600; background-color: #e0f2fe; color: #0369a1;">
                                        <option value="shift1" selected>Shift 1 (07:00 - 15:00)</option>
                                        <option value="shift2">Shift 2 (15:00 - 23:00)</option>
                                        <option value="shift3">Shift 3 (23:00 - 07:00)</option>
                                    </select>

                                    <!-- Sub Filter for Month (Visible when mainFilter == 'monthly') -->
                                    <select id="monthSubFilter"
                                        class="form-control form-control-sm rounded-pill border shadow-sm d-none"
                                        style="width: 130px; font-size: 11px; height: 30px; font-weight: 600; background-color: #f0fdf4; color: #15803d;">
                                        <option value="1">Januari</option>
                                        <option value="2">Februari</option>
                                        <option value="3">Maret</option>
                                        <option value="4">April</option>
                                        <option value="5">Mei</option>
                                        <option value="6">Juni</option>
                                        <option value="7">Juli</option>
                                        <option value="8">Agustus</option>
                                        <option value="9">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-body">
                                <div style="position: relative; height: 360px; width: 100%;">
                                    <canvas id="currentChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= ROW 4: STATUS HEATER (DONUT) & ALERT TERBARU ================= --}}
                <div class="row">
                    <!-- Status Heater (Donut) -->
                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100 rounded-lg">
                            <div class="card-header bg-white py-3 border-bottom-0">
                                <h6 class="font-weight-bold mb-0" style="color: #2c3e50;"><i
                                        class="fas fa-chart-pie text-info mr-2"></i> Status Heater (Ringkasan)</h6>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <div style="position: relative; height: 200px; width: 200px;">
                                    <canvas id="statusChart"></canvas>
                                </div>
                                <div id="statusLegend" class="mt-4 w-100 px-2" style="font-size: 13px;">
                                    <!-- Legend digenerate oleh JS -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alert Terbaru -->
                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100 rounded-lg">
                            <div
                                class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                                <h6 class="font-weight-bold mb-0" style="color: #2c3e50;"><i
                                        class="fas fa-bell text-warning mr-2"></i> Alert Terbaru</h6>
                                <a href="{{ url('/alerts') }}"
                                    class="text-primary font-weight-600 text-decoration-none small">Lihat Semua Alert <i
                                        class="fas fa-angle-right ml-1"></i></a>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush" id="recentAlertsList">
                                    <li class="list-group-item text-center text-muted py-4 border-0">
                                        Memuat alert...
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
