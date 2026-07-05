@extends('layouts.admin')

@section('page_title', 'Alerts System')

@section('content')
    <div class="content-wrapper" style="background-color: #F4F7F6;">
        <section class="content pt-4">
            <div class="container-fluid">

                <!-- Header with Filter on the Right (Fixed 1-Row Layout) -->
                <div class="d-flex align-items-center justify-content-between mb-4 flex-nowrap"
                    style="gap: 12px; min-height: 48px;">
                    <div style="flex: 1; min-width: 0;">
                        <h5 class="font-weight-bold text-dark mb-1 text-truncate"
                            style="font-size: clamp(14px, 1.2vw, 18px);">Daftar Peringatan & Alerts</h5>
                        <p class="text-muted small mb-0 text-truncate" style="font-size: clamp(10px, 0.9vw, 12px);">Pusat
                            penanganan notifikasi kegagalan arus heater (WARNING / DANGER).</p>
                    </div>
                    <div class="d-flex align-items-center flex-nowrap" style="flex-shrink: 0;">
                        <form method="GET" action="{{ route('alerts.index') }}" class="form-inline">
                            <label class="small font-weight-bold text-muted mr-2 d-none d-sm-inline">Filter
                                Severity:</label>
                            <select name="severity" class="form-control form-control-sm rounded-pill shadow-sm"
                                style="min-width: 140px; font-size: clamp(10px, 0.85vw, 12px);"
                                onchange="this.form.submit()">
                                <option value="">-- Semua Severity --</option>
                                <option value="DANGER" {{ request('severity') == 'DANGER' ? 'selected' : '' }}>DANGER
                                    (Kritis)</option>
                                <option value="WARNING" {{ request('severity') == 'WARNING' ? 'selected' : '' }}>WARNING
                                    (Peringatan)</option>
                            </select>
                        </form>
                    </div>
                </div>

                <!-- Alerts Table -->
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-header bg-white border-bottom-0 py-3">
                        <h6 class="font-weight-bold text-dark mb-0"><i class="fas fa-bell text-warning mr-2"></i> Log Alert
                            Terbaru</h6>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center mb-0" style="font-size: 13px;">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th class="py-3 border-0">No</th>
                                    <th class="py-3 border-0">Waktu Terdeteksi</th>
                                    <th class="py-3 border-0">Heater Code</th>
                                    <th class="py-3 border-0">Zone</th>
                                    <th class="py-3 border-0">Nilai Arus (A)</th>
                                    <th class="py-3 border-0">Severity</th>
                                    <th class="py-3 border-0">Rekomendasi Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($alerts as $idx => $alert)
                                    @php
                                        $isDanger = $alert->status === 'DANGER';
                                    @endphp
                                    <tr>
                                        <td>{{ $alerts->firstItem() + $idx }}</td>
                                        <td>{{ $alert->received_at->format('d-m-Y H:i:s') }}</td>
                                        <td class="font-weight-bold">{{ $alert->heater->heater_code ?? '-' }}</td>
                                        <td>{{ $alert->heater->zone ?? '-' }}</td>
                                        <td
                                            class="{{ $isDanger ? 'text-danger font-weight-bold' : 'text-warning font-weight-bold' }}">
                                            {{ number_format($alert->current, 2) }} A
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-{{ $isDanger ? 'danger' : 'warning' }} px-3 py-1 font-weight-bold">
                                                {{ $alert->status }}
                                            </span>
                                        </td>
                                        <td class="text-left">
                                            @if ($isDanger)
                                                <span class="text-danger font-weight-bold"><i class="fas fa-tools mr-1"></i>
                                                    Segera lakukan Penggantian Heater</span>
                                            @else
                                                <span class="text-muted"><i class="fas fa-search mr-1"></i> Periksa koneksi
                                                    & tegangan listrik</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-5 text-muted">Tidak ada log alert. Semua heater
                                            berjalan normal!</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($alerts->hasPages())
                        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                            <small class="text-muted">Menampilkan {{ $alerts->firstItem() }} - {{ $alerts->lastItem() }}
                                dari {{ $alerts->total() }} data</small>
                            <div>
                                {{ $alerts->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </section>
    </div>
@endsection
