@extends('layouts.app')

@section('title', 'Dashboard')

{{--
    CATATAN UNTUK CONTROLLER (opsional, dashboard tetap jalan tanpa ini):

    Variabel WAJIB (sudah ada sebelumnya, tidak berubah):
        $stats = [
            'total_items'    => ...,
            'total_borrowed' => ...,
            'total_pending'  => ...,
            'total_units'    => ...,
            'total_users'    => ...,
        ];
        $recentItems         -> koleksi Item (code, name, unit->name, location, status)
        $recentCirculations  -> koleksi Circulation (item->name, borrower_name, borrow_date, status, created_at)

    Variabel OPSIONAL (kalau dikirim, dashboard akan menampilkan data asli, bukan state kosong):
        $stats['total_maintenance']  -> jumlah barang status 'maintenance'
        $chartTrend = [
            'labels' => ['Jan','Feb','Mar','Apr','Mei','Jun'],
            'data'   => [12, 19, 14, 22, 18, 25], // jumlah peminjaman per bulan
        ];
        $unitBreakdown = [
            ['name' => 'SMPIT', 'count' => 120],
            ['name' => 'Daycare', 'count' => 80],
            ...
        ]; // bisa didapat dari: Unit::withCount('items')->get()
--}}

@section('content')
@php
    $totalItems    = $stats['total_items'] ?? 0;
    $totalBorrowed = $stats['total_borrowed'] ?? 0;
    $totalPending  = $stats['total_pending'] ?? 0;
    $totalUnits    = $stats['total_units'] ?? 0;
    $totalUsers    = $stats['total_users'] ?? 0;
    $totalMaintenance = $stats['total_maintenance'] ?? null;

    $totalAvailable = max($totalItems - $totalBorrowed - ($totalMaintenance ?? 0), 0);

    $trendLabels = $chartTrend['labels'] ?? ['Jan','Feb','Mar','Apr','Mei','Jun'];
    $trendData   = $chartTrend['data'] ?? array_fill(0, count($trendLabels), 0);
    $hasTrendData = isset($chartTrend);

    $hasUnitBreakdown = isset($unitBreakdown) && count($unitBreakdown) > 0;
@endphp

<div class="max-w-[1600px] mx-auto">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 animate-fadeInUp">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
                Selamat datang kembali, <span class="text-brand-600">{{ Auth::user()->name }}!</span>
            </h1>
            <p class="text-sm text-gray-500 mt-1">Berikut ringkasan inventaris Yayasan Permata</p>
        </div>
        <div class="flex items-center gap-2 bg-white border border-gray-100 shadow-sm rounded-xl px-4 py-2.5 text-sm text-gray-600 w-fit">
            <i class="fas fa-calendar-days text-brand-500"></i>
            {{ now()->translatedFormat('d F Y') }}
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6 stagger">
        <div class="card-elevated p-5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center mb-3 shadow-brand">
                <i class="fas fa-boxes-stacked text-white"></i>
            </div>
            <p class="text-sm text-gray-500">Total Barang</p>
            <p class="text-2xl font-bold text-gray-800 mt-0.5 tracking-tight">{{ number_format($totalItems) }}</p>
        </div>

        <div class="card-elevated p-5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center mb-3 shadow-[0_10px_30px_-10px_rgba(217,119,6,0.35)]">
                <i class="fas fa-hand-holding text-white"></i>
            </div>
            <p class="text-sm text-gray-500">Dipinjam</p>
            <p class="text-2xl font-bold text-gray-800 mt-0.5 tracking-tight">{{ number_format($totalBorrowed) }}</p>
        </div>

        <div class="card-elevated p-5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center mb-3 shadow-[0_10px_30px_-10px_rgba(220,38,38,0.35)]">
                <i class="fas fa-clock text-white"></i>
            </div>
            <p class="text-sm text-gray-500">Pending</p>
            <p class="text-2xl font-bold text-gray-800 mt-0.5 tracking-tight">{{ number_format($totalPending) }}</p>
        </div>

        <div class="card-elevated p-5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center mb-3 shadow-[0_10px_30px_-10px_rgba(5,150,105,0.35)]">
                <i class="fas fa-building text-white"></i>
            </div>
            <p class="text-sm text-gray-500">Total Unit</p>
            <p class="text-2xl font-bold text-gray-800 mt-0.5 tracking-tight">{{ number_format($totalUnits) }}</p>
        </div>

        <div class="card-elevated p-5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center mb-3 shadow-[0_10px_30px_-10px_rgba(147,51,234,0.35)]">
                <i class="fas fa-users text-white"></i>
            </div>
            <p class="text-sm text-gray-500">Total User</p>
            <p class="text-2xl font-bold text-gray-800 mt-0.5 tracking-tight">{{ number_format($totalUsers) }}</p>
        </div>
    </div>

    {{-- CHARTS + ACTIVITY --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">

        {{-- TREN PEMINJAMAN --}}
        <div class="lg:col-span-1 card-elevated p-5 animate-fadeInUp" style="animation-delay:.1s">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-brand-50 flex items-center justify-center">
                        <i class="fas fa-chart-line text-brand-600 text-[11px]"></i>
                    </span>
                    Tren Peminjaman
                </h3>
                <span class="text-xs text-gray-400">6 Bulan Terakhir</span>
            </div>
            @if($hasTrendData)
                <div class="chart-fallback-slot" style="height:220px">
                    <canvas id="trendChart" width="600" height="220"></canvas>
                </div>
            @else
                <div class="h-[200px] flex flex-col items-center justify-center text-center rounded-xl bg-gradient-to-b from-brand-50/60 to-transparent">
                    <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center mb-3">
                        <i class="fas fa-chart-line text-brand-400"></i>
                    </div>
                    <p class="text-xs text-gray-400 max-w-[190px]">Grafik tren akan tampil otomatis setelah data peminjaman tersambung.</p>
                </div>
            @endif
        </div>

        {{-- STATUS BARANG --}}
        @php
            $statusParts = [
                ['label' => 'Tersedia',  'value' => $totalAvailable, 'color' => '#10b981', 'bar' => 'bg-emerald-500'],
                ['label' => 'Dipinjam',  'value' => $totalBorrowed,  'color' => '#149c8c', 'bar' => 'bg-brand-500'],
            ];
            if (!is_null($totalMaintenance)) {
                $statusParts[] = ['label' => 'Perbaikan', 'value' => $totalMaintenance, 'color' => '#f59e0b', 'bar' => 'bg-amber-500'];
            }
        @endphp
        <div class="lg:col-span-1 card-elevated p-5 animate-fadeInUp" style="animation-delay:.18s">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-semibold text-gray-800">Status Barang</h3>
                <span class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center">
                    <i class="fas fa-chart-pie text-brand-600 text-xs"></i>
                </span>
            </div>

            <div class="flex items-center justify-center mb-5 chart-fallback-slot">
                <div class="relative shrink-0" style="width:160px;height:160px">
                    <canvas id="statusChart" width="160" height="160"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-2xl font-extrabold text-gray-800 tracking-tight">{{ number_format($totalItems) }}</span>
                        <span class="text-[11px] text-gray-400 tracking-wide">Total Barang</span>
                    </div>
                </div>
            </div>

            <ul class="space-y-3">
                @foreach($statusParts as $part)
                    @php $pct = $totalItems > 0 ? round(($part['value'] / $totalItems) * 100) : 0; @endphp
                    <li>
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="flex items-center gap-2 text-gray-600">
                                <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $part['color'] }}"></span>
                                {{ $part['label'] }}
                            </span>
                            <span class="font-semibold text-gray-800">{{ number_format($part['value']) }} <span class="text-gray-400 font-normal">({{ $pct }}%)</span></span>
                        </div>
                        <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full rounded-full {{ $part['bar'] }} transition-all duration-700 ease-out" style="width: 0%" data-target-width="{{ $pct }}%"></div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- AKTIVITAS TERBARU --}}
        <div class="md:col-span-2 lg:col-span-1 card-elevated p-5 animate-fadeInUp" style="animation-delay:.26s">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-brand-50 flex items-center justify-center">
                        <i class="fas fa-bolt text-brand-600 text-[11px]"></i>
                    </span>
                    Aktivitas Terbaru
                </h3>
                <a href="{{ route('super_admin.circulations.index') }}" class="text-xs text-brand-600 hover:text-brand-800 font-medium">Lihat semua</a>
            </div>
            <ul class="divide-y divide-gray-50 max-h-[260px] overflow-y-auto thin-scroll">
                @forelse($recentCirculations as $circulation)
                    @php
                        $statusMap = [
                            'approved' => ['icon' => 'fa-check', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'label' => 'dipinjam'],
                            'pending'  => ['icon' => 'fa-clock', 'bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'label' => 'menunggu persetujuan'],
                            'returned' => ['icon' => 'fa-rotate-left', 'bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'label' => 'dikembalikan'],
                        ];
                        $meta = $statusMap[$circulation->status] ?? ['icon' => 'fa-xmark', 'bg' => 'bg-red-50', 'text' => 'text-red-600', 'label' => $circulation->status];
                    @endphp
                    <li class="flex items-start gap-3 py-3">
                        <div class="w-9 h-9 rounded-lg {{ $meta['bg'] }} flex items-center justify-center shrink-0">
                            <i class="fas {{ $meta['icon'] }} {{ $meta['text'] }} text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm text-gray-700 truncate">
                                <span class="font-medium">{{ $circulation->item->name }}</span>
                                {{ $meta['label'] }} oleh
                                <span class="font-medium">{{ $circulation->borrower_name }}</span>
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ ($circulation->created_at ?? $circulation->borrow_date)->diffForHumans() }}
                            </p>
                        </div>
                    </li>
                @empty
                    <li class="py-8 text-center text-gray-400 text-sm">
                        <i class="fas fa-inbox text-2xl block mb-2"></i>
                        Belum ada aktivitas.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- TABLE + QUICK ACTIONS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- BARANG TERBARU --}}
        <div class="lg:col-span-2 card-elevated animate-fadeInUp" style="animation-delay:.1s">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-brand-50 flex items-center justify-center">
                        <i class="fas fa-boxes-stacked text-brand-600 text-[11px]"></i>
                    </span>
                    Barang Terbaru
                </h3>
                <a href="{{ route('super_admin.items.index') }}"
                   class="text-xs font-medium bg-brand-50 text-brand-700 hover:bg-brand-100 px-3 py-1.5 rounded-lg transition">
                    Lihat Semua
                </a>
            </div>
            <div class="overflow-x-auto">
                @if($recentItems->count() > 0)
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Kode</th>
                            <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Nama Barang</th>
                            <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Unit</th>
                            <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Lokasi</th>
                            <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                            <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($recentItems as $item)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="px-5 py-3.5 font-medium text-gray-700">{{ $item->code }}</td>
                            <td class="px-5 py-3.5 text-gray-700">{{ $item->name }}</td>
                            <td class="px-5 py-3.5 text-gray-500">{{ $item->unit->name ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-gray-500">{{ $item->location }}</td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full
                                    {{ $item->status == 'available' ? 'bg-emerald-50 text-emerald-700' :
                                       ($item->status == 'borrowed' ? 'bg-brand-50 text-brand-700' : 'bg-amber-50 text-amber-700') }}">
                                    {{ $item->status == 'available' ? 'Tersedia' : ($item->status == 'borrowed' ? 'Dipinjam' : 'Perbaikan') }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3 text-gray-400">
                                    <a href="{{ route('super_admin.items.show', $item) }}" class="hover:text-brand-600" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('super_admin.items.edit', $item) }}" class="hover:text-amber-600" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('super_admin.items.destroy', $item) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="hover:text-red-600" title="Hapus" onclick="return confirm('Yakin ingin menghapus barang ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center py-12 text-gray-400">
                    <i class="fas fa-box-open text-4xl mb-2 block"></i>
                    <p class="text-sm">Belum ada data barang</p>
                </div>
                @endif
            </div>
        </div>

        {{-- QUICK ACTION + RINGKASAN UNIT --}}
        <div class="lg:col-span-1 space-y-4">

            <div class="card-elevated p-5 animate-fadeInUp" style="animation-delay:.18s">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-brand-50 flex items-center justify-center">
                        <i class="fas fa-bolt-lightning text-brand-600 text-[11px]"></i>
                    </span>
                    Quick Action
                </h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('super_admin.items.create') }}"
                       class="flex flex-col items-center justify-center gap-2 rounded-xl bg-brand-50 hover:bg-brand-100 text-brand-700 py-4 transition">
                        <i class="fas fa-plus text-lg"></i>
                        <span class="text-xs font-medium text-center">Tambah Barang</span>
                    </a>
                    <a href="{{ route('super_admin.circulations.index') }}"
                       class="flex flex-col items-center justify-center gap-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 py-4 transition">
                        <i class="fas fa-right-left text-lg"></i>
                        <span class="text-xs font-medium text-center">Kelola Sirkulasi</span>
                    </a>
                    <a href="{{ route('super_admin.units.create') }}"
                       class="flex flex-col items-center justify-center gap-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 py-4 transition">
                        <i class="fas fa-building text-lg"></i>
                        <span class="text-xs font-medium text-center">Tambah Unit</span>
                    </a>
                    <a href="{{ route('super_admin.users.index') }}"
                       class="flex flex-col items-center justify-center gap-2 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 py-4 transition">
                        <i class="fas fa-user-gear text-lg"></i>
                        <span class="text-xs font-medium text-center">Manajemen Akun</span>
                    </a>
                </div>
            </div>

            <div class="card-elevated p-5 animate-fadeInUp" style="animation-delay:.26s">
                <h3 class="font-semibold text-gray-800 mb-4">Ringkasan per Unit</h3>
                @if($hasUnitBreakdown)
                    <div class="flex items-center gap-5">
                        <div class="relative shrink-0 chart-fallback-slot" style="width:128px;height:128px">
                            <canvas id="unitChart" width="128" height="128"></canvas>
                        </div>
                        <ul class="space-y-2 text-sm flex-1 min-w-0">
                            @foreach($unitBreakdown as $i => $u)
                                <li class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ ['#22988e','#3fb3a9','#a6e6df','#134e4a','#71d1c8'][$i % 5] }}"></span>
                                    <span class="text-gray-600 truncate">{{ $u['name'] }}</span>
                                    <span class="ml-auto font-semibold text-gray-800">{{ $u['count'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="text-center py-5 rounded-xl bg-gradient-to-b from-brand-50/60 to-transparent">
                        <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-building text-brand-400"></i>
                        </div>
                        <p class="text-xs text-gray-400 max-w-[190px] mx-auto">Ringkasan per unit akan tampil otomatis setelah data unit tersambung.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1) Animasi progress bar status barang - TIDAK bergantung pada Chart.js sama sekali
    document.querySelectorAll('[data-target-width]').forEach((bar, i) => {
        setTimeout(() => { bar.style.width = bar.dataset.targetWidth; }, 300 + (i * 150));
    });

    // 2) Kalau Chart.js gagal dimuat (mis. koneksi lambat), jangan bikin seluruh script mati
    if (typeof Chart === 'undefined') {
        console.error('Chart.js tidak berhasil dimuat dari CDN.');
        document.querySelectorAll('.chart-fallback-slot').forEach(function (slot) {
            slot.innerHTML = '<div class="text-center text-red-500 text-xs py-6">' +
                '<i class="fas fa-triangle-exclamation text-lg mb-1 block"></i>' +
                'Chart.js gagal dimuat.<br>Cek koneksi internet, lalu refresh halaman.</div>';
        });
        return;
    }

    // Sedikit jeda supaya ukuran elemen (dari Tailwind CDN) sudah final sebelum chart digambar
    setTimeout(initCharts, 60);

    function initCharts() {
    const brand = { solid: '#149c8c', dark: '#0d6459', light: '#9deadb', amber: '#f59e0b', emerald: '#10b981' };

    @if($hasTrendData)
    try {
        const trendCtx = document.getElementById('trendChart');
        if (trendCtx) {
            const gradientFill = trendCtx.getContext('2d').createLinearGradient(0, 0, 0, 220);
            gradientFill.addColorStop(0, 'rgba(20,156,140,0.35)');
            gradientFill.addColorStop(1, 'rgba(20,156,140,0.02)');

            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: @json($trendLabels),
                    datasets: [{
                        label: 'Peminjaman',
                        data: @json($trendData),
                        borderColor: brand.solid,
                        backgroundColor: gradientFill,
                        fill: true,
                        tension: 0.45,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: brand.solid,
                        pointBorderWidth: 2,
                        pointHoverBackgroundColor: brand.solid,
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 3,
                        borderWidth: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 1300, easing: 'easeOutQuart' },
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: brand.dark,
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            padding: 10,
                            cornerRadius: 10,
                            displayColors: false,
                            titleFont: { weight: '600' },
                            callbacks: {
                                label: (ctx) => ' ' + ctx.parsed.y + ' peminjaman'
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0 } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    } catch (err) {
        console.error('Gagal membuat trendChart:', err);
    }
    @endif

    @php
        $statusLabels = !is_null($totalMaintenance) ? ['Tersedia', 'Dipinjam', 'Perbaikan'] : ['Tersedia', 'Dipinjam'];
        $statusValues = !is_null($totalMaintenance) ? [$totalAvailable, $totalBorrowed, $totalMaintenance] : [$totalAvailable, $totalBorrowed];
    @endphp
    try {
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($statusLabels),
                    datasets: [{
                        data: @json($statusValues),
                        backgroundColor: [brand.emerald, brand.solid, brand.amber],
                        borderWidth: 3,
                        borderColor: '#ffffff',
                        borderRadius: 6,
                        hoverOffset: 6,
                        spacing: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '74%',
                    radius: '92%',
                    animation: { duration: 1200, easing: 'easeOutQuart' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: brand.dark,
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            padding: 10,
                            cornerRadius: 10,
                            displayColors: true,
                            boxPadding: 4,
                        }
                    }
                }
            });
        }
    } catch (err) {
        console.error('Gagal membuat statusChart:', err);
    }

    @if($hasUnitBreakdown)
    try {
        const unitCtx = document.getElementById('unitChart');
        if (unitCtx) {
            new Chart(unitCtx, {
                type: 'doughnut',
                data: {
                    labels: @json(collect($unitBreakdown)->pluck('name')),
                    datasets: [{
                        data: @json(collect($unitBreakdown)->pluck('count')),
                        backgroundColor: ['#149c8c','#39c1ad','#9deadb','#0d6459','#68d9c7'],
                        borderWidth: 3,
                        borderColor: '#ffffff',
                        borderRadius: 6,
                        hoverOffset: 6,
                        spacing: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    radius: '92%',
                    animation: { duration: 1200, easing: 'easeOutQuart' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: brand.dark,
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            padding: 10,
                            cornerRadius: 10,
                            displayColors: true,
                            boxPadding: 4,
                        }
                    }
                }
            });
        }
    } catch (err) {
        console.error('Gagal membuat unitChart:', err);
    }
    @endif
    } // end initCharts
});
</script>
@endpush
@endsection