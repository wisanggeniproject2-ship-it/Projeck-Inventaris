<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inventory Management System - Yayasan</title>
    
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
            background: linear-gradient(135deg, #A78BFA 0%, #8B5CF6 30%, #7C3AED 60%, #6D28D9 100%);
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

        .loader-ring {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.15);
            border-top-color: #FFFFFF;
            animation: spin 0.8s linear infinite;
            margin-bottom: 30px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loading-text {
            color: white;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 2px;
            animation: pulseText 1.5s ease-in-out infinite;
        }

        .loading-text span {
            color: #FCD34D;
        }

        @keyframes pulseText {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.02); }
        }

        .loading-sub {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            margin-top: 8px;
            animation: fadeInUp 1s ease;
        }

        .loading-dots::after {
            content: '';
            animation: dots 1.5s steps(3, end) infinite;
        }

        @keyframes dots {
            0% { content: ''; }
            33% { content: '.'; }
            66% { content: '..'; }
            100% { content: '...'; }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== HERO ===== */
        .hero-gradient {
            background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 40%, #6D28D9 70%, #A78BFA 100%);
            background-size: 400% 400%;
            animation: gradientShift 8s ease-in-out infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* ===== ANIMATIONS ===== */
        .fade-in {
            animation: fadeIn 1s ease forwards;
        }

        .fade-in-delay-1 { animation-delay: 0.2s; opacity: 0; }
        .fade-in-delay-2 { animation-delay: 0.4s; opacity: 0; }
        .fade-in-delay-3 { animation-delay: 0.6s; opacity: 0; }
        .fade-in-delay-4 { animation-delay: 0.8s; opacity: 0; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .float-animation {
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        .float-slow {
            animation: floatSlow 6s ease-in-out infinite;
        }

        @keyframes floatSlow {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(2deg); }
        }

        .card-hover {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 25px 50px rgba(124, 58, 237, 0.2);
        }

        .btn-gradient {
            background: linear-gradient(135deg, #8B5CF6, #6D28D9);
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 10px 40px rgba(124, 58, 237, 0.4);
        }

        .btn-outline {
            border: 2px solid white;
            transition: all 0.3s ease;
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.15);
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        /* ===== QR CODE DEMO ===== */
        .qr-scanner {
            animation: scanLine 2.5s ease-in-out infinite;
        }

        @keyframes scanLine {
            0% { transform: translateY(-30px); opacity: 0; }
            50% { transform: translateY(30px); opacity: 1; }
            100% { transform: translateY(-30px); opacity: 0; }
        }

        .qr-pulse {
            animation: pulseRing 2s ease-in-out infinite;
        }

        @keyframes pulseRing {
            0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4); }
            70% { box-shadow: 0 0 0 20px rgba(255, 255, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
        }

        /* ===== FEATURE ICONS ===== */
        .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            transition: all 0.3s ease;
        }

        .card-hover:hover .feature-icon {
            transform: scale(1.1) rotate(-5deg);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 640px) {
            .loading-text {
                font-size: 22px;
            }
            .hero-title {
                font-size: 32px !important;
            }
            .hero-sub {
                font-size: 16px !important;
            }
            .stat-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 8px;
            }
            .stat-card {
                padding: 12px 8px;
            }
            .stat-card p:first-child {
                font-size: 20px;
            }
            .stat-card p:last-child {
                font-size: 10px;
            }
        }

        @media (min-width: 641px) and (max-width: 1024px) {
            .hero-title {
                font-size: 40px !important;
            }
            .stat-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 16px;
            }
        }

        /* ===== SMOOTH SCROLL ===== */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body>

    <!-- ===== LOADING SCREEN ===== -->
    <div id="loading-screen">
        <div class="loader-ring"></div>
        <div class="loading-text">
            Selamat Datang <span>👋</span>
        </div>
        <div class="loading-sub">
            <span class="loading-dots">Memuat sistem</span>
        </div>
    </div>

    <!-- ===== NAVBAR ===== -->
    <nav class="bg-white/90 backdrop-blur-md shadow-sm fixed w-full z-50 top-0 transition-all duration-300" id="navbar">
        <div class="container mx-auto px-4 sm:px-6 py-3 sm:py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/20">
                        <i class="fas fa-boxes text-white text-xl"></i>
                    </div>
                    <span class="text-lg sm:text-xl font-bold text-gray-800">Inventory<span class="text-purple-600">System</span></span>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-600 hover:text-purple-600 transition font-medium">Fitur</a>
                    <a href="#about" class="text-gray-600 hover:text-purple-600 transition font-medium">Tentang</a>
                    <a href="{{ route('login') }}" class="btn-gradient text-white px-6 py-2.5 rounded-xl hover:shadow-lg transition font-medium">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                </div>

                <!-- Mobile Menu Toggle -->
                <button id="mobileMenuBtn" class="md:hidden text-gray-700 text-2xl focus:outline-none">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobileMenu" class="hidden md:hidden mt-4 pb-2 border-t border-gray-100 pt-4">
                <a href="#features" class="block py-2 text-gray-600 hover:text-purple-600 transition font-medium">Fitur</a>
                <a href="#demo" class="block py-2 text-gray-600 hover:text-purple-600 transition font-medium">Demo</a>
                <a href="#about" class="block py-2 text-gray-600 hover:text-purple-600 transition font-medium">Tentang</a>
                <a href="{{ route('login') }}" class="block btn-gradient text-white px-6 py-2.5 rounded-xl text-center mt-2">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
            </div>
        </div>
    </nav>

    <!-- ===== HERO SECTION ===== -->
    <section class="hero-gradient min-h-screen flex items-center pt-16 overflow-hidden">
        <div class="container mx-auto px-4 sm:px-6 py-12 sm:py-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                <!-- Left Content -->
                <div class="text-center lg:text-left">
                    <div class="inline-block bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full mb-4 sm:mb-6 fade-in">
                        <span class="text-white text-xs sm:text-sm font-medium">
                            <i class="fas fa-check-circle mr-2"></i>Sistem Manajemen Inventaris
                        </span>
                    </div>
                    
                    <h1 class="hero-title text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-extrabold text-white leading-tight mb-4 sm:mb-6 fade-in fade-in-delay-1">
                        Kelola Inventaris
                        <span class="text-yellow-300">Yayasan</span>
                        <br>Dengan Mudah
                    </h1>
                    
                    <p class="hero-sub text-base sm:text-lg lg:text-xl text-white/90 mb-6 sm:mb-8 max-w-lg mx-auto lg:mx-0 fade-in fade-in-delay-2">
                        Sistem manajemen inventaris berbasis multi-unit untuk yayasan. 
                        Kelola barang, peminjaman, dan laporan dengan efisien.
                    </p>
                    
                    <div class="flex flex-wrap gap-3 sm:gap-4 justify-center lg:justify-start fade-in fade-in-delay-3">
                        <a href="{{ route('login') }}" class="bg-white text-purple-600 px-6 sm:px-8 py-3 sm:py-3.5 rounded-xl font-semibold hover:shadow-xl transition flex items-center gap-2">
                            <i class="fas fa-sign-in-alt"></i>Login Sekarang
                        </a>
                        <a href="#features" class="btn-outline text-white px-6 sm:px-8 py-3 sm:py-3.5 rounded-xl font-semibold transition flex items-center gap-2">
                            <i class="fas fa-chevron-down"></i>Pelajari
                        </a>
                    </div>
                    
                    <!-- Stats -->
                    <div class="stat-grid grid grid-cols-3 gap-3 sm:gap-4 mt-8 sm:mt-12 fade-in fade-in-delay-4">
                        <div class="stat-card rounded-xl p-3 sm:p-4 text-center shadow-lg">
                            <p class="text-2xl sm:text-3xl font-bold text-purple-600">{{ $totalItems ?? 0 }}</p>
                            <p class="text-xs sm:text-sm text-gray-600">Total Barang</p>
                        </div>
                        <div class="stat-card rounded-xl p-3 sm:p-4 text-center shadow-lg">
                            <p class="text-2xl sm:text-3xl font-bold text-emerald-600">{{ $availableItems ?? 0 }}</p>
                            <p class="text-xs sm:text-sm text-gray-600">Tersedia</p>
                        </div>
                        <div class="stat-card rounded-xl p-3 sm:p-4 text-center shadow-lg">
                            <p class="text-2xl sm:text-3xl font-bold text-blue-600">{{ $totalUnits ?? 0 }}</p>
                            <p class="text-xs sm:text-sm text-gray-600">Total Unit</p>
                        </div>
                    </div>
                </div>
                
                <!-- Right Content - QR Demo -->
                <div class="flex justify-center lg:justify-end fade-in fade-in-delay-2">
                    <div class="float-slow">
                        <div class="bg-white/15 backdrop-blur-md rounded-2xl p-6 sm:p-8 border border-white/20 shadow-2xl qr-pulse">
                            <div class="text-white text-center">
                                <div class="relative inline-block">
                                    <i class="fas fa-qrcode text-7xl sm:text-8xl mb-4"></i>
                                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-16 h-1 bg-yellow-300 rounded-full qr-scanner"></div>
                                </div>
                                <h3 class="text-xl sm:text-2xl font-bold mt-2">QR Code System</h3>
                                <p class="text-white/80 text-sm sm:text-base mt-1">Scan QR Code untuk detail barang</p>
                            </div>
                            <div class="mt-6 grid grid-cols-2 gap-3 sm:gap-4">
                                <div class="bg-white/10 rounded-xl p-3 sm:p-4 text-center hover:bg-white/20 transition">
                                    <i class="fas fa-boxes text-2xl sm:text-3xl text-yellow-300"></i>
                                    <p class="text-xs sm:text-sm text-white/80 mt-1">Multi Unit</p>
                                </div>
                                <div class="bg-white/10 rounded-xl p-3 sm:p-4 text-center hover:bg-white/20 transition">
                                    <i class="fas fa-chart-line text-2xl sm:text-3xl text-green-300"></i>
                                    <p class="text-xs sm:text-sm text-white/80 mt-1">Real Time</p>
                                </div>
                                <div class="bg-white/10 rounded-xl p-3 sm:p-4 text-center hover:bg-white/20 transition">
                                    <i class="fas fa-shield-alt text-2xl sm:text-3xl text-blue-300"></i>
                                    <p class="text-xs sm:text-sm text-white/80 mt-1">Secure</p>
                                </div>
                                <div class="bg-white/10 rounded-xl p-3 sm:p-4 text-center hover:bg-white/20 transition">
                                    <i class="fas fa-users text-2xl sm:text-3xl text-purple-300"></i>
                                    <p class="text-xs sm:text-sm text-white/80 mt-1">Multi Role</p>
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
                <span class="inline-block bg-purple-100 text-purple-600 px-4 py-1 rounded-full text-sm font-medium mb-3">Fitur Unggulan</span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-800 mb-4">
                    Fitur <span class="text-purple-600">Lengkap</span>
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto text-sm sm:text-base">
                    Sistem inventory management dengan fitur lengkap untuk kebutuhan yayasan Anda
                </p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 card-hover border border-gray-100">
                    <div class="feature-icon bg-purple-100 text-purple-600 mb-4">
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
    <section id="about" class="py-16 sm:py-20 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                <div>
                    <span class="inline-block bg-purple-100 text-purple-600 px-4 py-1 rounded-full text-sm font-medium mb-3">Tentang</span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-800 mb-6">
                        Sistem <span class="text-purple-600">Inventaris</span>
                    </h2>
                    <p class="text-gray-600 text-sm sm:text-base mb-4">
                        Sistem Inventory Management ini dirancang khusus untuk yayasan dengan banyak unit. 
                        Dengan sistem ini, Anda dapat:
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-emerald-500 mt-1 mr-3 text-sm sm:text-base"></i>
                            <span class="text-gray-600 text-sm sm:text-base">Mengelola barang di setiap unit secara terpisah</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-emerald-500 mt-1 mr-3 text-sm sm:text-base"></i>
                            <span class="text-gray-600 text-sm sm:text-base">Melacak peminjaman barang dengan status real-time</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-emerald-500 mt-1 mr-3 text-sm sm:text-base"></i>
                            <span class="text-gray-600 text-sm sm:text-base">Memantau ketersediaan barang dengan mudah</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-emerald-500 mt-1 mr-3 text-sm sm:text-base"></i>
                            <span class="text-gray-600 text-sm sm:text-base">Mengakses data dari mana saja secara online</span>
                        </li>
                    </ul>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white rounded-2xl shadow-lg p-6 text-center card-hover">
                        <i class="fas fa-users text-4xl text-purple-600 mb-2"></i>
                        <p class="font-bold text-3xl text-gray-800">{{ $totalItems ?? 0 }}</p>
                        <p class="text-sm text-gray-600">Total Data Barang</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-lg p-6 text-center card-hover">
                        <i class="fas fa-building text-4xl text-blue-600 mb-2"></i>
                        <p class="font-bold text-3xl text-gray-800">{{ $totalUnits ?? 0 }}</p>
                        <p class="text-sm text-gray-600">Unit Aktif</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-lg p-6 text-center card-hover">
                        <i class="fas fa-qrcode text-4xl text-emerald-600 mb-2"></i>
                        <p class="font-bold text-3xl text-gray-800">{{ $totalItems ?? 0 }}</p>
                        <p class="text-sm text-gray-600">QR Code Aktif</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-lg p-6 text-center card-hover">
                        <i class="fas fa-exchange-alt text-4xl text-amber-600 mb-2"></i>
                        <p class="font-bold text-3xl text-gray-800">{{ $totalCirculations ?? 0 }}</p>
                        <p class="text-sm text-gray-600">Total Sirkulasi</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CTA SECTION ===== -->
    <section class="hero-gradient py-16 sm:py-20">
        <div class="container mx-auto px-4 sm:px-6 text-center">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4 sm:mb-6">
                Siap Mengelola Inventaris?
            </h2>
            <p class="text-white/90 text-base sm:text-lg mb-6 sm:mb-8 max-w-2xl mx-auto">
                Login sekarang dan mulai kelola inventaris dengan lebih efisien dan terstruktur.
            </p>
            <a href="{{ route('login') }}" class="bg-white text-purple-600 px-8 sm:px-10 py-3.5 sm:py-4 rounded-xl font-semibold hover:shadow-2xl transition inline-flex items-center gap-2 transform hover:scale-105">
                <i class="fas fa-sign-in-alt"></i>Login Sekarang
            </a>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="bg-gray-900 text-white py-8 sm:py-10">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-boxes text-2xl text-purple-400"></i>
                    <span class="text-xl font-bold">Inventory<span class="text-purple-400">System</span></span>
                </div>
                <div class="text-sm text-gray-400 text-center">
                    &copy; {{ date('Y') }} Inventory Management System. All rights reserved.
                </div>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white transition text-lg">
                        <i class="fab fa-github"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition text-lg">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition text-lg">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ===== SCRIPTS ===== -->
    <script>
        // ===== LOADING SCREEN =====
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.getElementById('loading-screen').classList.add('hide');
            }, 2000);
        });

        // ===== MOBILE MENU =====
        const mobileBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        mobileBtn.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
            this.querySelector('i').classList.toggle('fa-bars');
            this.querySelector('i').classList.toggle('fa-times');
        });

        // ===== SMOOTH SCROLL =====
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                    // Close mobile menu
                    mobileMenu.classList.add('hidden');
                    const icon = mobileBtn.querySelector('i');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
        });

        // ===== NAVBAR SCROLL EFFECT =====
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('shadow-md');
                navbar.classList.remove('shadow-sm');
            } else {
                navbar.classList.remove('shadow-md');
                navbar.classList.add('shadow-sm');
            }
        });

        // ===== COUNTER ANIMATION =====
        function animateCounters() {
            document.querySelectorAll('.stat-card .font-bold').forEach(el => {
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

        // Run counter on scroll
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