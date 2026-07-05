@extends('layouts.admin')

@section('page_title', 'Replacement Heater')

@section('content')
    <div class="content-wrapper" style="background-color: #F4F7F6;">
        <section class="content pt-4">
            <div class="container-fluid">

                <!-- Page Header & Export Action Buttons (Fixed 1-Row Layout) -->
                <div class="d-flex align-items-center justify-content-between mb-4 flex-nowrap"
                    style="gap: 12px; min-height: 48px;">
                    <div style="flex: 1; min-width: 0;">
                        <h5 class="font-weight-bold text-dark mb-1 text-truncate"
                            style="font-size: clamp(14px, 1.2vw, 18px);">Manajemen & Riwayat Ganti Heater</h5>
                        <p class="text-muted small mb-0 text-truncate" style="font-size: clamp(10px, 0.9vw, 12px);">Daftar
                            heater yang memerlukan penggantian (DANGER) dan riwayat penggantian elemen pemanas.</p>
                    </div>
                    <div class="d-flex align-items-center flex-nowrap" style="gap: 6px; flex-shrink: 0;">
                        <button type="button" class="btn btn-sm btn-success rounded-pill font-weight-bold shadow-sm"
                            data-toggle="modal" data-target="#modalPreviewExcelReplacement"
                            style="white-space: nowrap; font-size: clamp(10px, 0.85vw, 12px); padding: 5px 14px;">
                            <i class="fas fa-file-excel mr-1"></i> Preview & Export Dataset
                        </button>
                        <a href="{{ route('replacement.export.pdf', ['action' => 'print']) }}" target="_blank"
                            class="btn btn-sm btn-primary rounded-pill font-weight-bold shadow-sm"
                            style="white-space: nowrap; font-size: clamp(10px, 0.85vw, 12px); padding: 5px 12px;">
                            <i class="fas fa-print mr-1"></i> Cetak PDF
                        </a>
                        <a href="{{ route('replacement.export.pdf', ['action' => 'download']) }}" target="_blank"
                            class="btn btn-sm btn-danger rounded-pill font-weight-bold shadow-sm"
                            style="white-space: nowrap; font-size: clamp(10px, 0.85vw, 12px); padding: 5px 12px;">
                            <i class="fas fa-file-download mr-1"></i> Download PDF
                        </a>
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

                <!-- Urgent Replacement Required Section (DANGER Status) -->
                @if ($dangerHeaters->count() > 0)
                    <div class="card shadow-sm border-0 rounded-lg mb-4 bg-white border-left border-danger"
                        style="border-left-width: 5px !important;">
                        <div
                            class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                            <h6 class="font-weight-bold text-danger mb-0">
                                <i class="fas fa-exclamation-triangle mr-2"></i> HEATER MEMERLUKAN PENGGANTIAN SEGERA
                                (STATUS DANGER)
                            </h6>
                            <span class="badge badge-danger px-3 py-1 font-weight-bold">{{ $dangerHeaters->count() }} Unit
                                Kritis</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle text-center mb-0" style="font-size: 13px;">
                                <thead class="bg-light text-muted">
                                    <tr>
                                        <th>Heater Code</th>
                                        <th>Heater Name</th>
                                        <th>Zone</th>
                                        <th>Current (A)</th>
                                        <th>Status</th>
                                        <th>Waktu Terdeteksi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dangerHeaters as $dh)
                                        <tr>
                                            <td class="font-weight-bold text-danger">{{ $dh->heater_code }}</td>
                                            <td>{{ $dh->heater_name }}</td>
                                            <td>{{ $dh->zone }}</td>
                                            <td class="text-danger font-weight-bold">
                                                {{ $dh->latestLog ? number_format($dh->latestLog->current, 2) : '-' }} A
                                            </td>
                                            <td><span class="badge badge-danger px-2 py-1">DANGER</span></td>
                                            <td>{{ $dh->latestLog ? $dh->latestLog->received_at->format('d-m-Y H:i:s') : '-' }}
                                            </td>
                                            <td>
                                                <button
                                                    class="btn btn-sm btn-danger rounded-pill px-3 font-weight-bold shadow-sm btn-ganti-heater"
                                                    data-id="{{ $dh->id }}" data-code="{{ $dh->heater_code }}"
                                                    data-name="{{ $dh->heater_name }}" data-toggle="modal"
                                                    data-target="#modalReplaceHeater">
                                                    <i class="fas fa-tools mr-1"></i> Ganti Heater Ini
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- History Replacement Table -->
                <div class="card shadow-sm border-0 rounded-lg mb-4">
                    <div class="card-header bg-white border-bottom-0 py-3">
                        <h6 class="font-weight-bold text-dark mb-0"><i class="fas fa-history mr-2 text-primary"></i> Log
                            Riwayat Penggantian Heater</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center mb-0" style="font-size: 13px;">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th class="py-3 border-0">No</th>
                                    <th class="py-3 border-0">Tanggal Penggantian</th>
                                    <th class="py-3 border-0">Kode Heater</th>
                                    <th class="py-3 border-0">Nama Heater</th>
                                    <th class="py-3 border-0">Teknisi / Replaced By</th>
                                    <th class="py-3 border-0">Alasan Penggantian</th>
                                    <th class="py-3 border-0">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($replacements as $idx => $r)
                                    <tr>
                                        <td>{{ $replacements->firstItem() + $idx }}</td>
                                        <td>{{ $r->replacement_date->format('d-m-Y H:i:s') }}</td>
                                        <td class="font-weight-bold"><span
                                                class="badge badge-primary">{{ $r->heater->heater_code ?? $r->new_heater_code }}</span>
                                        </td>
                                        <td>{{ $r->heater->heater_name ?? '-' }}</td>
                                        <td><i class="fas fa-user-cog mr-1 text-muted"></i> {{ $r->replaced_by }}</td>
                                        <td class="text-left"><span
                                                class="text-danger font-weight-600">{{ $r->reason }}</span></td>
                                        <td class="text-muted small text-left">{{ $r->notes ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-5 text-muted">Belum ada riwayat penggantian heater.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($replacements->hasPages())
                        <div class="card-footer bg-white border-0 py-3">
                            {{ $replacements->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </section>
    </div>

    <!-- Modal Form Ganti Heater -->
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
                                @foreach ($allHeaters as $h)
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
    <!-- Modal Preview Excel & CSV Replacement -->
    <div class="modal fade" id="modalPreviewExcelReplacement" tabindex="-1" role="dialog"
        aria-labelledby="modalPreviewExcelReplacementLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content rounded-lg border-0 shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold" id="modalPreviewExcelReplacementLabel">
                        <i class="fas fa-file-excel mr-2"></i> Preview & Export Dataset Replacement (Excel / CSV)
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
                                    <input type="radio" id="replacementFormatExcel" name="replacement_export_format"
                                        value="excel" class="custom-control-input" checked>
                                    <label class="custom-control-label font-weight-bold text-success"
                                        for="replacementFormatExcel" style="cursor: pointer;">
                                        <i class="fas fa-file-excel fa-lg mr-1"></i> Microsoft Excel (.xlsx)
                                    </label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="replacementFormatCsv" name="replacement_export_format"
                                        value="csv" class="custom-control-input">
                                    <label class="custom-control-label font-weight-bold text-info"
                                        for="replacementFormatCsv" style="cursor: pointer;">
                                        <i class="fas fa-file-csv fa-lg mr-1"></i> CSV File (.csv)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="font-weight-bold text-muted small mb-2">Prinjau Data Riwayat Penggantian Heater:</p>
                    <div class="table-responsive border rounded mb-3" style="max-height: 220px;">
                        <table class="table table-sm table-striped text-center mb-0" style="font-size: 11px;">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Kode</th>
                                    <th>Teknisi</th>
                                    <th>Alasan</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($replacements->take(5) as $index => $rep)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $rep->replacement_date ? $rep->replacement_date->format('d-m-Y H:i') : '-' }}
                                        </td>
                                        <td class="font-weight-bold text-primary">{{ $rep->heater->heater_code ?? '-' }}
                                        </td>
                                        <td>{{ $rep->replaced_by }}</td>
                                        <td><span class="badge badge-warning">{{ $rep->reason }}</span></td>
                                        <td class="text-left text-muted">{{ $rep->notes ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4"
                        data-dismiss="modal">Batal</button>
                    <button type="button" id="btnConfirmReplacementDownload"
                        class="btn btn-success rounded-pill px-4 font-weight-bold">
                        <i class="fas fa-download mr-1"></i> Download File
                    </button>
                </div>
            </div>
        </div>
    </div>
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

            const btnConfirmRep = document.getElementById('btnConfirmReplacementDownload');
            if (btnConfirmRep) {
                btnConfirmRep.addEventListener('click', function() {
                    const format = document.querySelector('input[name="replacement_export_format"]:checked')
                        .value;
                    let url = "{{ route('replacement.export.excel') }}";
                    if (format === 'csv') {
                        url += "?format=csv";
                    }
                    window.location.href = url;
                    $('#modalPreviewExcelReplacement').modal('hide');
                });
            }
        });
    </script>
@endsection
