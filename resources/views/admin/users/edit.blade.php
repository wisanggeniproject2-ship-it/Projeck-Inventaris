@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-2xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Edit User</h1>
        <a href="{{ route('super_admin.users.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('super_admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Nama Lengkap *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Password (kosongkan jika tidak diubah)</label>
                    <input type="password" name="password"
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Role *</label>
                    <select name="role" id="role" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                        <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="admin_unit" {{ old('role', $user->role) == 'admin_unit' ? 'selected' : '' }}>Admin Unit</option>
                        <option value="super_admin" {{ old('role', $user->role) == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                </div>

                <div id="unit_field" style="display: {{ old('role', $user->role) != 'super_admin' ? 'block' : 'none' }}">
                    <label class="block text-sm font-medium mb-2">Unit</label>
                    <select name="unit_id" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Pilih Unit</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id', $user->unit_id) == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">No. Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }} class="mr-2">
                        <span class="text-sm font-medium">Aktif</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    <i class="fas fa-save mr-2"></i>Update
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