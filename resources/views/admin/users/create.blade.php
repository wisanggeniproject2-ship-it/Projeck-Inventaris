@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-2xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Tambah User</h1>
        <a href="{{ route('super_admin.users.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('super_admin.users.store') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Nama Lengkap *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Password *</label>
                    <input type="password" name="password" required
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Role *</label>
                    <select name="role" id="role" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                        <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="admin_unit" {{ old('role') == 'admin_unit' ? 'selected' : '' }}>Admin Unit</option>
                        <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                    @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div id="unit_field" style="display: {{ old('role') != 'super_admin' ? 'block' : 'none' }}">
                    <label class="block text-sm font-medium mb-2">Unit</label>
                    <select name="unit_id" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Pilih Unit</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('unit_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">No. Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="mr-2">
                        <span class="text-sm font-medium">Aktif</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('role').addEventListener('change', function() {
    var unitField = document.getElementById('unit_field');
    if (this.value === 'super_admin') {
        unitField.style.display = 'none';
    } else {
        unitField.style.display = 'block';
    }
});
</script>
@endsection