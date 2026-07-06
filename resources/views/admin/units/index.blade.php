@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold">Manajemen Unit</h1>
        <a href="{{ route('super_admin.units.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg whitespace-nowrap">
            <i class="fas fa-plus mr-2"></i>Tambah Unit
        </a>
    </div>

    <!-- Search -->
    <div class="mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-2">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Cari nama atau kode unit..." 
                   class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500 w-full sm:w-auto">
            <div class="flex gap-2">
                <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg whitespace-nowrap">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
                @if(request('search'))
                <a href="{{ route('super_admin.units.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 whitespace-nowrap">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 🔥 WRAPPER TABEL DENGAN OVERFLOW AUTO (BISA DIGESER) -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Unit</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($units as $unit)
                    <tr>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">{{ $unit->code }}</td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap font-medium">{{ $unit->name }}</td>
                        <td class="px-4 sm:px-6 py-4 max-w-xs truncate">{{ $unit->description ?: '-' }}</td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $unit->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $unit->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <div class="flex gap-2">
                                <a href="{{ route('super_admin.units.edit', $unit) }}" class="text-yellow-600 hover:text-yellow-800 transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('super_admin.units.destroy', $unit) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition" onclick="return confirm('Yakin ingin menghapus unit ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $units->links() }}
    </div>
</div>
@endsection