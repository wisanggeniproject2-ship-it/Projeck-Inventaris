<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // circulation_id harus boleh kosong, karena notifikasi bisa juga soal pengajuan aset
        DB::statement("ALTER TABLE notifications MODIFY COLUMN circulation_id BIGINT UNSIGNED NULL");

        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'disposal_id')) {
                $table->foreignId('disposal_id')->nullable()->after('circulation_id')->constrained('asset_disposals')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['disposal_id']);
            $table->dropColumn('disposal_id');
        });

        DB::statement("ALTER TABLE notifications MODIFY COLUMN circulation_id BIGINT UNSIGNED NOT NULL");
    }
};