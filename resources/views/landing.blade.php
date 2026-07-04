<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inventory Management System - Yayasan</title>
    
    <!-- ===== FAVICON - LOGO PERMATA ===== -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logopermata.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logopermata.png') }}">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('images/logopermata.png') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('images/logopermata.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/logopermata.png') }}">
    <link rel="apple-touch-icon-precomposed" href="{{ asset('images/logopermata.png') }}">
    <meta name="msapplication-TileImage" content="{{ asset('images/logopermata.png') }}">
    <meta name="msapplication-TileColor" content="#0D9488">
    <meta name="theme-color" content="#0D9488">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        /* ===== LOADING SCREEN ===== */
        #loading-screen {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: linear-gradient(135deg, #0D9488 0%, #0F766E 40%, #115E59 70%, #134E4A 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: opacity 0.8s ease, visibility 0.8s ease;
        }

        #loading-screen.hide {
            opacity: 0;
            visibility: hidden;
        }

        .loading-logo {
            width: 200px;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .loading-logo img {
            width: 180px;
            height: 180px;
            object-fit: contain;
            filter: drop-shadow(0 0 50px rgba(13, 148, 136, 0.7));
            animation: logoPulse 2s ease-in-out infinite;
        }

        @keyframes logoPulse {
            0%, 100% { transform: scale(1); opacity: 0.9; }
            50% { transform: scale(1.08); opacity: 1; }
        }

        .loading-box {
            display: grid;
            grid-template-columns: 24px 24px;
            grid-template-rows: 24px 24px;
            gap: 5px;
        }

        .loading-box .panel {
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.08);
            animation: panelPulse 1.5s ease-in-out infinite;
        }

        .loading-box .panel:nth-child(1) { animation-delay: 0s; }
        .loading-box .panel:nth-child(2) { animation-delay: 0.3s; }
        .loading-box .panel:nth-child(3) { animation-delay: 0.6s; }
        .loading-box .panel:nth-child(4) { animation-delay: 0.9s; }

        @keyframes panelPulse {
            0%, 100% { 
                background: rgba(255, 255, 255, 0.04);
                transform: scale(1);
            }
            50% { 
                background: rgba(255, 255, 255, 0.25);
                transform: scale(1.15);
                border-color: rgba(255, 255, 255, 0.3);
                box-shadow: 0 0 30px rgba(255, 255, 255, 0.05);
            }
        }

        /* ===== HERO ===== */
        .hero-gradient {
            background: linear-gradient(135deg, #0D9488 0%, #0F766E 35%, #115E59 65%, #134E4A 100%);
            background-size: 400% 400%;
            animation: gradientShift 10s ease-in-out infinite;
            position: relative;
            overflow: hidden;
        }

        .hero-gradient::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.03) 0%, transparent 70%);
            animation: heroGlow 8s ease-in-out infinite;
        }

        @keyframes heroGlow {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-30%, -20%); }
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            animation: floatParticle 15s infinite ease-in-out;
            pointer-events: none;
        }

        .particle:nth-child(1) { width: 300px; height: 300px; top: -100px; right: -100px; animation-delay: 0s; }
        .particle:nth-child(2) { width: 200px; height: 200px; bottom: -50px; left: -50px; animation-delay: -3s; }
        .particle:nth-child(3) { width: 150px; height: 150px; top: 50%; right: 20%; animation-delay: -6s; }

        @keyframes floatParticle {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.3; }
            33% { transform: translate(30px, -40px) scale(1.2); opacity: 0.6; }
            66% { transform: translate(-20px, 20px) scale(0.8); opacity: 0.4; }
        }

        .fade-in {
            animation: fadeIn 0.8s ease forwards;
        }

        .fade-in-delay-1 { animation-delay: 0.15s; opacity: 0; }
        .fade-in-delay-2 { animation-delay: 0.3s; opacity: 0; }
        .fade-in-delay-3 { animation-delay: 0.45s; opacity: 0; }
        .fade-in-delay-4 { animation-delay: 0.6s; opacity: 0; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(25px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .float-slow {
            animation: floatSlow 5s ease-in-out infinite;
        }

        @keyframes floatSlow {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-12px) rotate(1deg); }
        }

        .card-hover {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .card-hover::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(13, 148, 136, 0.05), transparent);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .card-hover:hover::before {
            opacity: 1;
        }

        .card-hover:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 25px 50px rgba(13, 148, 136, 0.2);
            border-color: #0D9488;
        }

        .card-hover .feature-icon {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover .feature-icon {
            transform: scale(1.15) rotate(-5deg);
            box-shadow: 0 8px 30px rgba(13, 148, 136, 0.2);
        }

        .btn-gradient {
            background: linear-gradient(135deg, #0D9488, #0F766E);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-gradient::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-gradient:hover::after {
            opacity: 1;
        }

        .btn-gradient:hover {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 12px 40px rgba(13, 148, 136, 0.4);
        }

        .btn-outline {
            border: 2px solid rgba(255, 255, 255, 0.6);
            transition: all 0.3s ease;
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-3px) scale(1.03);
            border-color: white;
            box-shadow: 0 12px 40px rgba(255, 255, 255, 0.1);
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stat-card:hover {
            transform: scale(1.06) translateY(-4px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
            border-color: #0D9488;
        }

        .stat-card .stat-number {
            transition: all 0.4s ease;
        }

        .stat-card:hover .stat-number {
            transform: scale(1.1);
        }

        .qr-scanner {
            animation: scanLine 2.5s ease-in-out infinite;
        }

        @keyframes scanLine {
            0% { transform: translateY(-25px); opacity: 0; }
            50% { transform: translateY(25px); opacity: 1; }
            100% { transform: translateY(-25px); opacity: 0; }
        }

        .qr-pulse {
            animation: pulseRing 2.5s ease-in-out infinite;
        }

        @keyframes pulseRing {
            0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.25); }
            70% { box-shadow: 0 0 0 25px rgba(255, 255, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
        }

        .qr-box-item {
            transition: all 0.3s ease;
        }

        .qr-box-item:hover {
            background: rgba(255, 255, 255, 0.25) !important;
            transform: scale(1.05);
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            transition: all 0.4s ease;
        }

        /* ===== NAVBAR ===== */
        .navbar-logo {
            height: 60px;
            width: auto;
            object-fit: contain;
            transition: all 0.3s ease;
        }

        .navbar-logo:hover {
            transform: scale(1.05);
            filter: drop-shadow(0 0 20px rgba(13, 148, 136, 0.2));
        }

        .nav-link {
            position: relative;
            transition: all 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: #0D9488;
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .nav-link:hover {
            color: #0D9488;
        }

        /* ===== FOOTER ===== */
        .footer-logo {
            height: 48px;
            width: auto;
            object-fit: contain;
            filter: brightness(0) invert(1);
            opacity: 0.8;
            transition: all 0.3s ease;
        }

        .footer-logo:hover {
            opacity: 1;
            transform: scale(1.05);
        }

        .social-link {
            transition: all 0.3s ease;
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .social-link:hover {
            background: rgba(13, 148, 136, 0.3);
            border-color: #0D9488;
            transform: translateY(-4px) scale(1.1);
            box-shadow: 0 8px 25px rgba(13, 148, 136, 0.2);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 640px) {
            .navbar-logo {
                height: 44px;
            }
            .loading-logo {
                width: 140px;
                height: 140px;
            }
            .loading-logo img {
                width: 120px;
                height: 120px;
            }
            .loading-box {
                grid-template-columns: 18px 18px;
                grid-template-rows: 18px 18px;
                gap: 3px;
            }
            .hero-title {
                font-size: 28px !important;
            }
            .hero-sub {
                font-size: 14px !important;
            }
            .stat-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 6px;
            }
            .stat-card {
                padding: 10px 6px;
            }
            .stat-card .stat-number {
                font-size: 18px !important;
            }
            .stat-card p:last-child {
                font-size: 9px;
            }
            .feature-icon {
                width: 44px;
                height: 44px;
                font-size: 18px;
            }
            .footer-logo {
                height: 36px;
            }
        }

        @media (min-width: 641px) and (max-width: 1024px) {
            .hero-title {
                font-size: 38px !important;
            }
            .stat-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 12px;
            }
            .navbar-logo {
                height: 52px;
            }
        }

        html {
            scroll-behavior: smooth;
        }

        section {
            position: relative;
        }
    </style>
</head>
<body>

    <!-- ===== LOADING SCREEN ===== -->
    <div id="loading-screen">
        <div class="loading-logo">
            <img src="{{ asset('images/logopermata.png') }}" alt="Logo Permata">
        </div>
        <div class="loading-box">
            <div class="panel"></div>
            <div class="panel"></div>
            <div class="panel"></div>
            <div class="panel"></div>
        </div>
    </div>

    <!-- ===== NAVBAR ===== -->
    <nav class="bg-white/95 backdrop-blur-xl shadow-lg fixed w-full z-50 top-0 transition-all duration-300 border-b border-white/20" id="navbar">
        <div class="container mx-auto px-4 sm:px-6 py-3 sm:py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <img src="{{ asset('images/logopermata.png') }}" alt="Logo Permata" class="navbar-logo">
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="nav-link text-gray-600 hover:text-teal-600 transition font-medium text-sm">Fitur</a>
                    <a href="#about" class="nav-link text-gray-600 hover:text-teal-600 transition font-medium text-sm">Tentang</a>
                    <a href="{{ route('login') }}" class="btn-gradient text-white px-6 py-2.5 rounded-xl hover:shadow-lg transition font-medium text-sm flex items-center gap-2">
                        <i class="fas fa-sign-in-alt"></i>Login
                    </a>
                </div>

                <button id="mobileMenuBtn" class="md:hidden text-gray-700 text-2xl focus:outline-none hover:text-teal-600 transition">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <div id="mobileMenu" class="hidden md:hidden mt-4 pb-2 border-t border-gray-100 pt-4">
                <a href="#features" class="block py-2.5 text-gray-600 hover:text-teal-600 transition font-medium text-sm">Fitur</a>
                <a href="#about" class="block py-2.5 text-gray-600 hover:text-teal-600 transition font-medium text-sm">Tentang</a>
                <a href="{{ route('login') }}" class="block btn-gradient text-white px-6 py-2.5 rounded-xl text-center mt-2 text-sm">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
            </div>
        </div>
    </nav>

    <!-- ===== HERO SECTION ===== -->
    <section class="hero-gradient min-h-screen flex items-center pt-16 overflow-hidden relative">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        
        <div class="container mx-auto px-4 sm:px-6 py-12 sm:py-20 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                <div class="text-center lg:text-left">
                    <div class="inline-block bg-white/20 backdrop-blur-sm px-5 py-2 rounded-full mb-5 fade-in border border-white/20">
                        <span class="text-white text-xs sm:text-sm font-medium">
                            <i class="fas fa-check-circle mr-2"></i>Sistem Manajemen Inventaris
                        </span>
                    </div>
                    
                    <h1 class="hero-title text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-extrabold text-white leading-tight mb-5 fade-in fade-in-delay-1">
                        Kelola Inventaris
                        <span class="text-yellow-300 relative">
                            Yayasan
                            <svg class="absolute -bottom-2 left-0 w-full h-3 text-yellow-300/30" viewBox="0 0 100 10">
                                <path d="M0,5 Q25,0 50,5 Q75,10 100,5" stroke="currentColor" fill="none" stroke-width="2"/>
                            </svg>
                        </span>
                        <br>Dengan Mudah
                    </h1>
                    
                    <p class="hero-sub text-base sm:text-lg lg:text-xl text-white/90 mb-7 max-w-lg mx-auto lg:mx-0 fade-in fade-in-delay-2 leading-relaxed">
                        Sistem manajemen inventaris berbasis multi-unit untuk yayasan. 
                        Kelola barang, peminjaman, dan laporan dengan efisien.
                    </p>
                    
                    <div class="flex flex-wrap gap-4 justify-center lg:justify-start fade-in fade-in-delay-3">
                        <a href="{{ route('login') }}" class="bg-white text-teal-600 px-8 sm:px-10 py-3.5 rounded-xl font-semibold hover:shadow-2xl transition flex items-center gap-2 text-sm hover:scale-105">
                            <i class="fas fa-sign-in-alt"></i>Login Sekarang
                        </a>
                        <a href="#features" class="btn-outline text-white px-8 sm:px-10 py-3.5 rounded-xl font-semibold transition flex items-center gap-2 text-sm">
                            <i class="fas fa-chevron-down"></i>Pelajari
                        </a>
                    </div>
                    
                    <div class="stat-grid grid grid-cols-3 gap-3 sm:gap-4 mt-10 fade-in fade-in-delay-4">
                        <div class="stat-card rounded-xl p-3 sm:p-4 text-center shadow-xl">
                            <p class="stat-number text-2xl sm:text-3xl font-bold text-teal-600">{{ $totalItems ?? 0 }}</p>
                            <p class="text-xs sm:text-sm text-gray-600 font-medium">Total Barang</p>
                        </div>
                        <div class="stat-card rounded-xl p-3 sm:p-4 text-center shadow-xl">
                            <p class="stat-number text-2xl sm:text-3xl font-bold text-emerald-600">{{ $availableItems ?? 0 }}</p>
                            <p class="text-xs sm:text-sm text-gray-600 font-medium">Tersedia</p>
                        </div>
                        <div class="stat-card rounded-xl p-3 sm:p-4 text-center shadow-xl">
                            <p class="stat-number text-2xl sm:text-3xl font-bold text-blue-600">{{ $totalUnits ?? 0 }}</p>
                            <p class="text-xs sm:text-sm text-gray-600 font-medium">Total Unit</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-center lg:justify-end fade-in fade-in-delay-2">
                    <div class="float-slow w-full max-w-md">
                        <div class="bg-white/15 backdrop-blur-xl rounded-2xl p-6 sm:p-8 border border-white/25 shadow-2xl qr-pulse">
                            <div class="text-white text-center">
                                <div class="relative inline-block">
                                    <div class="bg-white/10 rounded-2xl p-4">
                                        <i class="fas fa-qrcode text-7xl sm:text-8xl"></i>
                                    </div>
                                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-20 h-1 bg-yellow-300 rounded-full qr-scanner"></div>
                                </div>
                                <h3 class="text-xl sm:text-2xl font-bold mt-3">QR Code System</h3>
                                <p class="text-white/80 text-sm sm:text-base mt-1">Scan QR Code untuk detail barang</p>
                            </div>
                            <div class="mt-6 grid grid-cols-2 gap-3 sm:gap-4">
                                <div class="qr-box-item bg-white/10 rounded-xl p-3 sm:p-4 text-center hover:bg-white/20 transition cursor-default">
                                    <i class="fas fa-boxes text-2xl sm:text-3xl text-yellow-300"></i>
                                    <p class="text-xs sm:text-sm text-white/80 mt-1 font-medium">Multi Unit</p>
                                </div>
                                <div class="qr-box-item bg-white/10 rounded-xl p-3 sm:p-4 text-center hover:bg-white/20 transition cursor-default">
                                    <i class="fas fa-chart-line text-2xl sm:text-3xl text-green-300"></i>
                                    <p class="text-xs sm:text-sm text-white/80 mt-1 font-medium">Real Time</p>
                                </div>
                                <div class="qr-box-item bg-white/10 rounded-xl p-3 sm:p-4 text-center hover:bg-white/20 transition cursor-default">
                                    <i class="fas fa-shield-alt text-2xl sm:text-3xl text-blue-300"></i>
                                    <p class="text-xs sm:text-sm text-white/80 mt-1 font-medium">Secure</p>
                                </div>
                                <div class="qr-box-item bg-white/10 rounded-xl p-3 sm:p-4 text-center hover:bg-white/20 transition cursor-default">
                                    <i class="fas fa-users text-2xl sm:text-3xl text-purple-300"></i>
                                    <p class="text-xs sm:text-sm text-white/80 mt-1 font-medium">Multi Role</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== FEATURES SECTION ===== -->
    <section id="features" class="py-16 sm:py-20 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="text-center mb-12 sm:mb-16">
                <span class="inline-block bg-teal-100 text-teal-600 px-4 py-1.5 rounded-full text-sm font-medium mb-3">Fitur Unggulan</span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-800 mb-3">
                    Fitur <span class="text-teal-600">Lengkap</span>
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto text-sm sm:text-base">
                    Sistem inventory management dengan fitur lengkap untuk kebutuhan yayasan Anda
                </p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 card-hover border border-gray-100">
                    <div class="feature-icon bg-teal-100 text-teal-600 mb-4">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Manajemen Barang</h3>
                    <p class="text-gray-600 text-sm sm:text-base">Kelola semua barang dengan kode unik, kategori, lokasi, dan sumber anggaran.</p>
                </div>
                
                <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 card-hover border border-gray-100">
                    <div class="feature-icon bg-blue-100 text-blue-600 mb-4">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Sirkulasi Peminjaman</h3>
                    <p class="text-gray-600 text-sm sm:text-base">Sistem peminjaman dengan approval flow yang terstruktur dan transparan.</p>
                </div>
                
                <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 card-hover border border-gray-100">
                    <div class="feature-icon bg-emerald-100 text-emerald-600 mb-4">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">QR Code</h3>
                    <p class="text-gray-600 text-sm sm:text-base">Setiap barang dilengkapi QR Code untuk akses cepat ke informasi detail.</p>
                </div>
                
                <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 card-hover border border-gray-100">
                    <div class="feature-icon bg-amber-100 text-amber-600 mb-4">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Multi Unit System</h3>
                    <p class="text-gray-600 text-sm sm:text-base">Kelola beberapa unit dalam satu sistem terintegrasi.</p>
                </div>
                
                <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 card-hover border border-gray-100">
                    <div class="feature-icon bg-rose-100 text-rose-600 mb-4">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Multi Role Access</h3>
                    <p class="text-gray-600 text-sm sm:text-base">Hak akses berdasarkan role: Admin, Manager, dan User.</p>
                </div>
                
                <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 card-hover border border-gray-100">
                    <div class="feature-icon bg-indigo-100 text-indigo-600 mb-4">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Dashboard & Laporan</h3>
                    <p class="text-gray-600 text-sm sm:text-base">Dashboard informatif dengan data real-time dan laporan peminjaman.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== ABOUT SECTION ===== -->
    <section id="about" class="py-16 sm:py-20 bg-white">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">
                <div>
                    <span class="inline-block bg-teal-100 text-teal-600 px-4 py-1.5 rounded-full text-sm font-medium mb-3">Tentang</span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-800 mb-5">
                        Sistem <span class="text-teal-600">Inventaris</span>
                    </h2>
                    <p class="text-gray-600 text-sm sm:text-base mb-5 leading-relaxed">
                        Sistem Inventory Management ini dirancang khusus untuk yayasan dengan banyak unit. 
                        Dengan sistem ini, Anda dapat:
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start group">
                            <div class="bg-teal-100 rounded-full p-1 mt-0.5 mr-3 group-hover:bg-teal-200 transition">
                                <i class="fas fa-check text-teal-600 text-xs"></i>
                            </div>
                            <span class="text-gray-600 text-sm sm:text-base">Mengelola barang di setiap unit secara terpisah</span>
                        </li>
                        <li class="flex items-start group">
                            <div class="bg-teal-100 rounded-full p-1 mt-0.5 mr-3 group-hover:bg-teal-200 transition">
                                <i class="fas fa-check text-teal-600 text-xs"></i>
                            </div>
                            <span class="text-gray-600 text-sm sm:text-base">Melacak peminjaman barang dengan status real-time</span>
                        </li>
                        <li class="flex items-start group">
                            <div class="bg-teal-100 rounded-full p-1 mt-0.5 mr-3 group-hover:bg-teal-200 transition">
                                <i class="fas fa-check text-teal-600 text-xs"></i>
                            </div>
                            <span class="text-gray-600 text-sm sm:text-base">Memantau ketersediaan barang dengan mudah</span>
                        </li>
                        <li class="flex items-start group">
                            <div class="bg-teal-100 rounded-full p-1 mt-0.5 mr-3 group-hover:bg-teal-200 transition">
                                <i class="fas fa-check text-teal-600 text-xs"></i>
                            </div>
                            <span class="text-gray-600 text-sm sm:text-base">Mengakses data dari mana saja secara online</span>
                        </li>
                    </ul>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gradient-to-br from-teal-50 to-white rounded-2xl shadow-xl p-6 text-center card-hover border border-teal-100">
                        <div class="w-16 h-16 bg-teal-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-users text-3xl text-teal-600"></i>
                        </div>
                        <p class="font-bold text-3xl text-gray-800">{{ $totalItems ?? 0 }}</p>
                        <p class="text-sm text-gray-600 font-medium">Total Data Barang</p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-white rounded-2xl shadow-xl p-6 text-center card-hover border border-blue-100">
                        <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-building text-3xl text-blue-600"></i>
                        </div>
                        <p class="font-bold text-3xl text-gray-800">{{ $totalUnits ?? 0 }}</p>
                        <p class="text-sm text-gray-600 font-medium">Unit Aktif</p>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-50 to-white rounded-2xl shadow-xl p-6 text-center card-hover border border-emerald-100">
                        <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-qrcode text-3xl text-emerald-600"></i>
                        </div>
                        <p class="font-bold text-3xl text-gray-800">{{ $totalItems ?? 0 }}</p>
                        <p class="text-sm text-gray-600 font-medium">QR Code Aktif</p>
                    </div>
                    <div class="bg-gradient-to-br from-amber-50 to-white rounded-2xl shadow-xl p-6 text-center card-hover border border-amber-100">
                        <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-exchange-alt text-3xl text-amber-600"></i>
                        </div>
                        <p class="font-bold text-3xl text-gray-800">{{ $totalCirculations ?? 0 }}</p>
                        <p class="text-sm text-gray-600 font-medium">Total Sirkulasi</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CTA SECTION ===== -->
    <section class="hero-gradient py-16 sm:py-20 relative overflow-hidden">
        <div class="particle" style="width:200px;height:200px;bottom:-50px;right:-50px;animation-delay:-4s;"></div>
        <div class="container mx-auto px-4 sm:px-6 text-center relative z-10">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4">
                Siap Mengelola Inventaris?
            </h2>
            <p class="text-white/90 text-base sm:text-lg mb-7 max-w-2xl mx-auto">
                Login sekarang dan mulai kelola inventaris dengan lebih efisien dan terstruktur.
            </p>
            <a href="{{ route('login') }}" class="bg-white text-teal-600 px-10 sm:px-12 py-4 rounded-xl font-semibold hover:shadow-2xl transition inline-flex items-center gap-2 hover:scale-105 text-sm sm:text-base">
                <i class="fas fa-sign-in-alt"></i>Login Sekarang
            </a>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="bg-gray-900 text-white py-8 sm:py-12">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logopermata.png') }}" alt="Logo Permata" class="footer-logo">
                    <span class="text-white/60 text-sm font-medium">Inventory System</span>
                </div>
                <div class="text-sm text-gray-400 text-center">
                    &copy; {{ date('Y') }} Inventory Management System. All rights reserved.
                </div>
                <div class="flex space-x-3">
                    <a href="#" class="social-link text-gray-400 hover:text-white">
                        <i class="fab fa-github"></i>
                    </a>
                    <a href="#" class="social-link text-gray-400 hover:text-white">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link text-gray-400 hover:text-white">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="#" class="social-link text-gray-400 hover:text-white">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
            <div class="mt-6 pt-6 border-t border-gray-800 text-center text-xs text-gray-500">
                <p>Dibangun dengan <i class="fas fa-heart text-teal-500"></i> untuk Yayasan</p>
            </div>
        </div>
    </footer>

    <!-- ===== SCRIPTS ===== -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.getElementById('loading-screen').classList.add('hide');
            }, 2500);

            // Ganti favicon Laravel dengan logo Permata
            const favicon = document.querySelector('link[rel="icon"]');
            if (favicon) {
                favicon.href = "{{ asset('images/logopermata.png') }}";
            }
            
            // Tambahkan juga untuk apple touch icon
            const appleIcon = document.querySelector('link[rel="apple-touch-icon"]');
            if (appleIcon) {
                appleIcon.href = "{{ asset('images/logopermata.png') }}";
            }
        });

        const mobileBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        mobileBtn.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
            this.querySelector('i').classList.toggle('fa-bars');
            this.querySelector('i').classList.toggle('fa-times');
        });

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                    mobileMenu.classList.add('hidden');
                    const icon = mobileBtn.querySelector('i');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
        });

        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('shadow-lg', 'bg-white/95');
                navbar.classList.remove('shadow-sm', 'bg-white/90');
            } else {
                navbar.classList.remove('shadow-lg', 'bg-white/95');
                navbar.classList.add('shadow-sm', 'bg-white/90');
            }
        });

        function animateCounters() {
            document.querySelectorAll('.stat-number').forEach(el => {
                const target = parseInt(el.textContent);
                if (target > 0 && !el.dataset.animated) {
                    el.dataset.animated = 'true';
                    let current = 0;
                    const increment = Math.ceil(target / 30);
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= target) {
                            el.textContent = target;
                            clearInterval(timer);
                        } else {
                            el.textContent = current;
                        }
                    }, 30);
                }
            });
        }

        let counterTriggered = false;
        window.addEventListener('scroll', function() {
            if (!counterTriggered) {
                const statsSection = document.querySelector('.stat-grid');
                if (statsSection) {
                    const rect = statsSection.getBoundingClientRect();
                    if (rect.top < window.innerHeight) {
                        counterTriggered = true;
                        animateCounters();
                    }
                }
            }
        });
    </script>
</body>
</html>