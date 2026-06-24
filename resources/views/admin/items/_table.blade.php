@foreach($items as $item)
<tr>
    <td class="px-6 py-4">{{ $item->code }}</td>
    <td class="px-6 py-4">{{ $item->name }}</td>
    <td class="px-6 py-4">{{ $item->unit->name }}</td>
    <td class="px-6 py-4">{{ $item->location }}</td>
    <td class="px-6 py-4">
        <span class="px-2 py-1 text-xs rounded-full 
            {{ $item->status == 'available' ? 'bg-green-100 text-green-700' : 
               ($item->status == 'borrowed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
            {{ $item->status == 'available' ? 'Tersedia' : ($item->status == 'borrowed' ? 'Dipinjam' : 'Perbaikan') }}
        </span>
    </td>
    <td class="px-6 py-4">
        <a href="{{ route('super_admin.items.show', $item) }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-eye"></i>
        </a>
        <a href="{{ route('super_admin.items.edit', $item) }}" class="text-yellow-600 hover:text-yellow-800">
            <i class="fas fa-edit"></i>
        </a>
        <form action="{{ route('super_admin.items.destroy', $item) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Yakin?')">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </td>
</tr>
@endforeach