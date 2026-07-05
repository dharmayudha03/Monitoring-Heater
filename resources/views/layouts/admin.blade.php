<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('page_title', 'Heater Monitoring System') - PT IRC INOAC Indonesia</title>

    <!-- Favicon / Tab Logo -->
    <link rel="shortcut icon" href="{{ asset('images/favicon_clean.png') }}?v={{ time() }}" type="image/png">
    <link rel="icon" href="{{ asset('images/favicon_clean.png') }}?v={{ time() }}" type="image/png">

    <!-- Local Google Font (Poppins 100% Offline) -->
    <link rel="stylesheet" href="{{ asset('fonts/poppins/poppins.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">

    <!-- AdminLTE -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ time() + 10 }}">

    @yield('styles')
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">

    <div class="wrapper">
        @include('layouts.navbar')
        @include('layouts.sidebar')

        @yield('content')
    </div>

    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>

    <!-- Chart.js (Local 100% Offline) -->
    <script src="{{ asset('plugins/chartjs/chart.min.js') }}"></script>

    <!-- Global live clock and notification badge script -->
    <script>
        window.APP_URL = "{{ url('/') }}";
        let globalTotalAlerts = 0;

        function updateLiveClocks() {
            const now = new Date();
            const timeStr = now.toTimeString().split(' ')[0];
            const navClock = document.getElementById('nav-system-time');
            if (navClock) navClock.innerText = timeStr;
        }
        setInterval(updateLiveClocks, 1000);

        async function updateNavbarNotificationBadge() {
            try {
                const res = await fetch("{{ url('/api/heaters/alerts') }}");
                const json = await res.json();
                const count = json.data ? json.data.length : 0;
                globalTotalAlerts = count;

                const badge = document.getElementById('nav-alert-badge');
                if (badge) {
                    const readCount = parseInt(localStorage.getItem('alerts_read_count') || '0', 10);
                    const unread = Math.max(0, count - readCount);
                    if (unread > 0) {
                        badge.innerText = unread;
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            } catch (e) {}
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateNavbarNotificationBadge();
            setInterval(updateNavbarNotificationBadge, 5000);

            const bell = document.getElementById('btn-notification-bell');
            if (bell) {
                bell.addEventListener('click', () => {
                    localStorage.setItem('alerts_read_count', globalTotalAlerts);
                    const badge = document.getElementById('nav-alert-badge');
                    if (badge) badge.style.display = 'none';
                });
            }
        });
    </script>

    <script src="{{ asset('js/dashboard.js') }}?v={{ time() }}"></script>
    @yield('scripts')
</body>

</html>
