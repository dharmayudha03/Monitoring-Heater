@extends('layouts.admin')

@section('page_title', 'Monitoring Heater')

@section('content')
    <div class="content-wrapper" style="background-color: #F4F7F6;">
        <section class="content pt-4">
            <div class="container-fluid">

                <!-- Page Header (Fixed 1-Row Layout) -->
                <div class="d-flex align-items-center justify-content-between mb-4 flex-nowrap"
                    style="gap: 12px; min-height: 48px;">
                    <div style="flex: 1; min-width: 0;">
                        <h5 class="font-weight-bold text-dark mb-1 text-truncate"
                            style="font-size: clamp(14px, 1.2vw, 18px);">Monitoring All Heaters</h5>
                        <p class="text-muted small mb-0 text-truncate" style="font-size: clamp(10px, 0.9vw, 12px);">Overview
                            real-time status unit heater aktif pada seluruh zona produksi (Data Live ESP32 Sensor).</p>
                    </div>
                    @if (Auth::check() && Auth::user()->isAdmin())
                        <div style="flex-shrink: 0;">
                            <button class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm font-weight-bold"
                                data-toggle="modal" data-target="#modalReplaceHeater"
                                style="white-space: nowrap; font-size: clamp(10px, 0.85vw, 12px); padding: 6px 14px;">
                                <i class="fas fa-tools mr-1"></i> Form Ganti Heater
                            </button>
                        </div>
                    @endif
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-lg border-0 shadow-sm mb-4"
                        role="alert">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <!-- Heater Cards Grid -->
                <div class="row">
                    @foreach ($heaters as $heater)
                        @php
                            $log = $heater->latestLog;
                            $status = $log ? $log->status : 'OFFLINE';

                            $bgHeader = 'bg-success';
                            $badgeClass = 'badge-success';
                            $cardBorder = 'border-success';

                            if ($status === 'REPLACED') {
                                $bgHeader = 'bg-info';
                                $badgeClass = 'badge-info';
                                $cardBorder = 'border-info';
                            } elseif ($status === 'WARNING') {
                                $bgHeader = 'bg-warning';
                                $badgeClass = 'badge-warning';
                                $cardBorder = 'border-warning';
                            } elseif ($status === 'DANGER') {
                                $bgHeader = 'bg-danger';
                                $badgeClass = 'badge-danger';
                                $cardBorder = 'border-danger';
                            } elseif ($status === 'OFFLINE') {
                                $bgHeader = 'bg-secondary';
                                $badgeClass = 'badge-secondary';
                                $cardBorder = 'border-secondary';
                            }
                        @endphp

                        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                            <div class="card shadow-sm border-0 h-100 rounded-lg overflow-hidden position-relative"
                                style="transition: transform 0.2s;">
                                <!-- Card Top Header -->
                                <div
                                    class="{{ $bgHeader }} text-white p-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="font-weight-bold mb-0">{{ $heater->heater_code }}</h6>
                                        <small style="font-size: 11px; opacity: 0.9;">{{ $heater->heater_name }} |
                                            {{ $heater->zone }}</small>
                                    </div>
                                    <span class="badge badge-light text-dark font-weight-bold px-2 py-1"
                                        style="font-size: 11px;">
                                        {{ $status }}
                                    </span>
                                </div>

                                <!-- Card Body Metrics -->
                                <div class="card-body p-3">
                                    <div class="text-center py-2">
                                        <small class="text-muted d-block font-weight-bold" style="font-size: 10px;">ARUS
                                            LISTRIK (ESP32 SENSOR)</small>
                                        <strong class="h4 font-weight-bold text-dark mb-0">
                                            {{ $log ? number_format($log->current, 2) : '-' }} <small
                                                style="font-size:12px;">A</small>
                                        </strong>
                                        <div class="mt-1">
                                            <small class="text-muted" style="font-size: 10px;">Nominal: 10.93 A</small>
                                        </div>
                                    </div>

                                    <div class="mt-3 pt-2 border-top d-flex justify-content-between align-items-center"
                                        style="font-size: 11px;">
                                        <span class="text-muted">Update Terakhir:</span>
                                        <span class="font-weight-600 text-dark">
                                            {{ $log ? $log->received_at->format('H:i:s') : '-' }}
                                        </span>
                                    </div>

                                    @if ($status === 'REPLACED')
                                        <div class="mt-3">
                                            <span class="badge badge-pill badge-info d-block py-2 font-weight-bold" style="font-size: 11px;">
                                                <i class="fas fa-tools mr-1"></i> REPLACED (Toleransi 15 Mnt)
                                            </span>
                                        </div>
                                    @elseif ($status === 'DANGER')
                                        <div class="mt-3">
                                            @if (Auth::check() && Auth::user()->isAdmin())
                                                <button
                                                    class="btn btn-xs btn-block btn-outline-danger font-weight-bold py-1 btn-ganti-heater"
                                                    data-id="{{ $heater->id }}" data-code="{{ $heater->heater_code }}"
                                                    data-toggle="modal" data-target="#modalReplaceHeater">
                                                    <i class="fas fa-exclamation-circle mr-1"></i> Perlu Ganti Heater
                                                </button>
                                            @else
                                                <span class="badge badge-pill badge-danger d-block py-2 font-weight-bold" style="font-size: 11px;">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i> Status DANGER
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </section>
    </div>

    <!-- Modal Form Ganti Heater (Hanya Untuk Engineering / Admin) -->
    @if (Auth::check() && Auth::user()->isAdmin())
        <div class="modal fade" id="modalReplaceHeater" tabindex="-1" role="dialog" aria-labelledby="modalReplaceHeaterLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content rounded-lg border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white py-3">
                        <h5 class="modal-title font-weight-bold mb-0" id="modalReplaceHeaterLabel"><i
                                class="fas fa-tools mr-2"></i> Form Proses Penggantian Heater</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('replacement.store') }}" method="POST">
                        @csrf
                        <div class="modal-body p-3">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-muted small mb-1">Pilih Heater Yang Diganti</label>
                                <select name="heater_id" id="modal_heater_id" class="form-control rounded-pill" required>
                                    <option value="">-- Pilih Heater --</option>
                                    @foreach ($heaters as $h)
                                        <option value="{{ $h->id }}" data-code="{{ $h->heater_code }}">
                                            {{ $h->heater_code }} - {{ $h->heater_name }} ({{ $h->zone }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-muted small mb-1">Teknisi / Penanggung Jawab</label>
                                <input type="text" name="replaced_by" class="form-control rounded-pill"
                                    placeholder="Masukkan nama teknisi / penanggung jawab" required>
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-muted small mb-1">Alasan Penggantian</label>
                                <input type="text" name="reason" class="form-control rounded-pill"
                                    placeholder="Masukkan alasan penggantian..." required>
                            </div>

                            <div class="form-group mb-2">
                                <label class="font-weight-bold text-muted small mb-1">Catatan Tambahan</label>
                                <textarea name="notes" class="form-control rounded-lg" rows="2"
                                    placeholder="Deskripsi perbaikan/tipe sparepart baru yang dipasang..."></textarea>
                            </div>
                        </div>

                        <div class="modal-footer bg-light border-0 py-2">
                            <button type="button" class="btn btn-secondary rounded-pill px-4"
                                data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 font-weight-bold">
                                <i class="fas fa-check mr-1"></i> Simpan & Reset Status ke NORMAL
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#modalReplaceHeater').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const id = button.data('id');
                if (id) {
                    $('#modal_heater_id').val(id);
                } else {
                    $('#modal_heater_id').val('');
                }
            });
        });
    </script>
@endsection
