<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: #1a233a;">

    <!-- Brand Logo -->
    <a href="{{ url('/') }}"
        class="brand-link border-bottom border-secondary d-flex align-items-center py-2 px-3 justify-content-center">
        <img src="{{ asset('images/logo.png') }}" alt="IRC Logo" class="brand-image img-fluid"
            style="max-height: 40px; width: auto; object-fit: contain; margin-left: 0; opacity: 1;">
    </a>

    <!-- Sidebar -->
    <div class="sidebar px-2 pt-3">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                <!-- Dashboard (All Roles) -->
                <li class="nav-item mb-1">
                    <a href="{{ url('/') }}"
                        class="nav-link py-2 px-3 rounded-lg {{ Request::is('/') || Request::is('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-th-large mr-2"></i>
                        <p class="font-weight-600 mb-0">Dashboard</p>
                    </a>
                </li>

                <!-- Monitoring Heater (All Roles) -->
                <li class="nav-item mb-1">
                    <a href="{{ url('/monitoring') }}"
                        class="nav-link py-2 px-3 rounded-lg {{ Request::is('monitoring*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-fire mr-2"></i>
                        <p class="font-weight-600 mb-0">Monitoring Heater</p>
                    </a>
                </li>

                <!-- History (All Roles) -->
                <li class="nav-item mb-1">
                    <a href="{{ url('/history') }}"
                        class="nav-link py-2 px-3 rounded-lg {{ Request::is('history*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-line mr-2"></i>
                        <p class="font-weight-600 mb-0">History</p>
                    </a>
                </li>

                <!-- Alerts (All Roles) -->
                <li class="nav-item mb-1">
                    <a href="{{ url('/alerts') }}"
                        class="nav-link py-2 px-3 rounded-lg {{ Request::is('alerts*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-bell mr-2"></i>
                        <p class="font-weight-600 mb-0">Alerts</p>
                    </a>
                </li>

                <!-- Reports (All Roles) -->
                <li class="nav-item mb-1">
                    <a href="{{ url('/reports') }}"
                        class="nav-link py-2 px-3 rounded-lg {{ Request::is('reports*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-alt mr-2"></i>
                        <p class="font-weight-600 mb-0">Reports</p>
                    </a>
                </li>

                <!-- ADMIN ONLY MENUS -->
                @if (Auth::check() && Auth::user()->isAdmin())
                    <li class="nav-header text-white-50 font-weight-bold px-3 pt-3 pb-1"
                        style="font-size: 10px; letter-spacing: 1px;">ADMINISTRATION</li>

                    <!-- Replacement (Admin Only) -->
                    <li class="nav-item mb-1">
                        <a href="{{ url('/replacement') }}"
                            class="nav-link py-2 px-3 rounded-lg {{ Request::is('replacement*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tools mr-2 text-warning"></i>
                            <p class="font-weight-600 mb-0">Replacement</p>
                        </a>
                    </li>

                    <!-- Telegram Logs (Admin Only) -->
                    <li class="nav-item mb-1">
                        <a href="{{ url('/telegram-logs') }}"
                            class="nav-link py-2 px-3 rounded-lg {{ Request::is('telegram-logs*') ? 'active' : '' }}">
                            <i class="nav-icon fab fa-telegram-plane mr-2 text-info"></i>
                            <p class="font-weight-600 mb-0">Telegram Logs</p>
                        </a>
                    </li>

                    <!-- Settings (Admin Only) -->
                    <li class="nav-item mb-1">
                        <a href="{{ url('/settings') }}"
                            class="nav-link py-2 px-3 rounded-lg {{ Request::is('settings*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cog mr-2 text-primary"></i>
                            <p class="font-weight-600 mb-0">Settings</p>
                        </a>
                    </li>

                    <!-- Kelola User & Password (Super Admin Only) -->
                    @if (Auth::user()->isSuperAdmin())
                        <li class="nav-item mb-1">
                            <a href="{{ url('/users') }}"
                                class="nav-link py-2 px-3 rounded-lg {{ Request::is('users*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users-cog mr-2 text-success"></i>
                                <p class="font-weight-600 mb-0">Kelola User & Password</p>
                            </a>
                        </li>
                    @endif
                @endif

            </ul>
        </nav>
    </div>
</aside>
