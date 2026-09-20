@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-2xl space-y-6">
    <h1 class="text-2xl font-bold mb-2">Profil Saya</h1>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Update Info Profile -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold text-lg mb-4">Informasi Akun</h3>
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Nama *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">No. HP</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Role</label>
                    <input type="text" value="{{ ucfirst(str_replace('_', ' ', $user->role)) }}" disabled
                           class="w-full px-3 py-2 border rounded-lg bg-gray-100 text-gray-500">
                    <p class="text-xs text-gray-400 mt-1">Role hanya bisa diubah oleh admin.</p>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Update Password -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold text-lg mb-4">Ubah Password</h3>
        <form method="POST" action="{{ route('profile.password.update') }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Password Saat Ini *</label>
                    <input type="password" name="current_password" required
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Password Baru *</label>
                    <input type="password" name="password" required
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    <p class="text-xs text-gray-400 mt-1">Minimal 8 karakter.</p>
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Konfirmasi Password Baru *</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="px-6 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition">
                    <i class="fas fa-key mr-2"></i>Ubah Password
                </button>
            </div>
        </form>
    </div>

    <!-- Delete Account -->
    <div class="bg-white rounded-lg shadow p-6 border border-red-200">
        <h3 class="font-semibold text-lg mb-2 text-red-600">Hapus Akun</h3>
        <p class="text-sm text-gray-500 mb-4">Setelah akun dihapus, semua data akan hilang permanen. Pastikan kamu yakin sebelum melanjutkan.</p>

        <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Yakin mau hapus akun ini? Tindakan ini tidak bisa dibatalkan.');">
            @csrf
            @method('DELETE')

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Konfirmasi Password *</label>
                <input type="password" name="password" required
                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-red-500">
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">
                <i class="fas fa-trash mr-2"></i>Hapus Akun Saya
            </button>
        </form>
    </div>
</div>
@endsection