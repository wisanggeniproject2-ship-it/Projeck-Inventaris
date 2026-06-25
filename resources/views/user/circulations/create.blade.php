@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-3xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Ajukan Peminjaman</h1>
        <a href="{{ route('user.circulations.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('user.circulations.store') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <!-- Pilih Unit -->
                <div>
                    <label class="block text-sm font-medium mb-2">Pilih Unit *</label>
                    <select id="unit_select" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">-- Semua Unit --</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}" 
                            {{ $selectedItem && $selectedItem->unit_id == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Pilih Barang -->
                <div>
                    <label class="block text-sm font-medium mb-2">Pilih Barang *</label>
                    <select name="item_id" id="item_select" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="">-- Pilih Unit Terlebih Dahulu --</option>
                        @foreach($items as $item)
                        <option value="{{ $item->id }}" 
                                data-unit="{{ $item->unit_id }}"
                                {{ $selectedItem && $selectedItem->id == $item->id ? 'selected' : '' }}
                                style="display: none">
                            {{ $item->name }} ({{ $item->code }}) - {{ $item->unit->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('item_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Nama Peminjam *</label>
                    <input type="text" name="borrower_name" value="{{ old('borrower_name', auth()->user()->name) }}" required
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    @error('borrower_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Tanggal Kembali (Estimasi) *</label>
                    <input type="date" name="expected_return_date" value="{{ old('expected_return_date') }}" required
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    @error('expected_return_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Tujuan Peminjaman *</label>
                    <textarea name="purpose" rows="3" required 
                              class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">{{ old('purpose') }}</textarea>
                    @error('purpose') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        Peminjaman akan diproses oleh admin unit terkait. 
                        Status akan berubah menjadi "approved" setelah disetujui.
                    </p>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    <i class="fas fa-paper-plane mr-2"></i>Ajukan Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const unitSelect = document.getElementById('unit_select');
    const itemSelect = document.getElementById('item_select');
    const allItems = itemSelect.querySelectorAll('option');

    function filterItems() {
        const selectedUnit = unitSelect.value;
        
        allItems.forEach(option => {
            if (option.value === '') return;
            
            const unitId = option.getAttribute('data-unit');
            if (selectedUnit === '' || unitId === selectedUnit) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });
        
        itemSelect.value = '';
    }

    unitSelect.addEventListener('change', filterItems);
    filterItems();
});
</script>
@endsection