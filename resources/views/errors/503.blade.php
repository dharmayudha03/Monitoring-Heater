<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeliharaan Sistem - PT IRC INOAC Indonesia</title>

    <!-- Favicon / Tab Logo -->
    <link rel="shortcut icon" href="{{ asset('images/favicon_clean.png') }}?v={{ time() }}" type="image/png">
    <link rel="icon" href="{{ asset('images/favicon_clean.png') }}?v={{ time() }}" type="image/png">

    <!-- Local Google Font (Poppins 100% Offline) -->
    <link rel="stylesheet" href="{{ asset('fonts/poppins/poppins.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- AdminLTE / Bootstrap -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">

    <style>
        html,
        body {
            height: 100vh;
            overflow: hidden;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.7) 0%, rgba(15, 23, 42, 0.85) 100%), url('{{ asset('images/company_bg.jpg') }}');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .maintenance-card {
            max-width: 480px;
            width: 92%;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            padding: 24px 26px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-sizing: border-box;
        }

        .company-logo {
            max-height: 42px;
            width: auto;
            object-fit: contain;
        }

        .maintenance-icon {
            width: 50px;
            height: 50px;
            background: #fef3c7;
            color: #d97706;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px auto;
            font-size: 22px;
        }

        .status-badge-live {
            background-color: #dcfce7;
            color: #15803d;
            font-size: 10px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
        }
    </style>
</head>

<body>

    <div class="maintenance-card">
        <!-- Logo -->
        <img src="{{ asset('images/logo.png') }}" alt="PT IRC INOAC Indonesia" class="company-logo mb-2">

        <!-- Icon -->
        <div class="maintenance-icon shadow-sm">
            <i class="fas fa-tools"></i>
        </div>

        <h5 class="font-weight-bold text-dark mb-1" style="font-size: 16px;">Sistem Sedang Pemeliharaan (Maintenance)
        </h5>
        <p class="text-muted small mb-3" style="font-size: 11px; line-height: 1.4;">
            Website Heater Monitoring System sedang dalam proses pemeliharaan rutin oleh Tim IT & Maintenance PT IRC
            INOAC Indonesia.
        </p>

        <!-- Live Real-Time Status Box -->
        <div class="bg-light p-2.5 rounded-lg border text-left mb-3" style="font-size: 11px;">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="font-weight-bold text-dark"><i class="fas fa-microchip text-primary mr-1"></i> Sensor IoT
                    Data Collector</span>
                <span class="status-badge-live"><i class="fas fa-circle mr-1" style="font-size: 6px;"></i> REAL-TIME
                    ACTIVE</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <span class="font-weight-bold text-dark"><i class="fab fa-telegram text-info mr-1"></i> Notifikasi
                    Telegram Group</span>
                <span class="status-badge-live"><i class="fas fa-circle mr-1" style="font-size: 6px;"></i> ONLINE
                    24/7</span>
            </div>
            <hr class="my-1.5">
            <small class="text-muted d-block text-center" style="font-size: 9.5px;">
                *Pengumpulan data sensor & notifikasi Telegram bahaya tetap berjalan 100% real-time di latar belakang.
            </small>
        </div>

        <!-- Access Button & Link for Admin / User Bypass -->
        <div class="card border bg-light mb-3 text-center">
            <div class="card-body p-2.5">
                <small class="text-muted d-block font-weight-bold mb-1.5" style="font-size: 10.5px;">Akses Khusus Admin
                    / User Untuk Masuk Kembali:</small>
                <a href="{{ url('/irc2026') }}"
                    class="btn btn-primary rounded-pill px-3 py-1.5 btn-block font-weight-bold shadow-sm mb-1"
                    style="font-size: 12px;">
                    <i class="fas fa-sign-in-alt mr-1"></i> Klik Di Sini Untuk Masuk Ke Website
                </a>
                <small class="text-muted d-block" style="font-size: 10px;">
                    Atau gunakan URL: <code>{{ url('/irc2026') }}</code>
                </small>
            </div>
        </div>

        <div class="border-top pt-2">
            <small class="text-muted" style="font-size: 10px;">
                &copy; 2026 PT IRC INOAC Indonesia. All Rights Reserved.
            </small>
        </div>
    </div>

</body>

</html>
