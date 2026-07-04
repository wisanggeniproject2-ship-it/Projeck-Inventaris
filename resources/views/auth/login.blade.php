<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Inventory Management System</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * { 
            font-family: 'Inter', sans-serif; 
        }

        .login-gradient {
            background: linear-gradient(135deg, #0D9488 0%, #0F766E 40%, #115E59 70%, #134E4A 100%);
            background-size: 400% 400%;
            animation: gradientShift 8s ease-in-out infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            animation: cardFloat 3s ease-in-out infinite;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        @keyframes cardFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        .logo-container {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            border-radius: 20px;
            background: rgba(13, 148, 136, 0.08);
            transition: all 0.3s ease;
        }

        .logo-container:hover {
            transform: scale(1.05) rotate(-3deg);
            background: rgba(13, 148, 136, 0.15);
            box-shadow: 0 0 40px rgba(13, 148, 136, 0.15);
        }

        .logo-container img {
            width: 64px;
            height: 64px;
            object-fit: contain;
            filter: drop-shadow(0 0 20px rgba(13, 148, 136, 0.2));
        }

        .input-field {
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb;
        }

        .input-field:focus {
            border-color: #0D9488;
            box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.1);
            outline: none;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            transition: all 0.3s ease;
            font-size: 16px;
        }

        .input-wrapper:focus-within .input-icon {
            color: #0D9488;
        }

        .btn-login {
            background: linear-gradient(135deg, #0D9488, #0F766E);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            min-height: 52px;
        }

        .btn-login:hover {
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 10px 40px rgba(13, 148, 136, 0.35);
        }

        .btn-login:active {
            transform: scale(0.98);
        }

        .btn-login::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-login:hover::after {
            opacity: 1;
        }

        /* ===== LOADING STATE - FIXED CENTER ===== */
        .btn-login.loading {
            pointer-events: none;
            opacity: 0.85;
        }

        .btn-login.loading .btn-text {
            opacity: 0;
            visibility: hidden;
            position: absolute;
        }

        .btn-login.loading .btn-spinner {
            display: block;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .btn-spinner {
            display: none;
            width: 24px;
            height: 24px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .role-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
            cursor: pointer;
        }

        .role-card:hover {
            transform: translateY(-3px);
            border-color: #0D9488;
            background: rgba(13, 148, 136, 0.05);
            box-shadow: 0 4px 20px rgba(13, 148, 136, 0.08);
        }

        .role-card .role-icon {
            transition: all 0.3s ease;
        }

        .role-card:hover .role-icon {
            transform: scale(1.1);
        }

        .back-link {
            transition: all 0.3s ease;
        }

        .back-link:hover {
            transform: translateX(-4px);
            color: white !important;
        }

        .shake {
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-8px); }
            40% { transform: translateX(8px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
        }

        @media (max-width: 640px) {
            .logo-container {
                width: 64px;
                height: 64px;
            }
            .logo-container img {
                width: 50px;
                height: 50px;
            }
            .role-card {
                padding: 8px 10px;
            }
            .role-card .role-icon {
                font-size: 18px;
            }
        }
    </style>
</head>
<body class="login-gradient min-h-screen flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-md">
        <!-- Back to Landing -->
        <a href="{{ route('landing') }}" class="back-link text-white/70 hover:text-white transition inline-flex items-center text-sm font-medium mb-5 group">
            <i class="fas fa-arrow-left mr-2 text-xs transition-transform group-hover:-translate-x-1"></i>
            Kembali ke Beranda
        </a>
        
        <!-- Login Card -->
        <div class="login-card rounded-2xl shadow-2xl p-8 sm:p-10">
            <!-- Logo -->
            <div class="logo-container">
                <img src="{{ asset('images/logopermata.png') }}" alt="Logo Permata">
            </div>
            
            <div class="text-center mb-7">
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Selamat Datang</h2>
                <p class="text-gray-500 text-sm mt-1">Silakan login untuk melanjutkan</p>
            </div>
            
            <!-- Error Message -->
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm flex items-start gap-2 shake">
                    <i class="fas fa-exclamation-circle mt-0.5 text-red-500"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif
            
            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                
                <!-- Email -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-1.5">Email</label>
                    <div class="input-wrapper relative">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" value="admin@yayasan.com" 
                               class="input-field w-full pl-11 pr-4 py-3 rounded-xl text-gray-700 placeholder-gray-400 text-sm"
                               placeholder="admin@yayasan.com" required>
                    </div>
                </div>
                
                <!-- Password -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="block text-gray-700 text-sm font-semibold">Password</label>
                        <a href="#" class="text-xs text-teal-600 hover:text-teal-700 transition font-medium">Lupa password?</a>
                    </div>
                    <div class="input-wrapper relative">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" value="password"
                               class="input-field w-full pl-11 pr-4 py-3 rounded-xl text-gray-700 placeholder-gray-400 text-sm"
                               placeholder="********" required>
                    </div>
                </div>
                
                <!-- Remember Me -->
                <div class="flex items-center justify-between mb-7">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-teal-600 focus:ring-2 focus:ring-teal-400 transition">
                        <span class="ml-2 text-sm text-gray-600 group-hover:text-gray-800 transition">Ingat saya</span>
                    </label>
                    <span class="text-xs text-gray-400">v2.0.1</span>
                </div>
                
                <!-- Login Button -->
                <button type="submit" class="btn-login w-full text-white py-3.5 rounded-xl font-semibold text-sm flex items-center justify-center relative" id="loginBtn">
                    <span class="btn-text"><i class="fas fa-sign-in-alt mr-2"></i>Login</span>
                    <span class="btn-spinner"></span>
                </button>
            </form>
            
            <!-- Demo Accounts -->
            <div class="mt-7 pt-6 border-t border-gray-100">
                <p class="text-center text-xs text-gray-400 font-medium uppercase tracking-wider mb-3">Demo Account</p>
                <div class="grid grid-cols-3 gap-2">
                    <div class="role-card bg-gray-50/80 rounded-xl p-2.5 text-center" data-email="admin@yayasan.com">
                        <i class="fas fa-user-shield role-icon text-teal-600 text-lg block mb-0.5"></i>
                        <span class="font-semibold text-gray-700 text-xs block">Admin</span>
                        <p class="text-gray-400 text-[10px] truncate">admin@yayasan.com</p>
                    </div>
                    <div class="role-card bg-gray-50/80 rounded-xl p-2.5 text-center" data-email="manager@smp.com">
                        <i class="fas fa-user-tie role-icon text-blue-600 text-lg block mb-0.5"></i>
                        <span class="font-semibold text-gray-700 text-xs block">Manager</span>
                        <p class="text-gray-400 text-[10px] truncate">manager@smp.com</p>
                    </div>
                    <div class="role-card bg-gray-50/80 rounded-xl p-2.5 text-center" data-email="user@yayasan.com">
                        <i class="fas fa-user role-icon text-emerald-600 text-lg block mb-0.5"></i>
                        <span class="font-semibold text-gray-700 text-xs block">User</span>
                        <p class="text-gray-400 text-[10px] truncate">user@yayasan.com</p>
                    </div>
                </div>
                <p class="text-center text-[11px] text-gray-400 mt-2">
                    Password: <span class="font-mono bg-gray-100 px-2 py-0.5 rounded text-gray-600">password</span>
                </p>
            </div>
        </div>
        
        <!-- Footer -->
        <p class="text-center text-white/50 text-xs mt-6">
            &copy; {{ date('Y') }} Inventory Management System
        </p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const btn = document.getElementById('loginBtn');

            form.addEventListener('submit', function(e) {
                btn.classList.add('loading');
            });

            // Auto-fill demo account on role click
            document.querySelectorAll('.role-card').forEach((card) => {
                card.addEventListener('click', function() {
                    const email = this.dataset.email;
                    const emailInput = document.querySelector('input[name="email"]');
                    emailInput.value = email;
                    emailInput.focus();
                    emailInput.classList.add('border-teal-500');
                    setTimeout(() => {
                        emailInput.classList.remove('border-teal-500');
                    }, 500);
                });
            });

            // Input focus effects
            document.querySelectorAll('.input-field').forEach(input => {
                input.addEventListener('focus', function() {
                    this.closest('.input-wrapper').querySelector('.input-icon').style.color = '#0D9488';
                });
                input.addEventListener('blur', function() {
                    if (!this.value) {
                        this.closest('.input-wrapper').querySelector('.input-icon').style.color = '#9ca3af';
                    }
                });
                if (input.value) {
                    input.closest('.input-wrapper').querySelector('.input-icon').style.color = '#0D9488';
                }
            });
        });
    </script>
</body>
</html>