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

                <!-- 5 Summary KPI Cards (Responsive layout: 2 cols on mobile, 5 cols on desktop) -->
                <div class="row no-gutters mb-3" style="margin: -4px;">
                    <!-- Total Heater -->
                    <div class="col-6 col-md p-1">
                        <div class="card shadow-sm border-0 h-100 rounded-lg">
                            <div class="card-body d-flex align-items-center p-2 p-lg-3">
                                <div class="bg-primary rounded text-white d-flex align-items-center justify-content-center mr-2 mr-lg-3"
                                    style="width: 38px; height: 38px; flex-shrink: 0;">
                                    <i class="fas fa-th-large fa-sm"></i>
                                </div>
                                <div style="min-width: 0; line-height: 1.2;">
                                    <small class="text-muted font-weight-bold text-uppercase d-block text-truncate"
                                        style="font-size: clamp(9.5px, 0.8vw, 11px);">Total Heater</small>
                                    <h5 class="mb-0 font-weight-bold text-dark text-truncate" id="total-heater"
                                        style="font-size: clamp(15px, 1.35vw, 20px);">0</h5>
                                    <small class="text-muted d-block text-truncate" style="font-size: clamp(9px, 0.75vw, 10px);">
                                        A:<span id="heater-active">0</span> | N:<span id="heater-inactive">0</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Normal -->
                    <div class="col-6 col-md p-1">
                        <div class="card shadow-sm border-0 h-100 rounded-lg">
                            <div class="card-body d-flex align-items-center p-2 p-lg-3">
                                <div class="bg-success rounded text-white d-flex align-items-center justify-content-center mr-2 mr-lg-3"
                                    style="width: 38px; height: 38px; flex-shrink: 0;">
                                    <i class="fas fa-check-circle fa-sm"></i>
                                </div>
                                <div style="min-width: 0; line-height: 1.2;">
                                    <small class="text-muted font-weight-bold text-uppercase d-block text-truncate"
                                        style="font-size: clamp(9.5px, 0.8vw, 11px);">Normal</small>
                                    <h5 class="mb-0 font-weight-bold text-dark text-truncate" id="normal-count"
                                        style="font-size: clamp(15px, 1.35vw, 20px);">0</h5>
                                    <small class="text-muted font-weight-bold d-block text-truncate text-success" id="normal-pct"
                                        style="font-size: clamp(9px, 0.75vw, 10px);">0%</small>
                                  </div>
                              </div>
                          </div>
                      </div>

                    <!-- Warning -->
                    <div class="col-6 col-md p-1">
                        <div class="card shadow-sm border-0 h-100 rounded-lg">
                            <div class="card-body d-flex align-items-center p-2 p-lg-3">
                                <div class="bg-warning rounded text-white d-flex align-items-center justify-content-center mr-2 mr-lg-3"
                                    style="width: 38px; height: 38px; flex-shrink: 0;">
                                    <i class="fas fa-exclamation-circle fa-sm"></i>
                                </div>
                                <div style="min-width: 0; line-height: 1.2;">
                                    <small class="text-muted font-weight-bold text-uppercase d-block text-truncate"
                                        style="font-size: clamp(9.5px, 0.8vw, 11px);">Warning</small>
                                    <h5 class="mb-0 font-weight-bold text-dark text-truncate" id="warning-count"
                                        style="font-size: clamp(15px, 1.35vw, 20px);">0</h5>
                                    <small class="text-muted font-weight-bold d-block text-truncate text-warning" id="warning-pct"
                                        style="font-size: clamp(9px, 0.75vw, 10px);">0%</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Danger -->
                    <div class="col-6 col-md p-1">
                        <div class="card shadow-sm border-0 h-100 rounded-lg">
                            <div class="card-body d-flex align-items-center p-2 p-lg-3">
                                <div class="bg-danger rounded text-white d-flex align-items-center justify-content-center mr-2 mr-lg-3"
                                    style="width: 38px; height: 38px; flex-shrink: 0;">
                                    <i class="fas fa-exclamation-triangle fa-sm"></i>
                                </div>
                                <div style="min-width: 0; line-height: 1.2;">
                                    <small class="text-muted font-weight-bold text-uppercase d-block text-truncate"
                                        style="font-size: clamp(9.5px, 0.8vw, 11px);">Danger</small>
                                    <h5 class="mb-0 font-weight-bold text-dark text-truncate" id="danger-count"
                                        style="font-size: clamp(15px, 1.35vw, 20px);">0</h5>
                                    <small class="text-muted font-weight-bold d-block text-truncate text-danger" id="danger-pct"
                                        style="font-size: clamp(9px, 0.75vw, 10px);">0%</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Update Terakhir -->
                    <div class="col-12 col-md p-1">
                        <div class="card shadow-sm border-0 h-100 rounded-lg">
                            <div class="card-body d-flex align-items-center p-2 p-lg-3">
                                <div class="bg-light rounded text-muted d-flex align-items-center justify-content-center mr-2 mr-lg-3"
                                    style="width: 38px; height: 38px; flex-shrink: 0;">
                                    <i class="far fa-clock fa-sm"></i>
                                </div>
                                <div style="min-width: 0; line-height: 1.2;">
                                    <small class="text-muted font-weight-bold text-uppercase d-block text-truncate"
                                        style="font-size: clamp(9.5px, 0.8vw, 11px);">Update Terakhir</small>
                                    <h5 class="mb-0 font-weight-bold text-dark text-truncate" id="last-update-time"
                                        style="font-size: clamp(12px, 1.05vw, 16px);">-</h5>
                                    <small class="text-muted d-block text-truncate" id="last-update-date"
                                        style="font-size: clamp(9px, 0.75vw, 10px);">-</small>
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

                {{-- ================= ROW 2.5: PETA TATA LETAK MESIN (FACTORY FLOOR MAP) ================= --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm border-0 rounded-lg">
                            <div class="card-header bg-white py-3 border-bottom-0">
                                <h6 class="font-weight-bold mb-0 text-dark">
                                    <i class="fas fa-industry text-success mr-2"></i> Peta Tata Letak Mesin (Factory Floor Map)
                                </h6>
                                <small class="text-muted">Visualisasi peta area produksi departemen. Arahkan kursor pada mesin <b>TUNGYU (Monitored)</b> untuk memantau status heater secara real-time.</small>
                            </div>
                            <div class="card-body p-3">
                                <div class="factory-floor-container position-relative">
                                    <div class="factory-floor d-flex flex-column" style="min-width: 1400px; gap: 20px;">
                                        
                                        <!-- Title Sheet Header -->
                                        <div class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom" style="border-color: #cbd5e1 !important;">
                                            <span style="font-size: 14px; font-weight: 800; color: #1e293b; letter-spacing: 0.5px;">PRODUCTION MOLD PLANT 2 - EXISTING</span>
                                            <span class="text-muted" style="font-size: 10px; font-weight: 600;">SCALE: NTS | DEPT: PRODUCTION</span>
                                        </div>

                                        <div class="d-flex" style="gap: 20px; width: 100%;">
                                            
                                            <!-- ================= LEFT AREA: FINISHING LINE & OFFICE ================= -->
                                            <div class="d-flex flex-column" style="width: 140px; border-right: 2px dashed #cbd5e1; padding-right: 15px; gap: 15px; flex-shrink: 0;">
                                                <div class="floor-row-label">Finishing Line</div>
                                                <div class="d-flex flex-wrap gap-2" style="background: rgba(148, 163, 184, 0.05); padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                                    <div class="text-center font-weight-bold text-muted mb-2 w-100" style="font-size: 8px;">AREA ASAKAI</div>
                                                    <!-- Finishing Desks -->
                                                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 6px; width: 100%;">
                                                        @for($i=1; $i<=10; $i++)
                                                            <div class="text-muted text-center" style="border: 1px solid #94a3b8; font-size: 8px; padding: 2px; border-radius: 3px; background: #fff;">DESK-{{ $i }}</div>
                                                        @endfor
                                                    </div>
                                                </div>
                                                
                                                <div class="floor-row-label" style="margin-top: auto;">Mold Office</div>
                                                <div style="border: 1px solid #cbd5e1; padding: 8px; border-radius: 8px; font-size: 8px; background: #fff;" class="text-muted text-center font-weight-bold">
                                                    OFFICE & MEETING ROOM
                                                </div>
                                            </div>

                                            <!-- ================= CENTER AREA: MAIN PRODUCTION LANES ================= -->
                                            <div class="d-flex flex-column" style="flex: 1; gap: 20px;">
                                                
                                                <!-- LANE A: MOLD KUEMIN / TUNGYU 1000 / JINGDAY -->
                                                <div class="floor-row-wrapper">
                                                    <div class="floor-row-label">Lane A - MC Mold Kue Min & Tungyu 1000CC</div>
                                                    <div class="floor-row align-items-center">
                                                        <!-- Mold Kuemin (3 Green) -->
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>KM-01<span class="mach-code">KUEMIN</span></div>
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>KM-02<span class="mach-code">KUEMIN</span></div>
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>KM-03<span class="mach-code">KUEMIN</span></div>

                                                        <!-- Tungyu 1000CC (4 Green) -->
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>TY-1001<span class="mach-code">TY 1000</span></div>
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>TY-1002<span class="mach-code">TY 1000</span></div>
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>TY-1003<span class="mach-code">TY 1000</span></div>
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>TY-1004<span class="mach-code">TY 1000</span></div>

                                                        <!-- Jingday 2000CC (1 Green) -->
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>JD-2001<span class="mach-code">JD 2000</span></div>

                                                        <!-- Fin-Check (10 desks) -->
                                                        <div class="d-flex ml-3" style="gap: 5px; border-left: 2px solid #cbd5e1; padding-left: 10px;">
                                                            @for($i=1; $i<=10; $i++)
                                                                <div class="text-muted d-flex flex-column align-items-center justify-content-center" style="border: 1px solid #94a3b8; border-radius: 4px; width: 18px; height: 42px; font-size: 6px; font-weight: 700; background: #fff;">
                                                                    FC-{{ $i }}
                                                                </div>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- LANE B: MC INJECTION MOLD KUEMIN / JINGDAY / INJ. JINGDAY 1000 -->
                                                <div class="floor-row-wrapper">
                                                    <div class="floor-row-label">Lane B - MC Injection Mold Kue Min & Jing Day & Inj. Jingday 1000CC</div>
                                                    <div class="floor-row align-items-center">
                                                        <!-- MC Injection Mold Kuemin (2 Red, 4 Green) -->
                                                        <div class="machine-node mc-broken"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>KM-04<span class="mach-code">Broken</span></div>
                                                        <div class="machine-node mc-broken"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>KM-05<span class="mach-code">Broken</span></div>
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>KM-06<span class="mach-code">Active</span></div>
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>KM-07<span class="mach-code">Active</span></div>
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>KM-08<span class="mach-code">Active</span></div>
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>KM-09<span class="mach-code">Active</span></div>

                                                        <!-- MC Inject Mold Jing Day (1 Yellow, 1 Green, 1 Red) -->
                                                        <div class="machine-node mc-repairable"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>JD-02<span class="mach-code">REPAIR</span></div>
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>JD-03<span class="mach-code">Active</span></div>
                                                        <div class="machine-node mc-broken"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>JD-04<span class="mach-code" style="color: #ef4444; font-weight: 700;">01REP05</span></div>

                                                        <!-- INJ. JINGDAY 1000CC (1 Red, 10 Green) -->
                                                        <div class="machine-node mc-broken" style="margin-left: 10px; border-left: 2px solid #cbd5e1; padding-left: 10px;"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>JD-1001<span class="mach-code">Broken</span></div>
                                                        @for($i=2; $i<=11; $i++)
                                                            <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>JD-10{{ sprintf("%02d", $i) }}<span class="mach-code">Active</span></div>
                                                        @endfor
                                                    </div>
                                                </div>

                                                <!-- LANE C: NEW INJ. TUNGYU / TUNGYU (MONITORED) / TUNGYU 2000 / REP -->
                                                <div class="floor-row-wrapper">
                                                    <div class="floor-row-label">Lane C - MC Inj. Tungyu 1000CC & 2000CC & Rep (Area Project)</div>
                                                    <div class="floor-row align-items-center">
                                                        <!-- New Inj Tungyu (3 Green) -->
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>NTY-01<span class="mach-code">NEW TY</span></div>
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>NTY-02<span class="mach-code">NEW TY</span></div>
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>NTY-03<span class="mach-code">NEW TY</span></div>

                                                        <!-- Circle SW Blow Petlight Area -->
                                                        <div class="d-flex flex-column align-items-center justify-content-center px-2" style="border: 2px solid #94a3b8; border-radius: 50%; width: 62px; height: 62px; font-size: 7px; text-align: center; background: #fff; line-height: 1.1; margin: 0 10px; flex-shrink: 0; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);">
                                                            <span class="font-weight-bold">SW BLOW</span>
                                                            <span>PETLIGHT</span>
                                                        </div>

                                                        <!-- TARGET MACHINE (TUNGYU INJECTION - MONITORED) -->
                                                        <div id="monitored-machine" class="machine-node mc-monitored glow-pulsing-grey">
                                                            <svg viewBox="0 0 120 50" style="width: 100%; height: 38px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>
                                                            <span class="font-weight-bold">TUNGYU</span>
                                                            <span class="mach-code font-weight-bold" id="mach-status-text">OFFLINE</span>
                                                        </div>

                                                        <!-- Tungyu 2000CC / REP 2000CC (8 Green) -->
                                                        @for($i=50; $i>=43; $i--)
                                                            <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>TY-{{ $i }}<span class="mach-code">TY 2000</span></div>
                                                        @endfor

                                                        <!-- REP 1000CC (1 Yellow, 1 Green) -->
                                                        <div class="machine-node mc-repairable" style="margin-left: 10px; border-left: 2px solid #cbd5e1; padding-left: 10px;"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>RP-1001<span class="mach-code">REP 1000</span></div>
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>RP-1002<span class="mach-code">REP 1000</span></div>

                                                        <!-- INJ. JING DAY 2000CC (3 Green) -->
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>JD-2002<span class="mach-code">JD 2000</span></div>
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>JD-2003<span class="mach-code">JD 2000</span></div>
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>JD-2004<span class="mach-code">JD 2000</span></div>
                                                    </div>
                                                </div>

                                                <!-- LANE D: STOCK SOLID TIRE & VACUUM JING DAY / MEISHO / PANSTONE -->
                                                <div class="floor-row-wrapper">
                                                    <div class="floor-row-label">Lane D - Stock Solid Tire & Vacuum Area</div>
                                                    <div class="floor-row align-items-center">
                                                        <!-- Stock Solid Tire area indicator -->
                                                        <div class="d-flex flex-column align-items-center justify-content-center text-muted" style="border: 1px solid #cbd5e1; border-radius: 8px; width: 280px; height: 60px; font-size: 9px; font-weight: 700; background: #fff; flex-shrink: 0; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);">
                                                            <i class="fas fa-boxes mb-1" style="font-size: 14px;"></i> STOCK SOLID TIRE PALLETS AREA
                                                        </div>

                                                        <!-- Vacuum Jing Day (3 Green) -->
                                                        <div class="machine-node mc-active ml-3" style="border-left: 2px solid #cbd5e1; padding-left: 10px;"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>VAC-01<span class="mach-code">VACUUM</span></div>
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>VAC-02<span class="mach-code">VACUUM</span></div>
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>VAC-03<span class="mach-code">VACUUM</span></div>

                                                        <!-- Meisho 150T (2 Yellow) -->
                                                        <div class="machine-node mc-repairable"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>MS-01<span class="mach-code">MEISHO</span></div>
                                                        <div class="machine-node mc-repairable"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>MS-02<span class="mach-code">MEISHO</span></div>

                                                        <!-- Vacuum Panstone (3 Green) -->
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>VP-01<span class="mach-code">PANSTONE</span></div>
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>VP-02<span class="mach-code">PANSTONE</span></div>
                                                        <div class="machine-node mc-active"><svg viewBox="0 0 120 50" style="width: 100%; height: 32px; display: block; margin: 0 auto 2px auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg>VP-03<span class="mach-code">PANSTONE</span></div>
                                                    </div>
                                                </div>

                                            </div>

                                            <!-- ================= RIGHT AREA: MOLD STORAGE & R&D & VACUUM ================= -->
                                            <div class="d-flex flex-column" style="width: 250px; border-left: 2px dashed #cbd5e1; padding-left: 15px; gap: 20px; flex-shrink: 0;">
                                                
                                                <!-- R&D Section -->
                                                <div>
                                                    <div class="floor-row-label">R&D MOLD-HOSE</div>
                                                    <div style="border: 1px solid #cbd5e1; padding: 10px; border-radius: 8px; background: #fff;" class="text-muted text-center font-weight-bold">
                                                        <div style="font-size: 8px;">COOLING ROOM &</div>
                                                        <div style="font-size: 8px;">THOMPSON ASSY</div>
                                                    </div>
                                                </div>

                                                <!-- Mold Storage Vertical Columns (8 Green, 4 Red) -->
                                                <div>
                                                    <div class="floor-row-label">Mold Storage Column</div>
                                                    <div class="d-flex" style="gap: 12px; background: rgba(148, 163, 184, 0.05); padding: 8px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                                        <!-- Col 1 -->
                                                        <div class="d-flex flex-column" style="gap: 6px; flex: 1;">
                                                            @for($i=1; $i<=4; $i++)
                                                                <div class="machine-node mc-active w-100" style="height: 48px;"><svg viewBox="0 0 120 50" style="width: 100%; height: 20px; display: block; margin: 0 auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg><span class="mach-code" style="font-size: 6px;">MS-A{{$i}}</span></div>
                                                            @endfor
                                                            @for($i=1; $i<=2; $i++)
                                                                <div class="machine-node mc-broken w-100" style="height: 48px;"><svg viewBox="0 0 120 50" style="width: 100%; height: 20px; display: block; margin: 0 auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg><span class="mach-code" style="font-size: 6px;">MS-B{{$i}}</span></div>
                                                            @endfor
                                                        </div>
                                                        <!-- Col 2 -->
                                                        <div class="d-flex flex-column" style="gap: 6px; flex: 1;">
                                                            @for($i=5; $i<=8; $i++)
                                                                <div class="machine-node mc-active w-100" style="height: 48px;"><svg viewBox="0 0 120 50" style="width: 100%; height: 20px; display: block; margin: 0 auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg><span class="mach-code" style="font-size: 6px;">MS-A{{$i}}</span></div>
                                                            @endfor
                                                            @for($i=3; $i<=4; $i++)
                                                                <div class="machine-node mc-broken w-100" style="height: 48px;"><svg viewBox="0 0 120 50" style="width: 100%; height: 20px; display: block; margin: 0 auto;"><rect x="10" y="22" width="6" height="16" rx="1" fill="currentColor"/><line x1="16" y1="25" x2="45" y2="25" stroke="currentColor" stroke-width="2"/><line x1="16" y1="35" x2="45" y2="35" stroke="currentColor" stroke-width="2"/><rect x="22" y="19" width="6" height="22" rx="1" fill="currentColor" opacity="0.8"/><rect x="32" y="17" width="10" height="26" rx="2" fill="currentColor"/><rect x="46" y="17" width="8" height="26" rx="1" fill="currentColor"/><polygon points="72,8 84,8 78,19" fill="currentColor"/><rect x="54" y="22" width="38" height="10" rx="1" fill="currentColor"/><rect x="92" y="24" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/><rect x="5" y="42" width="104" height="6" rx="2" fill="currentColor" opacity="0.5"/></svg><span class="mach-code" style="font-size: 6px;">MS-B{{$i}}</span></div>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Sub Vacuum Area (Right-Bottom) -->
                                                <div>
                                                    <div class="floor-row-label">Vacuum & Jingday 2000</div>
                                                    <div class="d-flex flex-column" style="gap: 8px;">
                                                        <!-- INJ 2000CC (3 Green, 1 Yellow, 1 Green) -->
                                                        <div class="d-flex" style="gap: 5px;">
                                                            <div class="machine-node mc-active" style="width: 48px; height: 48px;"><span class="mach-code" style="font-size: 6px;">IJ-01</span></div>
                                                            <div class="machine-node mc-active" style="width: 48px; height: 48px;"><span class="mach-code" style="font-size: 6px;">IJ-02</span></div>
                                                            <div class="machine-node mc-active" style="width: 48px; height: 48px;"><span class="mach-code" style="font-size: 6px;">IJ-03</span></div>
                                                            <div class="machine-node mc-repairable" style="width: 48px; height: 48px;"><span class="mach-code" style="font-size: 6px;">KM-201</span></div>
                                                            <div class="machine-node mc-active" style="width: 48px; height: 48px;"><span class="mach-code" style="font-size: 6px;">JD-201</span></div>
                                                        </div>
                                                        <!-- Vacuum Panstone & Jing Day -->
                                                        <div class="d-flex" style="gap: 5px;">
                                                            <div class="machine-node mc-active" style="width: 48px; height: 48px;"><span class="mach-code" style="font-size: 6px;">VJD-01</span></div>
                                                            <div class="machine-node mc-active" style="width: 48px; height: 48px;"><span class="mach-code" style="font-size: 6px;">VJD-02</span></div>
                                                            <div class="machine-node mc-active" style="width: 48px; height: 48px;"><span class="mach-code" style="font-size: 6px;">VJD-03</span></div>
                                                            <div class="machine-node mc-repairable" style="width: 48px; height: 48px;"><span class="mach-code" style="font-size: 6px;">VP-04</span></div>
                                                            <div class="machine-node mc-active" style="width: 48px; height: 48px;"><span class="mach-code" style="font-size: 6px;">VP-05</span></div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>

                                        <!-- ================= BOTTOM AREA: PRESS SOLID TIRE MACHINES ================= -->
                                        <div class="w-100 pt-3 border-top d-flex align-items-center" style="border-color: #cbd5e1 !important; gap: 20px;">
                                            <div class="floor-row-label mb-0" style="flex-shrink: 0; width: 140px;">Lane E - Press M/C Set</div>
                                            <div class="d-flex align-items-center" style="gap: 15px; flex: 1;">
                                                <!-- Oasis / Oven Area -->
                                                <div class="text-muted text-center font-weight-bold" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 6px 12px; font-size: 8px; background: #fff;">
                                                    OASIS & OVEN M/C
                                                </div>
                                                <!-- Set 1 (3 machines) -->
                                                <div class="d-flex" style="gap: 6px; background: rgba(148, 163, 184, 0.05); padding: 6px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                                    <span class="text-muted font-weight-bold mr-2 align-self-center" style="font-size: 7px;">SET 1:</span>
                                                    <div class="machine-node mc-active" style="width: 60px; height: 42px; font-size: 7px;">PR-01</div>
                                                    <div class="machine-node mc-active" style="width: 60px; height: 42px; font-size: 7px;">PR-02</div>
                                                    <div class="machine-node mc-active" style="width: 60px; height: 42px; font-size: 7px;">PR-03</div>
                                                </div>
                                                <!-- Set 2 (3 machines) -->
                                                <div class="d-flex" style="gap: 6px; background: rgba(148, 163, 184, 0.05); padding: 6px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                                    <span class="text-muted font-weight-bold mr-2 align-self-center" style="font-size: 7px;">SET 2:</span>
                                                    <div class="machine-node mc-active" style="width: 60px; height: 42px; font-size: 7px;">PR-04</div>
                                                    <div class="machine-node mc-active" style="width: 60px; height: 42px; font-size: 7px;">PR-05</div>
                                                    <div class="machine-node mc-active" style="width: 60px; height: 42px; font-size: 7px;">PR-06</div>
                                                </div>
                                                <!-- Set 3 (3 machines) -->
                                                <div class="d-flex" style="gap: 6px; background: rgba(148, 163, 184, 0.05); padding: 6px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                                    <span class="text-muted font-weight-bold mr-2 align-self-center" style="font-size: 7px;">SET 3:</span>
                                                    <div class="machine-node mc-active" style="width: 60px; height: 42px; font-size: 7px;">PR-07</div>
                                                    <div class="machine-node mc-active" style="width: 60px; height: 42px; font-size: 7px;">PR-08</div>
                                                    <div class="machine-node mc-active" style="width: 60px; height: 42px; font-size: 7px;">PR-09</div>
                                                </div>
                                                <!-- Heavy Press 300T/500T/1000T -->
                                                <div class="d-flex" style="gap: 6px; border-left: 2px solid #cbd5e1; padding-left: 10px;">
                                                    <div class="machine-node mc-active" style="width: 65px; height: 42px; font-size: 7px;">PRESS 300T</div>
                                                    <div class="machine-node mc-active" style="width: 65px; height: 42px; font-size: 7px;">PRESS 500T</div>
                                                    <div class="machine-node mc-active" style="width: 65px; height: 42px; font-size: 7px;">PRESS 1000T</div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <!-- Sleek Floating Tooltip/Popover Overlay for Monitored Machine -->
                                    <div id="machine-details-popover" class="machine-popover">
                                        <div class="machine-popover-header d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="machine-popover-title">Injection Tungyu 1000CC</div>
                                                <div class="machine-popover-subtitle">Status Heater Real-Time</div>
                                            </div>
                                            <span class="badge badge-secondary" id="popover-overall-status">OFFLINE</span>
                                        </div>
                                        <div class="machine-popover-body">
                                            <div class="popover-sensor-grid" id="popover-sensor-container">
                                                <!-- Data Sensor CT01 - CT06 digenerate otomatis oleh JS -->
                                            </div>
                                            <div class="mt-3 pt-2 border-top text-center text-muted" style="font-size: 9.5px;">
                                                <i class="fas fa-clock mr-1"></i> Update: <span id="popover-last-update">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
