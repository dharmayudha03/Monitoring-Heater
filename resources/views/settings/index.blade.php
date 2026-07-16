@extends('layouts.admin')

@section('page_title', 'Pengaturan Profil & Sistem')

@section('content')
    <div class="content-wrapper" style="background-color: #F4F7F6;">
        <section class="content pt-4">
            <div class="container-fluid">

                <!-- Page Header -->
                <div class="d-flex align-items-center justify-content-between mb-4 flex-nowrap"
                    style="gap: 12px; min-height: 48px;">
                    <div style="flex: 1; min-width: 0;">
                        <h5 class="font-weight-bold text-dark mb-1 text-truncate"
                            style="font-size: clamp(14px, 1.2vw, 18px);">Pengaturan Profil & Sistem Terpadu</h5>
                        <p class="text-muted small mb-0 text-truncate" style="font-size: clamp(10px, 0.9vw, 12px);">
                            Kelola profil akun serta parameter kalibrasi sensor arus dan ambang batas heater.
                        </p>
                    </div>
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

                <div class="row">
                    <!-- Left Column: Forms -->
                    <div class="col-md-8">
                        
                        <!-- 1. Calibration & System Settings (Admin Only) -->
                        <div class="card shadow-sm border-0 rounded-lg mb-4">
                            <div class="card-header bg-white border-bottom-0 py-3">
                                <h6 class="font-weight-bold text-dark mb-0">
                                    <i class="fas fa-sliders-h text-danger mr-2"></i> Konfigurasi Kalibrasi & Ambang Batas Sensor
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('settings.system.update') }}" method="POST">
                                    @csrf
                                    
                                    <!-- A. Threshold Parameter settings -->
                                    <h6 class="font-weight-bold text-muted small text-uppercase mb-3" style="letter-spacing: 0.5px;">
                                        I. Ambang Batas Pengukuran Status
                                    </h6>
                                    
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label font-weight-bold text-muted small">Ambang Normal (Ampere)</label>
                                        <div class="col-sm-8">
                                            <input type="number" step="0.01" name="normal_min" class="form-control rounded-pill" 
                                                value="{{ old('normal_min', $sysSetting->normal_min) }}" required>
                                            <small class="text-muted d-block mt-1">Status <b>NORMAL</b> jika Arus &ge; nilai ini. (Rekomendasi: 9.00 A)</small>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label font-weight-bold text-muted small">Ambang Warning (Ampere)</label>
                                        <div class="col-sm-8">
                                            <input type="number" step="0.01" name="warning_min" class="form-control rounded-pill" 
                                                value="{{ old('warning_min', $sysSetting->warning_min) }}" required>
                                            <small class="text-muted d-block mt-1">Status <b>WARNING</b> jika Arus berada di antara nilai ini dan ambang normal. Di bawah nilai ini ber-status <b>DANGER</b>. (Rekomendasi: 7.60 A)</small>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <!-- B. Baseline settings -->
                                    <h6 class="font-weight-bold text-muted small text-uppercase mb-3" style="letter-spacing: 0.5px;">
                                        II. Nilai Baseline Acuan (Standar Delta RST)
                                    </h6>

                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label font-weight-bold text-muted small">Upper Baseline (Ampere)</label>
                                        <div class="col-sm-8">
                                            <input type="number" step="0.001" name="upper_baseline" class="form-control rounded-pill" 
                                                value="{{ old('upper_baseline', $sysSetting->upper_baseline) }}" required>
                                            <small class="text-muted d-block mt-1">Arus nominal standar untuk Cetakan Atas (Default: 11.000 A)</small>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label font-weight-bold text-muted small">Lower Baseline (Ampere)</label>
                                        <div class="col-sm-8">
                                            <input type="number" step="0.001" name="lower_baseline" class="form-control rounded-pill" 
                                                value="{{ old('lower_baseline', $sysSetting->lower_baseline) }}" required>
                                            <small class="text-muted d-block mt-1">Arus nominal standar untuk Cetakan Bawah (Default: 11.000 A)</small>
                                        </div>
                                    </div>

                                    <div class="form-group row mt-4">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-danger rounded-pill px-4 font-weight-bold shadow-sm">
                                                <i class="fas fa-save mr-1"></i> Simpan Konfigurasi Batas & Baseline
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- 2. Profile Info Form -->
                        <div class="card shadow-sm border-0 rounded-lg mb-4">
                            <div class="card-header bg-white border-bottom-0 py-3">
                                <h6 class="font-weight-bold text-dark mb-0">
                                    <i class="fas fa-user-edit text-primary mr-2"></i> Edit Informasi Profil Admin
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('settings.update') }}" method="POST">
                                    @csrf

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label font-weight-bold text-muted small">Nama Lengkap</label>
                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-light border-right-0 rounded-left-pill">
                                                        <i class="fas fa-user text-muted"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="name" class="form-control rounded-right-pill"
                                                    value="{{ old('name', $user->name) }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label font-weight-bold text-muted small">Alamat Email</label>
                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-light border-right-0 rounded-left-pill">
                                                        <i class="fas fa-envelope text-muted"></i>
                                                    </span>
                                                </div>
                                                <input type="email" name="email" class="form-control rounded-right-pill"
                                                    value="{{ old('email', $user->email) }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4">
                                    <h6 class="font-weight-bold text-dark mb-3">
                                        <i class="fas fa-key text-warning mr-2"></i> Ubah Password / Kata Sandi
                                    </h6>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label font-weight-bold text-muted small">Password Baru</label>
                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <input type="password" name="new_password" id="new_password"
                                                    class="form-control rounded-left-pill border-right-0"
                                                    placeholder="Kosongkan jika tidak ingin mengubah password">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary border-left-0 rounded-right-pill bg-white"
                                                        type="button" id="toggleNewPassword" style="border-color: #cbd5e1; color: #64748b;">
                                                        <i class="fas fa-eye" id="toggleNewPasswordIcon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label font-weight-bold text-muted small">Konfirmasi Password</label>
                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                                    class="form-control rounded-left-pill border-right-0"
                                                    placeholder="Ulangi password baru">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary border-left-0 rounded-right-pill bg-white"
                                                        type="button" id="toggleConfirmPassword" style="border-color: #cbd5e1; color: #64748b;">
                                                        <i class="fas fa-eye" id="toggleConfirmPasswordIcon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row mt-4">
                                        <div class="col-sm-9 offset-sm-3">
                                            <button type="submit" class="btn btn-primary rounded-pill px-4 font-weight-bold shadow-sm">
                                                <i class="fas fa-save mr-1"></i> Simpan Perubahan Profil
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Profile Preview Card -->
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 rounded-lg text-center p-4 bg-white mb-4">
                            @if ($user->isAdmin())
                                <div class="mx-auto mb-3 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                    style="width: 80px; height: 80px; font-size: 32px;">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                            @else
                                <div class="mx-auto mb-3 bg-success text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                    style="width: 80px; height: 80px; font-size: 32px;">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif

                            <h6 class="font-weight-bold text-dark mb-1">{{ $user->name }}</h6>
                            <p class="text-muted small mb-2">{{ $user->email }}</p>

                            @if ($user->isAdmin())
                                <span class="badge badge-success px-3 py-1 font-weight-bold mx-auto mb-3" style="width: fit-content;">
                                    <i class="fas fa-check-circle mr-1"></i> System Administrator
                                </span>
                            @else
                                <span class="badge badge-info px-3 py-1 font-weight-bold mx-auto mb-3" style="width: fit-content;">
                                    <i class="fas fa-user-check mr-1"></i> Operator System
                                </span>
                            @endif

                            <div class="border-top pt-3 text-left small text-muted">
                                <div class="d-flex justify-content-between py-1">
                                    <span>Role Akses:</span>
                                    <strong class="text-dark">{{ $user->isAdmin() ? 'Super Admin' : 'Operator' }}</strong>
                                </div>
                                <div class="d-flex justify-content-between py-1">
                                    <span>Status Akun:</span>
                                    <span class="text-success font-weight-bold">Aktif / Online</span>
                                </div>
                            </div>
                        </div>

                        <!-- Info Card: Delta RST Formula Explanation -->
                        <div class="card shadow-sm border-0 rounded-lg p-3 bg-light border-left border-info" style="border-left-width: 4px !important;">
                            <h6 class="font-weight-bold text-info"><i class="fas fa-info-circle mr-1"></i> Kalkulasi Delta RST</h6>
                            <p class="text-muted small mb-2" style="line-height: 1.4;">
                                Setiap elemen heater berdaya <b>1200 Watt</b> dipasok tegangan 3-Phase PLN <b>380 Volt</b>.
                            </p>
                            <ul class="pl-0 text-muted small mb-0" style="line-height: 1.6; list-style-type: none;">
                                <li class="mb-1">🔌 <b>1 Elemen:</b> 1200 W / 380 V = <b>3.15 A</b></li>
                                <li class="mb-1">🔌 <b>2 Elemen (1 Fasa):</b> 2400 W / 380 V = <b>6.31 A</b></li>
                                <li class="mb-1">⚡ <b>Sistem Delta RST:</b> (2400 W / 380 V) x &radic;3 = <b>10.93 A</b> (Kalibrasi Lapangan: <b>11.00 A</b>)</li>
                            </ul>
                            <p class="text-muted small mt-2 mb-0" style="line-height: 1.3;">
                                Nilai <b>11.00 A</b> adalah standar pembacaan normal tang ampere fisik Anda di lapangan.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleNew = document.getElementById('toggleNewPassword');
            if (toggleNew) {
                toggleNew.addEventListener('click', function() {
                    const pwdInput = document.getElementById('new_password');
                    const icon = document.getElementById('toggleNewPasswordIcon');
                    const isPassword = pwdInput.getAttribute('type') === 'password';
                    pwdInput.setAttribute('type', isPassword ? 'text' : 'password');
                    icon.className = isPassword ? 'fas fa-eye-slash text-primary' : 'fas fa-eye';
                });
            }

            const toggleConfirm = document.getElementById('toggleConfirmPassword');
            if (toggleConfirm) {
                toggleConfirm.addEventListener('click', function() {
                    const pwdInput = document.getElementById('new_password_confirmation');
                    const icon = document.getElementById('toggleConfirmPasswordIcon');
                    const isPassword = pwdInput.getAttribute('type') === 'password';
                    pwdInput.setAttribute('type', isPassword ? 'text' : 'password');
                    icon.className = isPassword ? 'fas fa-eye-slash text-primary' : 'fas fa-eye';
                });
            }
        });
    </script>
@endsection
