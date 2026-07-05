@extends('layouts.admin')

@section('page_title', 'Pengaturan Profil User')

@section('content')
    <div class="content-wrapper" style="background-color: #F4F7F6;">
        <section class="content pt-4">
            <div class="container-fluid">

                <!-- Page Header (Fixed 1-Row Layout) -->
                <div class="d-flex align-items-center justify-content-between mb-4 flex-nowrap"
                    style="gap: 12px; min-height: 48px;">
                    <div style="flex: 1; min-width: 0;">
                        <h5 class="font-weight-bold text-dark mb-1 text-truncate"
                            style="font-size: clamp(14px, 1.2vw, 18px);">Pengaturan Profil & Akun Administrator</h5>
                        <p class="text-muted small mb-0 text-truncate" style="font-size: clamp(10px, 0.9vw, 12px);">Kelola
                            informasi diri, alamat email, dan kata sandi akun administrator sistem.</p>
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
                    <!-- Left: Profile Info Form -->
                    <div class="col-md-8 mb-4">
                        <div class="card shadow-sm border-0 rounded-lg">
                            <div class="card-header bg-white border-bottom-0 py-3">
                                <h6 class="font-weight-bold text-dark mb-0"><i
                                        class="fas fa-user-edit text-primary mr-2"></i> Edit Informasi Profil</h6>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('settings.update') }}" method="POST">
                                    @csrf

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label font-weight-bold text-muted small">Nama
                                            Lengkap</label>
                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span
                                                        class="input-group-text bg-light border-right-0 rounded-left-pill"><i
                                                            class="fas fa-user text-muted"></i></span>
                                                </div>
                                                <input type="text" name="name" class="form-control rounded-right-pill"
                                                    value="{{ old('name', $user->name) }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label font-weight-bold text-muted small">Alamat
                                            Email</label>
                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span
                                                        class="input-group-text bg-light border-right-0 rounded-left-pill"><i
                                                            class="fas fa-envelope text-muted"></i></span>
                                                </div>
                                                <input type="email" name="email" class="form-control rounded-right-pill"
                                                    value="{{ old('email', $user->email) }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4">
                                    <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-key text-warning mr-2"></i>
                                        Ubah Password / Kata Sandi</h6>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label font-weight-bold text-muted small">Password
                                            Baru</label>
                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <input type="password" name="new_password" id="new_password"
                                                    class="form-control rounded-left-pill border-right-0"
                                                    placeholder="Kosongkan jika tidak ingin mengubah password">
                                                <div class="input-group-append">
                                                    <button
                                                        class="btn btn-outline-secondary border-left-0 rounded-right-pill bg-white"
                                                        type="button" id="toggleNewPassword"
                                                        style="border-color: #cbd5e1; color: #64748b;">
                                                        <i class="fas fa-eye" id="toggleNewPasswordIcon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label font-weight-bold text-muted small">Konfirmasi
                                            Password</label>
                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <input type="password" name="new_password_confirmation"
                                                    id="new_password_confirmation"
                                                    class="form-control rounded-left-pill border-right-0"
                                                    placeholder="Ulangi password baru">
                                                <div class="input-group-append">
                                                    <button
                                                        class="btn btn-outline-secondary border-left-0 rounded-right-pill bg-white"
                                                        type="button" id="toggleConfirmPassword"
                                                        style="border-color: #cbd5e1; color: #64748b;">
                                                        <i class="fas fa-eye" id="toggleConfirmPasswordIcon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row mt-4">
                                        <div class="col-sm-9 offset-sm-3">
                                            <button type="submit"
                                                class="btn btn-primary rounded-pill px-4 font-weight-bold shadow-sm">
                                                <i class="fas fa-save mr-1"></i> Simpan Perubahan Profil
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Profile Card Preview -->
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm border-0 rounded-lg text-center p-4 bg-white">
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
                                <span class="badge badge-success px-3 py-1 font-weight-bold mx-auto mb-3"
                                    style="width: fit-content;">
                                    <i class="fas fa-check-circle mr-1"></i> System Administrator
                                </span>
                            @else
                                <span class="badge badge-info px-3 py-1 font-weight-bold mx-auto mb-3"
                                    style="width: fit-content;">
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
