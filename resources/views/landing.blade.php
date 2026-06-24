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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-gradient:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        .fade-in {
            animation: fadeIn 1s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="bg-white shadow-md fixed w-full z-50 top-0">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-boxes text-2xl text-purple-600"></i>
                    <span class="text-xl font-bold text-gray-800">Inventory<span class="text-purple-600">System</span></span>
                </div>
                
                <div class="flex items-center space-x-6">
                    <a href="#features" class="text-gray-600 hover:text-purple-600 transition">Fitur</a>
                    <a href="#about" class="text-gray-600 hover:text-purple-600 transition">Tentang</a>
                    <a href="{{ route('login') }}" class="btn-gradient text-white px-6 py-2 rounded-lg hover:shadow-lg transition">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero-gradient min-h-screen flex items-center pt-16">
        <div class="container mx-auto px-6 py-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="fade-in">
                    <div class="inline-block bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full mb-6">
                        <span class="text-white text-sm font-medium">
                            <i class="fas fa-check-circle mr-2"></i>Sistem Manajemen Inventaris
                        </span>
                    </div>
                    
                    <h1 class="text-4xl lg:text-6xl font-extrabold text-white leading-tight mb-6">
                        Kelola Inventaris Yayasan
                        <span class="text-yellow-300">Dengan Mudah</span>
                    </h1>
                    
                    <p class="text-white/90 text-lg mb-8 max-w-lg">
                        Sistem manajemen inventaris berbasis multi-unit untuk yayasan. 
                        Kelola barang, peminjaman, dan laporan dengan efisien.
                    </p>
                    
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('login') }}" class="bg-white text-purple-600 px-8 py-3 rounded-lg font-semibold hover:shadow-lg transition flex items-center">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login Sekarang
                        </a>
                        <a href="#features" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white/10 transition flex items-center">
                            <i class="fas fa-chevron-down mr-2"></i>Pelajari Lebih
                        </a>
                    </div>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-4 mt-12">
                        <div class="stat-card rounded-lg p-4 text-center">
                            <p class="text-3xl font-bold text-purple-600">{{ $totalItems }}</p>
                            <p class="text-sm text-gray-600">Total Barang</p>
                        </div>
                        <div class="stat-card rounded-lg p-4 text-center">
                            <p class="text-3xl font-bold text-green-600">{{ $availableItems }}</p>
                            <p class="text-sm text-gray-600">Tersedia</p>
                        </div>
                        <div class="stat-card rounded-lg p-4 text-center">
                            <p class="text-3xl font-bold text-blue-600">{{ $totalUnits }}</p>
                            <p class="text-sm text-gray-600">Total Unit</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-center lg:justify-end fade-in">
                    <div class="float-animation">
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20">
                            <div class="text-white text-center">
                                <i class="fas fa-qrcode text-8xl mb-4"></i>
                                <h3 class="text-2xl font-bold">QR Code System</h3>
                                <p class="text-white/80 mt-2">Scan QR Code untuk melihat detail barang</p>
                            </div>
                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <div class="bg-white/10 rounded-lg p-4 text-center">
                                    <i class="fas fa-boxes text-3xl text-yellow-300"></i>
                                    <p class="text-sm text-white/80 mt-1">Multi Unit</p>
                                </div>
                                <div class="bg-white/10 rounded-lg p-4 text-center">
                                    <i class="fas fa-chart-line text-3xl text-green-300"></i>
                                    <p class="text-sm text-white/80 mt-1">Real Time</p>
                                </div>
                                <div class="bg-white/10 rounded-lg p-4 text-center">
                                    <i class="fas fa-shield-alt text-3xl text-blue-300"></i>
                                    <p class="text-sm text-white/80 mt-1">Secure</p>
                                </div>
                                <div class="bg-white/10 rounded-lg p-4 text-center">
                                    <i class="fas fa-users text-3xl text-purple-300"></i>
                                    <p class="text-sm text-white/80 mt-1">Multi Role</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-4">
                    Fitur <span class="text-purple-600">Unggulan</span>
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Sistem inventory management dengan fitur lengkap untuk kebutuhan yayasan Anda
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                    <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-boxes text-2xl text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Manajemen Barang</h3>
                    <p class="text-gray-600">Kelola semua barang dengan kode unik, kategori, lokasi, dan sumber anggaran.</p>
                </div>
                
                <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                    <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-exchange-alt text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Sirkulasi Peminjaman</h3>
                    <p class="text-gray-600">Sistem peminjaman dengan approval flow yang terstruktur dan transparan.</p>
                </div>
                
                <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                    <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-qrcode text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">QR Code</h3>
                    <p class="text-gray-600">Setiap barang dilengkapi QR Code untuk akses cepat ke informasi detail.</p>
                </div>
                
                <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                    <div class="w-14 h-14 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-building text-2xl text-yellow-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Multi Unit System</h3>
                    <p class="text-gray-600">Kelola beberapa unit (SMP, SMA, Administrasi) dalam satu sistem terintegrasi.</p>
                </div>
                
                <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                    <div class="w-14 h-14 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-user-shield text-2xl text-red-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Multi Role Access</h3>
                    <p class="text-gray-600">Hak akses berdasarkan role: Admin, Manager, dan User dengan fitur masing-masing.</p>
                </div>
                
                <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                    <div class="w-14 h-14 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-chart-pie text-2xl text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Dashboard & Laporan</h3>
                    <p class="text-gray-600">Dashboard informatif dengan data real-time dan laporan peminjaman.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section id="about" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-6">
                        Tentang <span class="text-purple-600">Sistem</span>
                    </h2>
                    <p class="text-gray-600 mb-4">
                        Sistem Inventory Management ini dirancang khusus untuk yayasan dengan banyak unit. 
                        Dengan sistem ini, Anda dapat:
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span class="text-gray-600">Mengelola barang di setiap unit secara terpisah</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span class="text-gray-600">Melacak peminjaman barang dengan status real-time</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span class="text-gray-600">Memantau ketersediaan barang dengan mudah</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span class="text-gray-600">Mengakses data dari mana saja secara online</span>
                        </li>
                    </ul>
                </div>
                <div class="bg-gray-50 rounded-2xl p-8">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white rounded-lg shadow p-6 text-center">
                            <i class="fas fa-users text-4xl text-purple-600 mb-2"></i>
                            <p class="font-bold text-2xl">{{ $totalItems }}</p>
                            <p class="text-sm text-gray-600">Total Data Barang</p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6 text-center">
                            <i class="fas fa-building text-4xl text-blue-600 mb-2"></i>
                            <p class="font-bold text-2xl">{{ $totalUnits }}</p>
                            <p class="text-sm text-gray-600">Unit Aktif</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA SECTION -->
    <section class="hero-gradient py-20">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl lg:text-4xl font-bold text-white mb-6">
                Siap Mengelola Inventaris Yayasan Anda?
            </h2>
            <p class="text-white/90 text-lg mb-8 max-w-2xl mx-auto">
                Login sekarang dan mulai kelola inventaris dengan lebih efisien dan terstruktur.
            </p>
            <a href="{{ route('login') }}" class="bg-white text-purple-600 px-10 py-4 rounded-lg font-semibold hover:shadow-2xl transition inline-flex items-center">
                <i class="fas fa-sign-in-alt mr-2"></i>Login Sekarang
            </a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-gray-900 text-white py-8">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-boxes text-2xl text-purple-400"></i>
                    <span class="text-xl font-bold">Inventory<span class="text-purple-400">System</span></span>
                </div>
                <div class="text-sm text-gray-400 mt-4 md:mt-0">
                    &copy; 2025 Inventory Management System. All rights reserved.
                </div>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <a href="#" class="text-gray-400 hover:text-white transition">
                        <i class="fab fa-github"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Smooth Scroll -->
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>