@extends('layouts.admin')

@section('page_title', 'Kelola User & Password')

@section('content')
<div class="content-wrapper" style="background-color: #F4F7F6;">
    <section class="content pt-4">
        <div class="container-fluid">
            
            <!-- Page Header (Fixed 1-Row Layout) -->
            <div class="d-flex align-items-center justify-content-between mb-4 flex-nowrap" style="gap: 12px; min-height: 48px;">
                <div style="flex: 1; min-width: 0;">
                    <h5 class="font-weight-bold text-dark mb-1 text-truncate" style="font-size: clamp(14px, 1.2vw, 18px);">Kelola Akun User & Password</h5>
                    <p class="text-muted small mb-0 text-truncate" style="font-size: clamp(10px, 0.9vw, 12px);">Manajemen pengguna, pembagian 3 role hak akses (Super Admin, Engineering, Viewer), dan opsi lihat/reset password.</p>
                </div>
                <div style="flex-shrink: 0;">
                    <button class="btn btn-sm btn-success rounded-pill px-3 shadow-sm font-weight-bold" data-toggle="modal" data-target="#modalAddUser" style="white-space: nowrap; font-size: clamp(10px, 0.85vw, 12px); padding: 6px 14px;">
                        <i class="fas fa-user-plus mr-1"></i> Tambah User Baru
                    </button>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-lg border-0 shadow-sm mb-4" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-lg border-0 shadow-sm mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- 3 Default Roles Info Cards -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm border-0 border-left border-danger rounded-lg h-100" style="border-left-width: 4px !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mr-2" style="width: 32px; height: 32px;">
                                    <i class="fas fa-crown fa-sm"></i>
                                </div>
                                <h6 class="font-weight-bold mb-0 text-danger">Role 1: Super Admin (Jon)</h6>
                            </div>
                            <small class="text-muted d-block">Hak akses penuh (Master). Dapat mengelola user, melihat/mengganti password, dan mengatur seluruh sistem.</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm border-0 border-left border-primary rounded-lg h-100" style="border-left-width: 4px !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-2" style="width: 32px; height: 32px;">
                                    <i class="fas fa-tools fa-sm"></i>
                                </div>
                                <h6 class="font-weight-bold mb-0 text-primary">Role 2: User Engineering</h6>
                            </div>
                            <small class="text-muted d-block">Tim Maintenance & Perbaikan. Dapat melakukan Replacement heater, tes notifikasi Telegram, dan melihat laporan.</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm border-0 border-left border-success rounded-lg h-100" style="border-left-width: 4px !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-2" style="width: 32px; height: 32px;">
                                    <i class="fas fa-eye fa-sm"></i>
                                </div>
                                <h6 class="font-weight-bold mb-0 text-success">Role 3: Public / Operator Viewer</h6>
                            </div>
                            <small class="text-muted d-block">Untuk umum / operator yang hanya ingin melihat data live monitoring, grafik arus, history, dan laporan (Read Only).</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Table Card -->
            <div class="card shadow-sm border-0 rounded-lg mb-4">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="font-weight-bold mb-0 text-dark">Daftar Pengguna Sistem & Password</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center mb-0" style="font-size: 13px;">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th>Email Address</th>
                                <th>Role / Hak Akses</th>
                                <th>Password</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $index => $u)
                                <tr>
                                    <td class="align-middle font-weight-bold">{{ $index + 1 }}</td>
                                    <td class="align-middle font-weight-bold text-dark text-left">
                                        {{ $u->name }}
                                        @if(auth()->id() === $u->id)
                                            <span class="badge badge-secondary ml-1" style="font-size: 10px;">(Anda)</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-muted text-left">{{ $u->email }}</td>
                                    <td class="align-middle">
                                        @if($u->role === 'super_admin')
                                            <span class="badge badge-danger px-3 py-1 rounded-pill"><i class="fas fa-crown mr-1"></i> Super Admin</span>
                                        @elseif($u->role === 'engineering' || $u->role === 'admin')
                                            <span class="badge badge-primary px-3 py-1 rounded-pill"><i class="fas fa-tools mr-1"></i> Engineering</span>
                                        @else
                                            <span class="badge badge-success px-3 py-1 rounded-pill"><i class="fas fa-eye mr-1"></i> Public Viewer</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <div class="input-group input-group-sm mx-auto" style="max-width: 200px;">
                                            <input type="password" id="passInput_{{ $u->id }}" class="form-control text-center font-weight-bold bg-light" value="{{ $u->plain_password ?: 'password123' }}" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('passInput_{{ $u->id }}', 'eyeIcon_{{ $u->id }}')" title="Lihat/Sembunyikan Password">
                                                    <i class="fas fa-eye" id="eyeIcon_{{ $u->id }}"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center justify-content-center" style="gap: 6px;">
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill font-weight-bold px-2 py-1 shadow-sm" data-toggle="modal" data-target="#modalEditUser{{ $u->id }}" style="font-size: 11px; white-space: nowrap;">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </button>
                                            @if(auth()->id() !== $u->id)
                                                <form action="{{ route('users.destroy', $u->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user {{ $u->name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill font-weight-bold px-2 py-1 shadow-sm" style="font-size: 11px; white-space: nowrap;">
                                                        <i class="fas fa-trash-alt mr-1"></i> Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Edit User -->
                                <div class="modal fade" id="modalEditUser{{ $u->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content rounded-lg border-0 shadow-lg">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title font-weight-bold">
                                                    <i class="fas fa-user-edit mr-2"></i> Edit User: {{ $u->name }}
                                                </h5>
                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('users.update', $u->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4 text-left">
                                                    <div class="form-group mb-3">
                                                        <label class="font-weight-bold small text-muted">Nama Lengkap</label>
                                                        <input type="text" name="name" class="form-control rounded-pill" value="{{ $u->name }}" required>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label class="font-weight-bold small text-muted">Email Address</label>
                                                        <input type="email" name="email" class="form-control rounded-pill" value="{{ $u->email }}" required>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label class="font-weight-bold small text-muted">Role / Hak Akses</label>
                                                        <select name="role" class="form-control rounded-pill">
                                                            <option value="super_admin" {{ $u->role === 'super_admin' ? 'selected' : '' }}>Super Admin (Jon)</option>
                                                            <option value="engineering" {{ in_array($u->role, ['engineering', 'admin']) ? 'selected' : '' }}>User Engineering</option>
                                                            <option value="user" {{ in_array($u->role, ['user', 'operator']) ? 'selected' : '' }}>Public / Operator Viewer</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <label class="font-weight-bold small text-muted">Password Baru (Biarkan kosong jika tidak diganti)</label>
                                                        <input type="text" name="password" class="form-control rounded-pill" placeholder="Masukkan password baru">
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light border-0">
                                                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary rounded-pill px-4 font-weight-bold">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Modal Tambah User Baru -->
<div class="modal fade" id="modalAddUser" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content rounded-lg border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-user-plus mr-2"></i> Tambah User Pengguna Baru
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-left">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-muted">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control rounded-pill" placeholder="Contoh: Teknisi Maintenance 2" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-muted">Email Address</label>
                        <input type="email" name="email" class="form-control rounded-pill" placeholder="Contoh: eng2@irc.co.id" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-muted">Role / Hak Akses</label>
                        <select name="role" class="form-control rounded-pill" required>
                            <option value="user" selected>Public / Operator Viewer (Read Only)</option>
                            <option value="engineering">User Engineering (Maintenance)</option>
                            <option value="super_admin">Super Admin (Jon)</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small text-muted">Password Akun</label>
                        <input type="text" name="password" class="form-control rounded-pill" placeholder="Minimal 6 karakter" required>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 font-weight-bold">Tambah User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
</script>
@endsection
