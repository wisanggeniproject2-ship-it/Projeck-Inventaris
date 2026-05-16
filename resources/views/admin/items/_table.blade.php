@if(isset($items) && $items->count() > 0)
<table class="min-w-full">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        <tr class="border-t">
            <td class="px-6 py-4">{{ $item->code }}</td>
            <td class="px-6 py-4">{{ $item->name }}</td>
            <td class="px-6 py-4">{{ $item->location }}</td>
            <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs rounded-full 
                    {{ $item->status == 'available' ? 'bg-green-100 text-green-700' : 
                       ($item->status == 'borrowed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                    {{ $item->status == 'available' ? 'Tersedia' : ($item->status == 'borrowed' ? 'Dipinjam' : 'Perbaikan') }}
                </span>
            </td>
            <td class="px-6 py-4">
                <a href="{{ route('admin.items.show', $item) }}" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-eye"></i>
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="text-center py-8 text-gray-500">
    <i class="fas fa-box-open text-4xl mb-2"></i>
    <p>Tidak ada data</p>
</div>
@endif