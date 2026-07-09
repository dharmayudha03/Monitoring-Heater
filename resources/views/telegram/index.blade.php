@extends('layouts.admin')

@section('page_title', 'Telegram Notification Logs')

@section('content')
    <div class="content-wrapper" style="background-color: #F4F7F6;">
        <section class="content pt-4">
            <div class="container-fluid">

                <!-- Header (Responsive Layout) -->
                <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between mb-4"
                    style="gap: 12px; min-height: 48px;">
                    <div style="min-width: 0; width: 100%;">
                        <h5 class="font-weight-bold text-dark mb-1"
                            style="font-size: clamp(14px, 1.2vw, 18px);">Telegram Audit Logs</h5>
                        <p class="text-muted small mb-0 text-muted" style="font-size: clamp(10px, 0.9vw, 12px);">Riwayat
                            notifikasi otomatis yang terkirim ke Bot & Grup Telegram Maintenance.</p>
                    </div>
                    <div class="d-flex align-items-center flex-wrap justify-content-start justify-content-sm-end mt-2 mt-sm-0" style="gap: 6px; width: 100%;">
                        <form action="{{ route('telegram.remind_danger') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit"
                                class="btn btn-sm btn-warning rounded-pill font-weight-bold shadow-sm text-dark"
                                style="white-space: nowrap; font-size: 11px; padding: 5px 12px;">
                                <i class="fas fa-bell mr-1"></i> Kirim Reminder DANGER Harian
                            </button>
                        </form>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-lg border-0 shadow-sm mb-3"
                        role="alert">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-warning alert-dismissible fade show rounded-lg border-0 shadow-sm mb-3"
                        role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <!-- Bot Config Info Card -->
                <div class="card shadow-sm border-0 rounded-lg mb-4 bg-white border-left border-info"
                    style="border-left-width: 4px !important;">
                    <div class="card-body p-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center" style="gap: 12px;">
                        <div>
                            <small class="text-uppercase text-muted font-weight-bold d-block"
                                style="font-size:10px;">TERHUBUNG KE GRUP TELEGRAM (SUPERGROUP)</small>
                            <div class="d-flex flex-wrap align-items-center" style="gap: 6px;">
                                <strong class="text-dark h6 mb-0"><i class="fas fa-users text-info mr-1"></i> Monitoring Heater</strong>
                                <span class="badge badge-light border text-secondary font-weight-normal">Group ID: <code>-1003969013563</code></span>
                            </div>
                        </div>
                        <div class="mt-2 mt-md-0">
                            <span class="badge badge-success px-3 py-2 font-weight-bold" style="font-size: 12px; white-space: nowrap;">
                                <i class="fas fa-check-circle mr-1"></i> BOT ACTIVE IN GROUP
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                            <thead class="bg-light text-muted text-center">
                                <tr>
                                    <th class="py-3 border-0">No</th>
                                    <th class="py-3 border-0">Waktu Terkirim</th>
                                    <th class="py-3 border-0">Target Heater</th>
                                    <th class="py-3 border-0">Pesan Notifikasi</th>
                                    <th class="py-3 border-0">Status Telegram</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $idx => $t)
                                    <tr>
                                        <td class="text-center">{{ $logs->firstItem() + $idx }}</td>
                                        <td class="text-center" style="white-space: nowrap;">
                                            {{ $t->sent_at ? $t->sent_at->format('d-m-Y H:i:s') : $t->created_at->format('d-m-Y H:i:s') }}
                                        </td>
                                        <td class="text-center font-weight-bold">
                                            {{ $t->heater->heater_code ?? 'System Alert' }}
                                        </td>
                                        <td class="text-left">
                                            <div class="p-2 bg-light rounded border"
                                                style="font-family: monospace; font-size: 12px; color: #2c3e50;">
                                                {!! nl2br(e($t->message)) !!}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if ($t->status === 'SUCCESS')
                                                <span class="badge badge-success px-3 py-1 font-weight-bold"><i
                                                        class="fas fa-check-circle mr-1"></i> SUCCESS</span>
                                            @else
                                                <span class="badge badge-danger px-3 py-1 font-weight-bold"><i
                                                        class="fas fa-times-circle mr-1"></i> FAILED</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-5 text-center text-muted">Belum ada riwayat notifikasi
                                            Telegram.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($logs->hasPages())
                        <div class="card-footer bg-white border-0 py-3">
                            {{ $logs->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </section>
    </div>
@endsection
