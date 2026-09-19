<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 🔥 Ubah tipe kolom dari DATE → DATETIME
        // Ini biar bisa simpan tanggal + jam
        DB::statement("ALTER TABLE circulations MODIFY COLUMN borrow_date DATETIME NULL");
        DB::statement("ALTER TABLE circulations MODIFY COLUMN expected_return_date DATETIME NULL");
        DB::statement("ALTER TABLE circulations MODIFY COLUMN return_date DATETIME NULL");
    }

    public function down(): void
    {
        // Balik ke DATE kalau perlu rollback
        DB::statement("ALTER TABLE circulations MODIFY COLUMN borrow_date DATE NULL");
        DB::statement("ALTER TABLE circulations MODIFY COLUMN expected_return_date DATE NULL");
        DB::statement("ALTER TABLE circulations MODIFY COLUMN return_date DATE NULL");
    }
};