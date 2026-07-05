<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom-0 bg-white shadow-sm">
    <!-- Left navbar links -->
    <ul class="navbar-nav align-items-center">
        <li class="nav-item">
            <a class="nav-link text-dark" data-widget="pushmenu" href="#" role="button"><i
                    class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <h5 class="mb-0 font-weight-bold text-dark ml-2">@yield('page_title', 'Heater Monitoring System')</h5>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto align-items-center">
        <!-- System Time Badge -->
        <li class="nav-item d-none d-md-flex align-items-center mr-3 bg-light px-3 py-1 rounded-pill">
            <i class="far fa-clock text-primary mr-2"></i>
            <span id="nav-system-time" class="font-weight-600 text-dark"
                style="font-size: 13px;">{{ date('H:i:s') }}</span>
        </li>

        <!-- Notifications Dropdown -->
        <li class="nav-item dropdown mr-2">
            <a class="nav-link position-relative" data-toggle="dropdown" href="#" id="btn-notification-bell"
                style="cursor: pointer;">
                <i class="far fa-bell fa-lg text-secondary"></i>
                <span class="badge badge-danger position-absolute" id="nav-alert-badge"
                    style="top: 2px; right: 2px; font-size: 9px; padding: 2px 5px; display: none;">0</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right shadow-sm border-0">
                <span class="dropdown-item dropdown-header font-weight-bold">Notifikasi System</span>
                <div class="dropdown-divider"></div>
                <a href="{{ url('/alerts') }}" class="dropdown-item py-2">
                    <i class="fas fa-exclamation-triangle text-warning mr-2"></i> Monitor Alert Status
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ url('/monitoring') }}" class="dropdown-item py-2">
                    <i class="fas fa-tools text-danger mr-2"></i> Perlu Ganti Heater
                </a>
            </div>
        </li>

        <!-- User Menu Dropdown -->
        @php
            $currentUser = Auth::user();
            $userName = $currentUser ? $currentUser->name : 'Administrator';
            $userRole = $currentUser && $currentUser->isAdmin() ? 'Administrator' : 'Operator';
            $userBadgeColor = $currentUser && $currentUser->isAdmin() ? 'bg-primary' : 'bg-success';
            $userIcon = $currentUser && $currentUser->isAdmin() ? 'fa-user-shield' : 'fa-user';
        @endphp

        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-toggle="dropdown"
                style="cursor: pointer;">
                <div class="{{ $userBadgeColor }} text-white rounded-circle d-flex align-items-center justify-content-center mr-2 shadow-sm"
                    style="width:34px; height:34px;">
                    <i class="fas {{ $userIcon }}"></i>
                </div>
                <div class="d-none d-md-block text-left" style="line-height: 1.2;">
                    <span class="d-block font-weight-bold text-dark" style="font-size: 13px;">{{ $userName }}</span>
                    <small class="text-muted font-weight-600" style="font-size: 11px;">
                        <i class="fas fa-circle text-success mr-1" style="font-size:8px;"></i>{{ $userRole }}
                    </small>
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow-sm border-0 rounded-lg py-2 mt-2"
                style="min-width: 200px;">
                <div class="px-3 py-2 border-bottom">
                    <strong class="d-block text-dark small">{{ $userName }}</strong>
                    <small class="text-muted">{{ $currentUser ? $currentUser->email : 'admin@irc.co.id' }}</small>
                </div>

                @if ($currentUser && $currentUser->isAdmin())
                    <a href="{{ url('/settings') }}" class="dropdown-item py-2 text-dark small">
                        <i class="fas fa-cog text-muted mr-2"></i> Pengaturan Akun
                    </a>
                    <div class="dropdown-divider"></div>
                @endif

                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="dropdown-item py-2 text-danger font-weight-bold small">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout System
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
