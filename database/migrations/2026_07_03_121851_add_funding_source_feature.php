<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // HANYA TAMBAHKAN KOLOM KE TABEL items
        Schema::table('items', function (Blueprint $table) {
            // Cek apakah kolom price sudah ada
            if (!Schema::hasColumn('items', 'price')) {
                $table->decimal('price', 15, 2)->nullable()->after('condition');
            }
            
            // Cek apakah kolom funding_source_id sudah ada
            if (!Schema::hasColumn('items', 'funding_source_id')) {
                $table->foreignId('funding_source_id')
                      ->nullable()
                      ->constrained('funding_sources')
                      ->nullOnDelete();
            }
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            // Hapus foreign key jika ada
            if (Schema::hasColumn('items', 'funding_source_id')) {
                $table->dropForeign(['funding_source_id']);
                $table->dropColumn('funding_source_id');
            }
            
            // Hapus kolom price jika ada
            if (Schema::hasColumn('items', 'price')) {
                $table->dropColumn('price');
            }
        });
    }
};