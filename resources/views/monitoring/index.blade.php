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

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show rounded-lg border-0 shadow-sm mb-4"
                        role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
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
                                <div class="card-body p-3 d-flex flex-column justify-content-between">
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

                                    <div>
                                        <div class="mt-3 pt-2 border-top d-flex justify-content-between align-items-center"
                                            style="font-size: 11px;">
                                            <span class="text-muted">Update Terakhir:</span>
                                            <span class="font-weight-600 text-dark">
                                                {{ $log ? $log->received_at->format('H:i:s') : '-' }}
                                            </span>
                                        </div>

                                        <!-- Admin Action Buttons (Kalibrasi & Ganti Heater Terintegrasi) -->
                                        <div class="mt-3">
                                            @if (Auth::check() && Auth::user()->isAdmin())
                                                <div class="d-flex" style="gap: 8px;">
                                                    <button
                                                        class="btn btn-xs btn-outline-primary font-weight-bold py-1 btn-kalibrasi flex-grow-1"
                                                        data-code="{{ $heater->heater_code }}"
                                                        data-current="{{ $log ? number_format($log->current, 2) : '0.00' }}"
                                                        data-multiplier="{{ 
                                                            $heater->heater_code === 'CT01' ? $sysSetting->m_ct1 : (
                                                            $heater->heater_code === 'CT02' ? $sysSetting->m_ct2 : (
                                                            $heater->heater_code === 'CT03' ? $sysSetting->m_ct3 : (
                                                            $heater->heater_code === 'CT04' ? $sysSetting->m_ct4 : (
                                                            $heater->heater_code === 'CT05' ? $sysSetting->m_ct5 : $sysSetting->m_ct6
                                                            ))))
                                                        }}"
                                                        data-toggle="modal" data-target="#modalCalibrateSensor"
                                                        style="font-size: 11px; padding: 4px 8px;">
                                                        <i class="fas fa-sliders-h mr-1"></i> Kalibrasi
                                                    </button>
                                                    
                                                    @if ($status === 'DANGER')
                                                        <button
                                                            class="btn btn-xs btn-outline-danger font-weight-bold py-1 btn-ganti-heater flex-grow-1"
                                                            data-id="{{ $heater->id }}" data-code="{{ $heater->heater_code }}"
                                                            data-name="{{ $heater->heater_name }}" data-zone="{{ $heater->zone }}"
                                                            data-toggle="modal" data-target="#modalReplaceHeater"
                                                            style="font-size: 11px; padding: 4px 8px;">
                                                            <i class="fas fa-tools mr-1"></i> Ganti
                                                        </button>
                                                    @endif
                                                </div>
                                            @else
                                                @if ($status === 'REPLACED')
                                                    <span class="badge badge-pill badge-info d-block py-2 font-weight-bold" style="font-size: 11px;">
                                                        <i class="fas fa-tools mr-1"></i> REPLACED (Toleransi 15 Mnt)
                                                    </span>
                                                @elseif ($status === 'DANGER')
                                                    <span class="badge badge-pill badge-danger d-block py-2 font-weight-bold" style="font-size: 11px;">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i> Status DANGER
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
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
                <div class="modal-content rounded-lg border-0 shadow-lg" style="max-height: 95vh; overflow-y: auto;">
                    <div class="modal-header bg-primary text-white py-2 px-3">
                        <h6 class="modal-title font-weight-bold mb-0" id="modalReplaceHeaterLabel"><i
                                class="fas fa-tools mr-2"></i> Form Proses Penggantian Heater</h6>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('replacement.store') }}" method="POST">
                        @csrf
                        <div class="modal-body p-3">
                            <div class="form-group mb-2">
                                <label class="font-weight-bold text-muted small mb-1">Pilih Heater Yang Diganti</label>
                                <select name="heater_id" id="modal_heater_id" class="form-control form-control-sm rounded-pill" required>
                                    <option value="">-- Pilih Heater --</option>
                                    @foreach ($heaters as $h)
                                        <option value="{{ $h->id }}" data-code="{{ $h->heater_code }}" data-name="{{ $h->heater_name }}" data-zone="{{ $h->zone }}">
                                            {{ $h->heater_code }} - {{ $h->heater_name }} ({{ $h->zone }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-2">
                                <label class="font-weight-bold text-muted small mb-1">Unit / Posisi Heater Yang Diganti</label>
                                <select name="replaced_unit" id="modal_replaced_unit" class="form-control form-control-sm rounded-pill" required>
                                    <option value="Heater 1">Heater 1</option>
                                    <option value="Heater 2">Heater 2</option>
                                    <option value="Kedua Heater (Heater 1 & 2)">Kedua Heater (Heater 1 & 2)</option>
                                </select>
                            </div>

                            <div class="form-group mb-2">
                                <label class="font-weight-bold text-muted small mb-1">Teknisi / Penanggung Jawab</label>
                                <input type="text" name="replaced_by" class="form-control form-control-sm rounded-pill"
                                    placeholder="Masukkan nama teknisi" required>
                            </div>

                            <div class="form-group mb-2">
                                <label class="font-weight-bold text-muted small mb-1">Alasan Penggantian</label>
                                <input type="text" name="reason" class="form-control form-control-sm rounded-pill"
                                    placeholder="Masukkan alasan penggantian..." required>
                            </div>

                            <div class="form-group mb-1">
                                <label class="font-weight-bold text-muted small mb-1">Catatan Tambahan</label>
                                <textarea name="notes" class="form-control rounded-lg" rows="1"
                                    placeholder="Deskripsi perbaikan/tipe sparepart baru..."></textarea>
                            </div>
                        </div>

                        <div class="modal-footer bg-light border-0 py-2 px-3">
                            <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3"
                                data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 font-weight-bold">
                                <i class="fas fa-check mr-1"></i> Simpan & Reset Status ke NORMAL
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Kalibrasi Sensor Arus (Hanya Untuk Engineering / Admin) -->
    @if (Auth::check() && Auth::user()->isAdmin())
        <div class="modal fade" id="modalCalibrateSensor" tabindex="-1" role="dialog" aria-labelledby="modalCalibrateSensorLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content rounded-lg border-0 shadow-lg" style="max-height: 95vh; overflow-y: auto;">
                    <div class="modal-header bg-danger text-white py-2 px-3">
                        <h6 class="modal-title font-weight-bold mb-0" id="modalCalibrateSensorLabel"><i
                                class="fas fa-sliders-h mr-2"></i> Kalibrasi Sensor Arus</h6>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('settings.calibrate') }}" method="POST">
                        @csrf
                        <div class="modal-body p-3">
                            <input type="hidden" name="heater_code" id="calibrate_heater_code">

                            <div class="form-group mb-2">
                                <label class="font-weight-bold text-muted small mb-1">Sensor / Line</label>
                                <input type="text" id="display_heater_code" class="form-control form-control-sm rounded-pill bg-light border-0 font-weight-bold text-dark" readonly>
                            </div>

                            <div class="row mb-2">
                                <div class="col-6">
                                    <label class="font-weight-bold text-muted small mb-1">Arus Saat Ini (Ampere)</label>
                                    <input type="text" name="current_esp_reading" id="calibrate_current_esp" class="form-control form-control-sm rounded-pill bg-light border-0 text-center font-weight-bold text-dark" readonly>
                                </div>
                                <div class="col-6">
                                    <label class="font-weight-bold text-muted small mb-1">Multiplier Aktif</label>
                                    <input type="text" id="calibrate_multiplier" class="form-control form-control-sm rounded-pill bg-light border-0 text-center font-weight-bold text-dark" readonly>
                                </div>
                            </div>

                            <div class="form-group mb-2">
                                <label class="font-weight-bold text-dark small mb-1 d-block">Metode Kalibrasi</label>
                                <div class="d-flex" style="gap: 15px;">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="type_auto" name="type" class="custom-control-input" value="auto" checked>
                                        <label class="custom-control-label font-weight-bold text-muted small" style="cursor:pointer;" for="type_auto">Otomatis (Masukkan Tang Ampere)</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="type_manual" name="type" class="custom-control-input" value="manual">
                                        <label class="custom-control-label font-weight-bold text-muted small" style="cursor:pointer;" for="type_manual">Manual (Masukkan Multiplier)</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Auto Calibration input -->
                            <div class="form-group mb-2" id="group_auto_calibrate">
                                <label class="font-weight-bold text-danger small mb-1">Hasil Pembacaan Tang Ampere Aktual (Ampere)</label>
                                <input type="number" step="0.01" name="actual_current" id="actual_current" class="form-control form-control-sm rounded-pill border-danger" placeholder="Contoh: 10.93">
                                <small class="text-muted d-block mt-1" style="font-size: 11px; line-height: 1.2;">Sistem akan menghitung otomatis faktor pengali baru agar nilai arus di web sesuai dengan tang ampere Anda.</small>
                            </div>

                            <!-- Manual Calibration input -->
                            <div class="form-group mb-2 d-none" id="group_manual_calibrate">
                                <label class="font-weight-bold text-primary small mb-1">Nilai Multiplier Kalibrasi Baru (Multiplier OTA)</label>
                                <input type="number" step="0.001" name="manual_multiplier" id="manual_multiplier" class="form-control form-control-sm rounded-pill border-primary" placeholder="Contoh: 2.565">
                                <small class="text-muted d-block mt-1" style="font-size: 11px; line-height: 1.2;">Masukkan angka pengali (multiplier) baru secara manual.</small>
                            </div>
                        </div>

                        <div class="modal-footer bg-light border-0 py-2 px-3">
                            <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3"
                                data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm btn-danger rounded-pill px-4 font-weight-bold">
                                <i class="fas fa-save mr-1"></i> Simpan Kalibrasi
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
            function updateReplacedUnitOptions(selectElement, targetDropdownId) {
                const selectedOption = $(selectElement).find('option:selected');
                const rawName = selectedOption.data('name') || '';
                const zone = selectedOption.data('zone') || '';
                
                // Clean the name (remove "CTXX - ")
                const cleanName = rawName.replace(/^CT\d+\s*-\s*/i, '');
                
                const dropdown = $(targetDropdownId);
                dropdown.empty();
                
                if (cleanName && zone) {
                    dropdown.append(`<option value="Heater 1 (${cleanName})">Heater 1 (${cleanName})</option>`);
                    dropdown.append(`<option value="Heater 2 (${zone})">Heater 2 (${zone})</option>`);
                    dropdown.append(`<option value="Kedua Heater (Heater 1 & 2)">Kedua Heater (Heater 1 & 2)</option>`);
                } else {
                    dropdown.append(`<option value="Heater 1">Heater 1</option>`);
                    dropdown.append(`<option value="Heater 2">Heater 2</option>`);
                    dropdown.append(`<option value="Kedua Heater (Heater 1 & 2)">Kedua Heater (Heater 1 & 2)</option>`);
                }
            }

            // Form Ganti Heater modal handler
            $('#modalReplaceHeater').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const id = button.data('id');
                const name = button.data('name') || '';
                const zone = button.data('zone') || '';

                if (id) {
                    $('#modal_heater_id').val(id);
                    // Ensure attributes are set/updated on the target option
                    const opt = $('#modal_heater_id').find(`option[value="${id}"]`);
                    if (name) opt.attr('data-name', name).data('name', name);
                    if (zone) opt.attr('data-zone', zone).data('zone', zone);
                    
                    updateReplacedUnitOptions($('#modal_heater_id')[0], '#modal_replaced_unit');
                } else {
                    $('#modal_heater_id').val('');
                    $('#modal_replaced_unit').empty().append('<option value="">-- Pilih Heater Terlebih Dahulu --</option>');
                }
            });

            $('#modal_heater_id').on('change', function() {
                updateReplacedUnitOptions(this, '#modal_replaced_unit');
            });

            // Form Kalibrasi Sensor modal handler
            $('#modalCalibrateSensor').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const code = button.data('code');
                const current = button.data('current');
                const multiplier = button.data('multiplier');

                $('#calibrate_heater_code').val(code);
                $('#display_heater_code').val(code);
                $('#calibrate_current_esp').val(current);
                $('#calibrate_multiplier').val(multiplier);

                // Reset fields
                $('#actual_current').val('');
                $('#manual_multiplier').val(multiplier);
            });

            $('input[name="type"]').on('change', function() {
                if ($(this).val() === 'auto') {
                    $('#group_auto_calibrate').removeClass('d-none');
                    $('#group_manual_calibrate').addClass('d-none');
                    $('#actual_current').prop('required', true);
                    $('#manual_multiplier').prop('required', false);
                } else {
                    $('#group_auto_calibrate').addClass('d-none');
                    $('#group_manual_calibrate').removeClass('d-none');
                    $('#actual_current').prop('required', false);
                    $('#manual_multiplier').prop('required', true);
                }
            });

            // Trigger default state
            $('input[name="type"]:checked').trigger('change');
        });
    </script>
@endsection
