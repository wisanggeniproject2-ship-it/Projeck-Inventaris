@if(isset($circulations) && $circulations->count() > 0)
<table class="min-w-full">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peminjam</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Pinjam</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($circulations as $circulation)
        <tr class="border-t">
            <td class="px-6 py-4">{{ $circulation->item->name }}</td>
            <td class="px-6 py-4">{{ $circulation->borrower_name }}</td>
            <td class="px-6 py-4">{{ $circulation->borrow_date->format('d/m/Y') }}</td>
            <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs rounded-full
                    {{ $circulation->status == 'approved' ? 'bg-green-100 text-green-700' : 
                       ($circulation->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                       ($circulation->status == 'returned' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                    {{ ucfirst($circulation->status) }}
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="text-center py-8 text-gray-500">
    <i class="fas fa-exchange-alt text-4xl mb-2"></i>
    <p>Tidak ada data peminjaman</p>
</div>
@endif