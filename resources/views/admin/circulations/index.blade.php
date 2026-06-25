@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Manajemen Sirkulasi</h1>
    </div>

    <!-- Filter -->
    <div class="mb-6">
        <form method="GET" class="flex gap-2">
            <select name="status" class="px-4 py-2 border rounded-lg">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
            </select>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peminjam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Pinjam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tenggat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($circulations as $circulation)
                <tr>
                    <td class="px-6 py-4">
                        {{ $circulation->item->name }}
                        <br>
                        <small class="text-gray-500">{{ $circulation->item->code }}</small>
                    </td>
                    <td class="px-6 py-4">{{ $circulation->borrower_name }}</td>
                    <td class="px-6 py-4">{{ $circulation->item->unit->name }}</td>
                    <td class="px-6 py-4">{{ $circulation->borrow_date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">{{ $circulation->expected_return_date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $circulation->status == 'approved' ? 'bg-green-100 text-green-700' : 
                               ($circulation->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                               ($circulation->status == 'returned' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                            {{ ucfirst($circulation->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <!-- DETAIL - PAKAI super_admin.circulations.show -->
                            <a href="{{ route('super_admin.circulations.show', $circulation) }}" 
                               class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            @if($circulation->status == 'pending')
                                <form action="{{ route('super_admin.circulations.approve', $circulation) }}" 
                                      method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800"
                                            onclick="return confirm('Setujui peminjaman ini?')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form action="{{ route('super_admin.circulations.reject', $circulation) }}" 
                                      method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-800"
                                            onclick="return confirm('Tolak peminjaman ini?')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            @endif
                            
                            @if($circulation->status == 'approved')
                                <form action="{{ route('super_admin.circulations.return', $circulation) }}" 
                                      method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-blue-600 hover:text-blue-800"
                                            onclick="return confirm('Tandai barang sudah dikembalikan?')">
                                        <i class="fas fa-undo-alt"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $circulations->links() }}
    </div>
</div>
@endsection