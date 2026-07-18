<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Inventaris Sekolah') }}</title>

    <!-- Warna dasar HTML/body diset duluan supaya tidak ada kedipan putih sebelum CSS lain siap -->
    <style>
        html, body { background-color: #0d6459; overflow-x: hidden; }
    </style>

    <!-- Tailwind CSS (Play CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50:  '#eafbf7',
                            100: '#cdf5ec',
                            200: '#9deadb',
                            300: '#68d9c7',
                            400: '#39c1ad',
                            500: '#149c8c',
                            600: '#0c7d70',
                            700: '#0d6459',
                            800: '#0f5049',
                            900: '#0f3f3a',
                            950: '#082523',
                        },
                    },
                    boxShadow: {
                        brand: '0 10px 26px -8px rgba(12,125,112,0.35)',
                        'brand-lg': '0 22px 45px -14px rgba(12,125,112,0.45)',
                    },
                },
            },
        };
    </script>

    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Chart.js (pakai cdnjs, satu domain dengan Font Awesome yang sudah terbukti jalan di project ini) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
            background-color: #f9fafb;
        }

        .sidebar-transition { transition: transform 0.3s ease-in-out; }

        /* Kartu putih standar dipakai di seluruh dashboard - shadow & hover TOSCA, bukan hitam */
        .card-elevated {
            background: #fff;
            border-radius: 1rem;
            border: 1px solid rgba(12, 125, 112, 0.08);
            box-shadow: 0 2px 10px -4px rgba(12, 125, 112, 0.10);
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color .25s ease;
        }
        .card-elevated:hover {
            transform: translateY(-4px);
            border-color: rgba(12, 125, 112, 0.18);
            box-shadow: 0 18px 36px -14px rgba(12, 125, 112, 0.35);
        }

        /* Staggered entrance animation helper */
        .stagger > * {
            opacity: 0;
            animation: fadeInUp 0.5s ease-out forwards;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .stagger > *:nth-child(1) { animation-delay: .03s; }
        .stagger > *:nth-child(2) { animation-delay: .09s; }
        .stagger > *:nth-child(3) { animation-delay: .15s; }
        .stagger > *:nth-child(4) { animation-delay: .21s; }
        .stagger > *:nth-child(5) { animation-delay: .27s; }
        .stagger > *:nth-child(6) { animation-delay: .33s; }

        /* Custom thin scrollbar for sidebar/tables */
        .thin-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
        .thin-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 999px; }
        .thin-scroll::-webkit-scrollbar-track { background: transparent; }

        /* ===== PAGE LOADER (pengganti layar putih saat pindah halaman) ===== */
        #pageLoader {
            position: fixed;
            inset: 0;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 16px;
            background: linear-gradient(160deg, #0d6459 0%, #0c7d70 55%, #149c8c 100%);
            transition: opacity .35s ease, visibility .35s ease;
        }
        #pageLoader.loader-hidden { opacity: 0; visibility: hidden; }
        #pageLoader .loader-badge {
            width: 108px; height: 108px;
            display: flex; align-items: center; justify-content: center;
            animation: loaderPop .5s ease;
        }
        #pageLoader .loader-badge img { width: 100%; height: 100%; object-fit: contain; }
        #pageLoader .loader-badge i { font-size: 46px; color: #ffffff; }
        #pageLoader .loader-spinner {
            width: 30px; height: 30px; border-radius: 50%;
            border: 3px solid rgba(255,255,255,.28);
            border-top-color: #ffffff;
            animation: loaderSpin .8s linear infinite;
        }
        #pageLoader .loader-text {
            color: rgba(255,255,255,.85);
            font-size: 11px; letter-spacing: .12em; text-transform: uppercase;
            font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
        }
        @keyframes loaderSpin { to { transform: rotate(360deg); } }
        @keyframes loaderPop { from { opacity: 0; transform: scale(.85); } to { opacity: 1; transform: scale(1); } }
    </style>

    @stack('styles')
</head>
<body>

    <!-- Loader branded: tampil default saat halaman baru dibuka, disembunyikan setelah siap -->
    <div id="pageLoader">
        <div class="loader-badge">
            <img src="{{ asset('images/logopermata.png') }}" alt="Logo"
                 onerror="this.remove(); this.parentElement.innerHTML = '<i class=\'fas fa-graduation-cap\'></i>';">
        </div>
        <div class="loader-spinner"></div>
        <div class="loader-text">Memuat...</div>
    </div>

    <div class="flex h-screen overflow-hidden">
        @include('layouts.sidebar')

        <div class="flex-1 flex flex-col min-w-0">
            @include('layouts.navbar')

            <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-50">
                @if(session('success'))
                    <div class="animate-fadeInUp bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-4 flex items-center gap-2">
                        <i class="fas fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="animate-fadeInUp bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 flex items-center gap-2">
                        <i class="fas fa-circle-exclamation"></i> {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Transisi masuk & keluar halaman pakai loader bermerek (bukan layar putih polos)
        document.addEventListener('DOMContentLoaded', function () {
            const loader = document.getElementById('pageLoader');

            // Halaman baru selesai dimuat -> sembunyikan loader (jeda singkat biar tidak flicker)
            setTimeout(() => loader.classList.add('loader-hidden'), 120);

            const EXIT_MS = 140;
            let isNavigating = false;
            function showLoaderThenGo(action) {
                if (isNavigating) return;
                isNavigating = true;
                loader.classList.remove('loader-hidden');
                setTimeout(action, EXIT_MS);
            }

            document.addEventListener('click', function (e) {
                const link = e.target.closest('a[href]');
                if (!link) return;
                const href = link.getAttribute('href') || '';
                if (href.startsWith('#') || href.startsWith('javascript:')) return;
                if (link.target === '_blank' || link.hasAttribute('download')) return;
                if (link.dataset.noTransition !== undefined) return;
                if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0) return;
                if (link.origin !== window.location.origin) return;

                e.preventDefault();
                showLoaderThenGo(() => { window.location.href = link.href; });
            });

            document.addEventListener('submit', function (e) {
                const form = e.target;
                if (form.dataset.noTransition !== undefined) return;
                if (form.dataset.transitioning) return;

                e.preventDefault();
                form.dataset.transitioning = '1';
                showLoaderThenGo(() => form.submit());
            });

            // Fix: kalau halaman ditampilkan lagi lewat tombol Back/Forward browser,
            // JS-nya TIDAK dijalankan ulang dari awal (DOMContentLoaded tidak nembak lagi) -
            // browser cuma "membekukan & mengembalikan" state lama apa adanya (bfcache).
            // Akibatnya variabel isNavigating bisa nyangkut true, dan loader bisa nyangkut nyala,
            // jadi semua klik link berikutnya diam saja sampai di-refresh manual.
            // Listener ini menjamin keduanya di-reset setiap halaman ditampilkan lagi.
            window.addEventListener('pageshow', function () {
                isNavigating = false;
                loader.classList.add('loader-hidden');
            });
        });
    </script>

    @stack('scripts')
</body>
</html>