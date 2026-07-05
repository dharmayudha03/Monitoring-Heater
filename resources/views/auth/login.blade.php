<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PT IRC INOAC Indonesia</title>

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
        }

        body {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.45) 0%, rgba(15, 23, 42, 0.65) 100%), url('{{ asset('images/company_bg.jpg') }}');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }

        .login-box-container {
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            padding: 30px 30px 24px 30px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .company-logo {
            max-height: 52px;
            width: auto;
            object-fit: contain;
        }

        .form-control-corp {
            height: 48px;
            border-color: #cbd5e1;
            font-size: 14px;
            color: #0f172a;
            background-color: #ffffff;
        }

        .form-control-corp:focus {
            border-color: #0284c7;
            box-shadow: none;
            background-color: #ffffff;
        }

        .input-group-text-corp {
            background-color: #ffffff;
            border-color: #cbd5e1;
            color: #64748b;
        }

        .btn-toggle-eye {
            background-color: #ffffff;
            border-color: #cbd5e1;
            color: #64748b;
            transition: all 0.2s;
        }

        .btn-toggle-eye:hover,
        .btn-toggle-eye:focus {
            background-color: #f8fafc;
            color: #0284c7;
            box-shadow: none;
        }

        .btn-corp-login {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            font-weight: 600;
            border-radius: 10px;
            height: 48px;
            font-size: 15px;
            border: none;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
            transition: all 0.2s ease-in-out;
        }

        .btn-corp-login:hover {
            background: linear-gradient(135deg, #0369a1 0%, #075985 100%);
            color: #ffffff;
            box-shadow: 0 6px 16px rgba(2, 132, 199, 0.4);
        }

        .header-title {
            color: #0f172a;
            font-weight: 700;
            font-size: 19px;
            margin-bottom: 2px;
            letter-spacing: -0.3px;
        }

        .header-subtitle {
            color: #64748b;
            font-size: 12px;
            font-weight: 500;
        }
    </style>
</head>

<body>

    <div class="login-box-container">
        <!-- Company Logo & Header -->
        <div class="text-center mb-3">
            <img src="{{ asset('images/logo.png') }}" alt="PT IRC INOAC Indonesia" class="company-logo mb-2">
            <h1 class="header-title">Heater Monitoring System</h1>
            <p class="header-subtitle mb-0">PT IRC INOAC Indonesia</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger rounded-lg small py-2 px-3 mb-3" role="alert">
                <i class="fas fa-exclamation-circle mr-1"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            <!-- Email -->
            <div class="form-group mb-3">
                <label class="small font-weight-bold text-dark mb-1">Email / Username</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text input-group-text-corp border-right-0"
                            style="border-radius: 10px 0 0 10px;">
                            <i class="fas fa-envelope"></i>
                        </span>
                    </div>
                    <input type="email" name="email" id="email"
                        class="form-control form-control-corp border-left-0" placeholder="Masukkan email anda"
                        value="{{ old('email') }}" style="border-radius: 0 10px 10px 0;" required>
                </div>
            </div>

            <!-- Password with Clean Button Eye Toggle -->
            <div class="form-group mb-3">
                <label class="small font-weight-bold text-dark mb-1">Password</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text input-group-text-corp border-right-0"
                            style="border-radius: 10px 0 0 10px;">
                            <i class="fas fa-lock"></i>
                        </span>
                    </div>
                    <input type="password" name="password" id="password"
                        class="form-control form-control-corp border-left-0 border-right-0"
                        placeholder="Masukkan password" required>
                    <div class="input-group-append">
                        <button class="btn btn-toggle-eye" type="button" id="togglePassword"
                            style="border-radius: 0 10px 10px 0; border: 1px solid #cbd5e1; border-left: 0;">
                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="d-flex justify-content-between align-items-center mb-3 pt-1">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="remember" class="custom-control-input" id="rememberMe">
                    <label class="custom-control-label small text-muted font-weight-normal" for="rememberMe"
                        style="cursor: pointer;">Ingat saya di perangkat ini</label>
                </div>
            </div>

            <button type="submit" class="btn btn-corp-login btn-block">
                Masuk ke Sistem <i class="fas fa-sign-in-alt ml-2"></i>
            </button>
        </form>

        <!-- Footer -->
        <div class="text-center mt-3 pt-2 border-top">
            <small class="text-muted" style="font-size: 11px;">&copy; 2026 PT IRC INOAC Indonesia. All Rights
                Reserved.</small>
        </div>
    </div>

    <!-- Toggle Password & Focus Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('togglePassword');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    const passwordInput = document.getElementById('password');
                    const icon = document.getElementById('togglePasswordIcon');
                    const isPassword = passwordInput.getAttribute('type') === 'password';

                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    icon.className = isPassword ? 'fas fa-eye-slash text-primary' : 'fas fa-eye';
                });
            }

            @if ($errors->any())
                const pwdInput = document.getElementById('password');
                if (pwdInput) {
                    pwdInput.focus();
                    pwdInput.select();
                }
            @else
                const emailInput = document.getElementById('email');
                if (emailInput && !emailInput.value) {
                    emailInput.focus();
                } else if (emailInput && emailInput.value) {
                    const pwdInput = document.getElementById('password');
                    if (pwdInput) pwdInput.focus();
                }
            @endif
        });
    </script>
</body>

</html>
