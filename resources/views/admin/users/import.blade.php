@extends('layouts.app')

@section('title', 'Import User')

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- HEADER --}}
    <div class="mb-5 sm:mb-6 animate-fadeInUp">
        <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800 flex items-center gap-2">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center shadow-brand shrink-0">
                <i class="fas fa-file-excel text-white text-sm"></i>
            </span>
            Import Data User dari Excel
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">
            Tambahkan banyak akun user sekaligus lewat file Excel/CSV, tidak perlu isi form satu-satu.
        </p>
    </div>

    {{-- ALERT: SUKSES --}}
    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-start gap-2 animate-fadeInUp">
            <i class="fas fa-circle-check mt-0.5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- ALERT: SEBAGIAN GAGAL --}}
    @if(session('warning'))
        <div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-sm animate-fadeInUp">
            <div class="flex items-start gap-2 mb-2">
                <i class="fas fa-triangle-exclamation mt-0.5"></i>
                <span>{{ session('warning') }}</span>
            </div>
            <ul class="list-disc list-inside ml-6 space-y-0.5">
                @foreach(session('import_errors', []) as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ALERT: ERROR VALIDASI FILE --}}
    @error('file')
        <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-start gap-2 animate-fadeInUp">
            <i class="fas fa-circle-exclamation mt-0.5"></i>
            <span>{{ $message }}</span>
        </div>
    @enderror

    {{-- CARD FORM --}}
    <div class="card-elevated p-5 sm:p-6 animate-fadeInUp">
        <form action="{{ route('super_admin.users.import') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">File Excel / CSV</label>

                <label for="fileInput"
                       id="dropArea"
                       class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-gray-200 hover:border-brand-400 rounded-xl py-8 cursor-pointer transition bg-gray-50/50">
                    <i class="fas fa-cloud-arrow-up text-2xl text-brand-500"></i>
                    <span class="text-sm text-gray-600" id="fileLabel">Klik untuk pilih file, atau tarik file ke sini</span>
                    <span class="text-xs text-gray-400">Format: .xlsx, .xls, .csv — maksimal 5 MB</span>
                    <input type="file" name="file" id="fileInput" accept=".xlsx,.xls,.csv" required class="hidden">
                </label>
            </div>

            <div class="bg-brand-50/60 border border-brand-100 rounded-xl p-4 text-xs sm:text-sm text-gray-600">
                <p class="font-semibold text-brand-700 mb-1.5 flex items-center gap-1.5">
                    <i class="fas fa-circle-info"></i> Format kolom Excel yang wajib
                </p>
                <p>
                    Baris pertama (header) harus berisi kolom <strong>name</strong> dan <strong>email</strong>.
                    Setiap baris berikutnya akan otomatis menjadi 1 akun user baru dengan role <strong>user</strong>.
                    Password awal dibuat otomatis oleh sistem, dan user akan diminta mengganti password saat login pertama kali.
                </p>
            </div>

            <div class="flex items-center gap-3 pt-1">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 text-white text-sm font-semibold shadow-brand hover:-translate-y-0.5 transition">
                    <i class="fas fa-upload"></i>
                    Upload &amp; Import
                </button>
                <a href="{{ route('super_admin.users.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var fileInput = document.getElementById('fileInput');
    var fileLabel = document.getElementById('fileLabel');

    if (fileInput && fileLabel) {
        fileInput.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                fileLabel.textContent = this.files[0].name;
            }
        });
    }
});
</script>
@endpush
@endsection