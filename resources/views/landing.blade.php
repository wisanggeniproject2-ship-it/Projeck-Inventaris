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

        /* ===== HERO DENGAN BACKGROUND IMAGE ===== */
        .hero-bg {
            position: relative;
            background-image: url('{{ asset("images/hero-yayasan.jpg") }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            overflow: hidden;
        }

        .hero-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, 
                rgba(13, 148, 136, 0.92) 0%, 
                rgba(15, 118, 110, 0.88) 35%, 
                rgba(17, 94, 89, 0.92) 65%, 
                rgba(19, 78, 74, 0.95) 100%);
            z-index: 1;
        }

        .hero-bg::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
            animation: heroGlow 8s ease-in-out infinite;
            z-index: 2;
            pointer-events: none;
        }

        @keyframes heroGlow {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-30%, -20%); }
        }

        .hero-content {
            position: relative;
            z-index: 10;
        }

        .hero-gradient-fallback {
            background: linear-gradient(135deg, #0D9488 0%, #0F766E 35%, #115E59 65%, #134E4A 100%);
            background-size: 400% 400%;
            animation: gradientShift 10s ease-in-out infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* ===== CTA SECTION (DIPERPENDEK) ===== */
        .cta-section {
            position: relative;
            background: linear-gradient(135deg, #0D9488 0%, #0F766E 50%, #115E59 100%);
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url('{{ asset("images/hero-yayasan.jpg") }}');
            background-size: cover;
            background-position: center;
            opacity: 0.15;
            z-index: 1;
        }

        .cta-section::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: heroGlow 8s ease-in-out infinite;
            z-index: 2;
            pointer-events: none;
        }

        .cta-content {
            position: relative;
            z-index: 10;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.06);
            animation: floatParticle 15s infinite ease-in-out;
            pointer-events: none;
            z-index: 3;
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
            border-radius: 24px;
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
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.05);
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
            border-radius: 20px;
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
            border-radius: 16px;
        }

        .qr-box-item:hover {
            background: rgba(255, 255, 255, 0.25) !important;
            transform: scale(1.05);
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            transition: all 0.4s ease;
        }

        /* ===== NAVBAR ===== */
        .navbar-logo {
            height: 55px;
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
            padding: 8px 0;
            font-weight: 500;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #0D9488, #14B8A6);
            transition: width 0.3s ease;
            border-radius: 2px;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .nav-link:hover {
            color: #0D9488;
        }

        .navbar-glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
        }

        .navbar-glass.scrolled {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.08);
        }

        .navbar-container {
            border-radius: 0 0 24px 24px;
        }

        /* ===== FOOTER ===== */
        .footer-logo {
            height: 48px;
            width: auto;
            object-fit: contain;
            filter: brightness(0) invert(1);
            opacity: 0.9;
            transition: all 0.3s ease;
        }

        .footer-logo:hover {
            opacity: 1;
            transform: scale(1.05);
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
            .hero-bg {
                background-attachment: scroll;
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
                height: 50px;
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
    <nav class="navbar-glass navbar-container fixed w-full z-50 top-0 transition-all duration-300" id="navbar">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/logopermata.png') }}" alt="Logo Permata" class="navbar-logo">
                    <div class="hidden sm:block">
                        <h1 class="text-lg font-bold text-gray-800 leading-tight group-hover:text-teal-600 transition">
                            Inventory System
                        </h1>
                        <p class="text-xs text-gray-500 -mt-0.5">Yayasan Permata</p>
                    </div>
                </a>
                
                <div class="hidden md:flex items-center gap-2">
                    <a href="#features" class="nav-link text-gray-600 hover:text-teal-600 transition text-sm px-4 py-2 rounded-xl hover:bg-teal-50">
                        <i class="fas fa-th-large mr-2 text-xs"></i>Fitur
                    </a>
                    <a href="#about" class="nav-link text-gray-600 hover:text-teal-600 transition text-sm px-4 py-2 rounded-xl hover:bg-teal-50">
                        <i class="fas fa-info-circle mr-2 text-xs"></i>Tentang
                    </a>
                    <a href="{{ route('login') }}" class="btn-gradient text-white px-6 py-2.5 rounded-xl hover:shadow-lg transition font-medium text-sm flex items-center gap-2 ml-2">
                        <i class="fas fa-sign-in-alt"></i>Login
                    </a>
                </div>

                <button id="mobileMenuBtn" class="md:hidden text-gray-700 text-2xl focus:outline-none hover:text-teal-600 transition w-10 h-10 flex items-center justify-center rounded-xl hover:bg-teal-50">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <div id="mobileMenu" class="hidden md:hidden pb-4 border-t border-gray-100 pt-4">
                <a href="#features" class="flex items-center gap-3 py-3 px-4 text-gray-600 hover:text-teal-600 hover:bg-teal-50 transition font-medium text-sm rounded-xl">
                    <i class="fas fa-th-large text-xs"></i>Fitur
                </a>
                <a href="#about" class="flex items-center gap-3 py-3 px-4 text-gray-600 hover:text-teal-600 hover:bg-teal-50 transition font-medium text-sm rounded-xl">
                    <i class="fas fa-info-circle text-xs"></i>Tentang
                </a>
                <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 btn-gradient text-white px-6 py-3 rounded-xl text-center mt-2 text-sm font-medium">
                    <i class="fas fa-sign-in-alt"></i>Login
                </a>
            </div>
        </div>
    </nav>

    <!-- ===== HERO SECTION ===== -->
    <section class="hero-bg hero-gradient-fallback flex items-center pt-20 overflow-hidden relative">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        
        <div class="container mx-auto px-4 sm:px-6 py-16 sm:py-20 hero-content">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                <div class="text-center lg:text-left">
                    <div class="inline-block bg-white/20 backdrop-blur-md px-5 py-2 rounded-full mb-5 fade-in border border-white/30 shadow-lg">
                        <span class="text-white text-xs sm:text-sm font-medium">
                            <i class="fas fa-check-circle mr-2"></i>Sistem Manajemen Inventaris
                        </span>
                    </div>
                    
                    <h1 class="hero-title text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-extrabold text-white leading-tight mb-5 fade-in fade-in-delay-1 drop-shadow-lg">
                        Kelola Inventaris
                        <span class="text-yellow-300 relative">
                            Yayasan
                            <svg class="absolute -bottom-2 left-0 w-full h-3 text-yellow-300/30" viewBox="0 0 100 10">
                                <path d="M0,5 Q25,0 50,5 Q75,10 100,5" stroke="currentColor" fill="none" stroke-width="2"/>
                            </svg>
                        </span>
                        <br>Dengan Mudah
                    </h1>
                    
                    <p class="hero-sub text-base sm:text-lg lg:text-xl text-white/95 mb-7 max-w-lg mx-auto lg:mx-0 fade-in fade-in-delay-2 leading-relaxed drop-shadow">
                        Sistem manajemen inventaris berbasis multi-unit untuk yayasan. 
                        Kelola barang, peminjaman, dan laporan dengan efisien.
                    </p>
                    
                    <div class="flex flex-wrap gap-4 justify-center lg:justify-start fade-in fade-in-delay-3">
                        <a href="{{ route('login') }}" class="bg-white text-teal-600 px-8 sm:px-10 py-3.5 rounded-2xl font-semibold hover:shadow-2xl transition flex items-center gap-2 text-sm hover:scale-105 shadow-xl">
                            <i class="fas fa-sign-in-alt"></i>Login Sekarang
                        </a>
                        <a href="#features" class="btn-outline text-white px-8 sm:px-10 py-3.5 rounded-2xl font-semibold transition flex items-center gap-2 text-sm">
                            <i class="fas fa-chevron-down"></i>Pelajari
                        </a>
                    </div>
                    
                    <!-- ===== KELEBIHAN WEBSITE (GANTI DARI DATA STATISTIK) ===== -->
                    <div class="grid grid-cols-2 gap-3 sm:gap-4 mt-10 fade-in fade-in-delay-4">
                        <div class="bg-white/15 backdrop-blur-md rounded-2xl p-3 sm:p-4 border border-white/20 hover:bg-white/25 transition-all duration-300 hover:scale-105">
                            <div class="flex items-center gap-2 sm:gap-3">
                                <div class="w-9 h-9 sm:w-10 sm:h-10 bg-yellow-300/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-bolt text-yellow-300 text-sm sm:text-base"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-white font-bold text-xs sm:text-sm">Cepat & Efisien</p>
                                    <p class="text-white/70 text-[10px] sm:text-xs leading-tight">Kelola ribuan data tanpa lemot</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white/15 backdrop-blur-md rounded-2xl p-3 sm:p-4 border border-white/20 hover:bg-white/25 transition-all duration-300 hover:scale-105">
                            <div class="flex items-center gap-2 sm:gap-3">
                                <div class="w-9 h-9 sm:w-10 sm:h-10 bg-green-300/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-shield-alt text-green-300 text-sm sm:text-base"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-white font-bold text-xs sm:text-sm">Aman & Terkontrol</p>
                                    <p class="text-white/70 text-[10px] sm:text-xs leading-tight">Hak akses berlapis per role</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white/15 backdrop-blur-md rounded-2xl p-3 sm:p-4 border border-white/20 hover:bg-white/25 transition-all duration-300 hover:scale-105">
                            <div class="flex items-center gap-2 sm:gap-3">
                                <div class="w-9 h-9 sm:w-10 sm:h-10 bg-blue-300/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-chart-line text-blue-300 text-sm sm:text-base"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-white font-bold text-xs sm:text-sm">Laporan Real-time</p>
                                    <p class="text-white/70 text-[10px] sm:text-xs leading-tight">Pantau data kapan saja</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white/15 backdrop-blur-md rounded-2xl p-3 sm:p-4 border border-white/20 hover:bg-white/25 transition-all duration-300 hover:scale-105">
                            <div class="flex items-center gap-2 sm:gap-3">
                                <div class="w-9 h-9 sm:w-10 sm:h-10 bg-purple-300/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-globe text-purple-300 text-sm sm:text-base"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-white font-bold text-xs sm:text-sm">Akses di Mana Saja</p>
                                    <p class="text-white/70 text-[10px] sm:text-xs leading-tight">Bisa dibuka dari mana pun</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-center lg:justify-end fade-in fade-in-delay-2">
                    <div class="float-slow w-full max-w-md">
                        <div class="bg-white/15 backdrop-blur-xl rounded-3xl p-6 sm:p-8 border border-white/30 shadow-2xl qr-pulse">
                            <div class="text-white text-center">
                                <div class="relative inline-block">
                                    <div class="bg-white/10 rounded-3xl p-4">
                                        <i class="fas fa-qrcode text-7xl sm:text-8xl"></i>
                                    </div>
                                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-20 h-1 bg-yellow-300 rounded-full qr-scanner"></div>
                                </div>
                                <h3 class="text-xl sm:text-2xl font-bold mt-3">QR Code System</h3>
                                <p class="text-white/85 text-sm sm:text-base mt-1">Scan QR Code untuk detail barang</p>
                            </div>
                            <div class="mt-6 grid grid-cols-2 gap-3 sm:gap-4">
                                <div class="qr-box-item bg-white/10 rounded-2xl p-3 sm:p-4 text-center hover:bg-white/20 transition cursor-default">
                                    <i class="fas fa-boxes text-2xl sm:text-3xl text-yellow-300"></i>
                                    <p class="text-xs sm:text-sm text-white/85 mt-1 font-medium">Multi Unit</p>
                                </div>
                                <div class="qr-box-item bg-white/10 rounded-2xl p-3 sm:p-4 text-center hover:bg-white/20 transition cursor-default">
                                    <i class="fas fa-chart-line text-2xl sm:text-3xl text-green-300"></i>
                                    <p class="text-xs sm:text-sm text-white/85 mt-1 font-medium">Real Time</p>
                                </div>
                                <div class="qr-box-item bg-white/10 rounded-2xl p-3 sm:p-4 text-center hover:bg-white/20 transition cursor-default">
                                    <i class="fas fa-shield-alt text-2xl sm:text-3xl text-blue-300"></i>
                                    <p class="text-xs sm:text-sm text-white/85 mt-1 font-medium">Secure</p>
                                </div>
                                <div class="qr-box-item bg-white/10 rounded-2xl p-3 sm:p-4 text-center hover:bg-white/20 transition cursor-default">
                                    <i class="fas fa-users text-2xl sm:text-3xl text-purple-300"></i>
                                    <p class="text-xs sm:text-sm text-white/85 mt-1 font-medium">Multi Role</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== FEATURES SECTION ===== -->
    <section id="features" class="py-20 sm:py-24 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14 sm:mb-20">
                <span class="inline-block bg-teal-100 text-teal-600 px-4 py-1.5 rounded-full text-sm font-medium mb-4">Fitur Unggulan</span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-800 mb-4">
                    Fitur <span class="text-teal-600">Lengkap</span>
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto text-sm sm:text-base leading-relaxed">
                    Sistem inventory management dengan fitur lengkap untuk kebutuhan yayasan Anda
                </p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                <div class="bg-white rounded-3xl shadow-lg p-7 sm:p-9 card-hover border border-gray-100">
                    <div class="feature-icon bg-teal-100 text-teal-600 mb-5">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Manajemen Barang</h3>
                    <p class="text-gray-600 text-sm sm:text-base leading-relaxed">Kelola semua barang dengan kode unik, kategori, lokasi, dan sumber anggaran.</p>
                </div>
                
                <div class="bg-white rounded-3xl shadow-lg p-7 sm:p-9 card-hover border border-gray-100">
                    <div class="feature-icon bg-blue-100 text-blue-600 mb-5">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Sirkulasi Peminjaman</h3>
                    <p class="text-gray-600 text-sm sm:text-base leading-relaxed">Sistem peminjaman dengan approval flow yang terstruktur dan transparan.</p>
                </div>
                
                <div class="bg-white rounded-3xl shadow-lg p-7 sm:p-9 card-hover border border-gray-100">
                    <div class="feature-icon bg-emerald-100 text-emerald-600 mb-5">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">QR Code</h3>
                    <p class="text-gray-600 text-sm sm:text-base leading-relaxed">Setiap barang dilengkapi QR Code untuk akses cepat ke informasi detail.</p>
                </div>
                
                <div class="bg-white rounded-3xl shadow-lg p-7 sm:p-9 card-hover border border-gray-100">
                    <div class="feature-icon bg-amber-100 text-amber-600 mb-5">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Multi Unit System</h3>
                    <p class="text-gray-600 text-sm sm:text-base leading-relaxed">Kelola beberapa unit dalam satu sistem terintegrasi.</p>
                </div>
                
                <div class="bg-white rounded-3xl shadow-lg p-7 sm:p-9 card-hover border border-gray-100">
                    <div class="feature-icon bg-rose-100 text-rose-600 mb-5">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Multi Role Access</h3>
                    <p class="text-gray-600 text-sm sm:text-base leading-relaxed">Hak akses berdasarkan role: Admin, Manager, dan User.</p>
                </div>
                
                <div class="bg-white rounded-3xl shadow-lg p-7 sm:p-9 card-hover border border-gray-100">
                    <div class="feature-icon bg-indigo-100 text-indigo-600 mb-5">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Dashboard & Laporan</h3>
                    <p class="text-gray-600 text-sm sm:text-base leading-relaxed">Dashboard informatif dengan data real-time dan laporan peminjaman.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== ABOUT SECTION ===== -->
    <section id="about" class="py-20 sm:py-24 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <div>
                    <span class="inline-block bg-teal-100 text-teal-600 px-4 py-1.5 rounded-full text-sm font-medium mb-4">Tentang</span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-800 mb-6 leading-tight">
                        Sistem <span class="text-teal-600">Inventaris</span>
                    </h2>
                    <p class="text-gray-600 text-sm sm:text-base mb-7 leading-relaxed">
                        Sistem Inventory Management ini dirancang khusus untuk yayasan dengan banyak unit. 
                        Dengan sistem ini, Anda dapat:
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-start group">
                            <div class="bg-teal-100 rounded-full p-1.5 mt-0.5 mr-3 group-hover:bg-teal-200 transition flex-shrink-0">
                                <i class="fas fa-check text-teal-600 text-xs"></i>
                            </div>
                            <span class="text-gray-600 text-sm sm:text-base">Mengelola barang di setiap unit secara terpisah</span>
                        </li>
                        <li class="flex items-start group">
                            <div class="bg-teal-100 rounded-full p-1.5 mt-0.5 mr-3 group-hover:bg-teal-200 transition flex-shrink-0">
                                <i class="fas fa-check text-teal-600 text-xs"></i>
                            </div>
                            <span class="text-gray-600 text-sm sm:text-base">Melacak peminjaman barang dengan status real-time</span>
                        </li>
                        <li class="flex items-start group">
                            <div class="bg-teal-100 rounded-full p-1.5 mt-0.5 mr-3 group-hover:bg-teal-200 transition flex-shrink-0">
                                <i class="fas fa-check text-teal-600 text-xs"></i>
                            </div>
                            <span class="text-gray-600 text-sm sm:text-base">Memantau ketersediaan barang dengan mudah</span>
                        </li>
                        <li class="flex items-start group">
                            <div class="bg-teal-100 rounded-full p-1.5 mt-0.5 mr-3 group-hover:bg-teal-200 transition flex-shrink-0">
                                <i class="fas fa-check text-teal-600 text-xs"></i>
                            </div>
                            <span class="text-gray-600 text-sm sm:text-base">Mengakses data dari mana saja secara online</span>
                        </li>
                    </ul>
                </div>
                <div class="grid grid-cols-2 gap-5 sm:gap-6">
                    <div class="bg-gradient-to-br from-teal-50 to-white rounded-3xl shadow-xl p-7 text-center card-hover border border-teal-100">
                        <div class="w-16 h-16 bg-teal-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-bolt text-3xl text-teal-600"></i>
                        </div>
                        <p class="font-bold text-lg text-gray-800 mb-1">Cepat</p>
                        <p class="text-sm text-gray-600 font-medium">Proses instan</p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-white rounded-3xl shadow-xl p-7 text-center card-hover border border-blue-100">
                        <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shield-alt text-3xl text-blue-600"></i>
                        </div>
                        <p class="font-bold text-lg text-gray-800 mb-1">Aman</p>
                        <p class="text-sm text-gray-600 font-medium">Data terproteksi</p>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-50 to-white rounded-3xl shadow-xl p-7 text-center card-hover border border-emerald-100">
                        <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-mobile-alt text-3xl text-emerald-600"></i>
                        </div>
                        <p class="font-bold text-lg text-gray-800 mb-1">Responsif</p>
                        <p class="text-sm text-gray-600 font-medium">Semua perangkat</p>
                    </div>
                    <div class="bg-gradient-to-br from-amber-50 to-white rounded-3xl shadow-xl p-7 text-center card-hover border border-amber-100">
                        <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-sync-alt text-3xl text-amber-600"></i>
                        </div>
                        <p class="font-bold text-lg text-gray-800 mb-1">Real-time</p>
                        <p class="text-sm text-gray-600 font-medium">Update otomatis</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CTA SECTION (DIPERPENDEK) ===== -->
    <section class="cta-section py-14 sm:py-16 relative overflow-hidden">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center cta-content">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-3 drop-shadow-lg">
                Siap Mengelola Inventaris?
            </h2>
            <p class="text-white/95 text-sm sm:text-base mb-6 max-w-2xl mx-auto drop-shadow leading-relaxed">
                Login sekarang dan mulai kelola inventaris dengan lebih efisien dan terstruktur.
            </p>
            <a href="{{ route('login') }}" class="bg-white text-teal-600 px-8 sm:px-10 py-3 rounded-2xl font-semibold hover:shadow-2xl transition inline-flex items-center gap-2 hover:scale-105 text-sm shadow-xl">
                <i class="fas fa-sign-in-alt"></i>Login Sekarang
            </a>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="bg-gray-900 text-white py-12 sm:py-14">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logopermata.png') }}" alt="Logo Permata" class="footer-logo">
                    <div>
                        <p class="text-white font-semibold">Inventory System</p>
                        <p class="text-white/50 text-xs">Yayasan Permata</p>
                    </div>
                </div>
                <div class="text-sm text-gray-400 text-center">
                    &copy; {{ date('Y') }} Inventory Management System. All rights reserved.
                </div>
            </div>
            <div class="mt-8 pt-6 border-t border-gray-800 text-center text-xs text-gray-500">
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
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>