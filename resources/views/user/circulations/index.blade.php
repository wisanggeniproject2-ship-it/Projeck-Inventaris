@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
                <i class="fas fa-clock-rotate-left text-brand-500 mr-2"></i>Riwayat Peminjaman Saya
            </h1>
            <p class="text-sm text-gray-500 mt-1">Daftar semua peminjaman yang pernah Anda ajukan</p>
        </div>
        <a href="{{ route('user.items.index') }}" 
           class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-4 py-2.5 rounded-xl whitespace-nowrap shadow-sm hover:shadow-md transition-all text-sm font-medium inline-flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Ajukan Peminjaman
        </a>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2 mb-6">
        <a href="{{ route('user.circulations.index') }}" 
           class="bg-white rounded-xl shadow-sm p-3 text-center hover:shadow-md transition border-2 {{ !request('status') ? 'border-brand-500' : 'border-transparent' }}">
            <p class="text-2xl font-bold text-gray-800">{{ $stats['all'] }}</p>
            <p class="text-xs text-gray-500">Semua</p>
        </a>
        <a href="{{ route('user.circulations.index', ['status' => 'pending']) }}" 
           class="bg-white rounded-xl shadow-sm p-3 text-center hover:shadow-md transition border-2 {{ request('status') == 'pending' ? 'border-yellow-500' : 'border-transparent' }}">
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
            <p class="text-xs text-gray-500">Menunggu</p>
        </a>
        <a href="{{ route('user.circulations.index', ['status' => 'approved']) }}" 
           class="bg-white rounded-xl shadow-sm p-3 text-center hover:shadow-md transition border-2 {{ request('status') == 'approved' ? 'border-green-500' : 'border-transparent' }}">
            <p class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</p>
            <p class="text-xs text-gray-500">Disetujui</p>
        </a>
        <a href="{{ route('user.circulations.index', ['status' => 'returned']) }}" 
           class="bg-white rounded-xl shadow-sm p-3 text-center hover:shadow-md transition border-2 {{ request('status') == 'returned' ? 'border-blue-500' : 'border-transparent' }}">
            <p class="text-2xl font-bold text-blue-600">{{ $stats['returned'] }}</p>
            <p class="text-xs text-gray-500">Dikembalikan</p>
        </a>
        <a href="{{ route('user.circulations.index', ['status' => 'rejected']) }}" 
           class="bg-white rounded-xl shadow-sm p-3 text-center hover:shadow-md transition border-2 {{ request('status') == 'rejected' ? 'border-red-500' : 'border-transparent' }}">
            <p class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
            <p class="text-xs text-gray-500">Ditolak</p>
        </a>
    </div>

    {{-- SEARCH --}}
    <div class="mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-2">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nama barang atau kode..." 
                       class="w-full pl-10 pr-4 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition">
            </div>
            <div class="flex gap-2">
                <button type="submit" 
                        class="bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-white px-6 py-2.5 rounded-xl whitespace-nowrap shadow-sm hover:shadow-md transition-all text-sm font-medium inline-flex items-center gap-2">
                    <i class="fas fa-search"></i>Cari
                </button>
                @if(request('search'))
                <a href="{{ route('user.circulations.index', ['status' => request('status')]) }}" 
                   class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2.5 rounded-xl whitespace-nowrap transition inline-flex items-center justify-center"
                   title="Reset">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- TABEL --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Barang</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-sign-out-alt mr-1 text-gray-400"></i>Jam Pinjam
                        </th>
                        <th class="px-4 sm:px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-hourglass-half mr-1 text-gray-400"></i>Tenggat
                        </th>
                        <th class="px-4 sm:px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-sign-in-alt mr-1 text-gray-400"></i>Jam Kembali
                        </th>
                        <th class="px-4 sm:px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($circulations as $circulation)
                    @php
                        $isOverdue = method_exists($circulation, 'isOverdue')
                            ? ($circulation->isOverdue() ?? false)
                            : false;
                    @endphp
                    <tr class="border-t border-gray-100 
                        {{ $circulation->status == 'pending' ? 'bg-yellow-50/50' : '' }}
                        {{ $circulation->status == 'rejected' ? 'bg-red-50/40' : '' }}
                        {{ $isOverdue ? 'bg-red-50/60' : '' }}">

                        {{-- BARANG --}}
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-800">{{ $circulation->item->name ?? '-' }}</div>

                            {{-- 🔥 KODE LENGKAP --}}
                            <div class="text-[10px] font-mono font-semibold mt-0.5 break-all"
                                 style="color: #0F6B5F;">
                                {{ $circulation->item->full_code ?? $circulation->item->code ?? '-' }}
                            </div>
                        </td>

                        {{-- UNIT --}}
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 text-xs font-medium bg-teal-50 text-teal-700 px-2 py-1 rounded-lg border border-teal-100">
                                <i class="fas fa-building text-[9px]"></i>
                                {{ $circulation->item->unit->name ?? '-' }}
                            </span>
                        </td>

                        {{-- 🔥 JAM PINJAM --}}
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-800">
                                <i class="fas fa-calendar-alt text-gray-400 text-xs mr-1"></i>
                                {{ $circulation->borrow_date ? $circulation->borrow_date->format('d/m/Y') : '-' }}
                            </div>
                            <div class="text-xs text-gray-500 mt-0.5">
                                <i class="fas fa-clock text-gray-400 text-[10px] mr-1"></i>
                                {{ $circulation->borrow_date ? $circulation->borrow_date->format('H:i') : '-' }} WIB
                            </div>
                        </td>

                        {{-- 🔥 TENGGAT --}}
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            @if($circulation->expected_return_date)
                                <div class="text-sm font-medium {{ $isOverdue ? 'text-red-600' : 'text-gray-800' }}">
                                    <i class="fas fa-calendar-alt text-gray-400 text-xs mr-1"></i>
                                    {{ $circulation->expected_return_date->format('d/m/Y') }}
                                </div>
                                <div class="text-xs {{ $isOverdue ? 'text-red-600 font-bold' : 'text-gray-500' }} mt-0.5">
                                    <i class="fas fa-clock text-[10px] mr-1"></i>
                                    {{ $circulation->expected_return_date->format('H:i') }} WIB
                                </div>

                                {{-- Badge terlambat --}}
                                @if($isOverdue)
                                    <span class="inline-flex items-center gap-1 mt-1.5 px-2 py-0.5 text-[10px] font-bold rounded-full bg-red-100 text-red-700 border border-red-200">
                                        <i class="fas fa-exclamation-triangle text-[9px]"></i>
                                        TERLAMBAT
                                    </span>
                                @elseif($circulation->status == 'approved')
                                    @php
                                        $sisaJam = method_exists($circulation, 'timeUntilDue')
                                            ? $circulation->timeUntilDue()
                                            : null;
                                    @endphp
                                    @if($sisaJam)
                                        <span class="inline-flex items-center gap-1 mt-1.5 px-2 py-0.5 text-[10px] font-medium rounded-full bg-green-100 text-green-700 border border-green-200">
                                            <i class="fas fa-hourglass-half text-[9px]"></i>
                                            Sisa {{ $sisaJam }}
                                        </span>
                                    @endif
                                @endif
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>

                        {{-- 🔥 JAM KEMBALI --}}
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            @if($circulation->return_date)
                                <div class="text-sm font-medium text-gray-800">
                                    <i class="fas fa-calendar-check text-green-500 text-xs mr-1"></i>
                                    {{ $circulation->return_date->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500 mt-0.5">
                                    <i class="fas fa-clock text-gray-400 text-[10px] mr-1"></i>
                                    {{ $circulation->return_date->format('H:i') }} WIB
                                </div>
                            @else
                                <span class="text-gray-400 text-sm italic">Belum dikembalikan</span>
                            @endif
                        </td>

                        {{-- STATUS --}}
                        <td class="px-4 sm:px-6 py-4">
                            @php
                                $statusClasses = [
                                    'approved'       => 'bg-green-100 text-green-700',
                                    'pending'        => 'bg-yellow-100 text-yellow-700',
                                    'return_pending' => 'bg-blue-100 text-blue-700',
                                    'returned'       => 'bg-gray-100 text-gray-700',
                                    'rejected'       => 'bg-red-100 text-red-700',
                                ];
                                $statusIcons = [
                                    'pending'        => '⏳',
                                    'approved'       => '✅',
                                    'return_pending' => '🔄',
                                    'returned'       => '📦',
                                    'rejected'       => '❌',
                                ];
                                $cls  = $statusClasses[$circulation->status] ?? 'bg-gray-100 text-gray-700';
                                $icon = $statusIcons[$circulation->status] ?? '';
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $cls }}">
                                {{ $icon }} {{ ucfirst(str_replace('_', ' ', $circulation->status)) }}
                            </span>

                            {{-- Alasan reject --}}
                            @if($circulation->status == 'rejected' && $circulation->rejection_reason)
                                <div class="mt-2 p-2.5 bg-red-50 border-l-4 border-red-400 rounded-r-lg text-xs text-red-700 max-w-xs">
                                    <p class="font-semibold mb-1 flex items-center gap-1">
                                        <i class="fas fa-info-circle"></i>
                                        Alasan Ditolak:
                                    </p>
                                    <p class="break-words leading-snug">{{ $circulation->rejection_reason }}</p>

                                    @if($circulation->rejected_at)
                                        <p class="mt-1.5 text-[10px] text-red-500/80">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $circulation->rejected_at->format('d/m/Y H:i') }}
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </td>

                        {{-- AKSI --}}
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <div class="flex gap-3 items-center">
                                {{-- Detail Barang --}}
                                <a href="{{ route('user.items.show', $circulation->item_id) }}" 
                                   class="text-blue-600 hover:text-blue-800" title="Detail Barang">
                                    <i class="fas fa-eye text-lg"></i>
                                </a>
                                
                                {{-- Ajukan Pengembalian --}}
                                @if($circulation->status == 'approved')
                                    <form action="{{ route('user.circulations.requestReturn', $circulation) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="text-orange-600 hover:text-orange-800" 
                                                title="Ajukan Pengembalian"
                                                onclick="return confirm('Yakin ingin mengajukan pengembalian barang ini?')">
                                            <i class="fas fa-undo-alt text-lg"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                {{-- Menunggu Konfirmasi --}}
                                @if($circulation->status == 'return_pending')
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-medium rounded-full bg-blue-100 text-blue-700 border border-blue-200" title="Menunggu konfirmasi admin">
                                        <i class="fas fa-clock text-[10px]"></i>
                                        Menunggu
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 sm:px-6 py-12 text-center text-gray-500">
                            <div class="w-20 h-20 mx-auto mb-3 rounded-full bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-inbox text-3xl text-gray-300"></i>
                            </div>
                            <p class="text-gray-600 font-medium mb-1">Belum ada riwayat peminjaman</p>
                            <p class="text-gray-400 text-sm mb-4">Silakan ajukan peminjaman barang terlebih dahulu</p>
                            <a href="{{ route('user.items.index') }}" 
                               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-xl text-sm font-medium transition shadow-sm hover:shadow-md">
                                <i class="fas fa-plus"></i>
                                Ajukan Peminjaman
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    @if($circulations->hasPages())
    <div class="mt-6">
        {{ $circulations->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection