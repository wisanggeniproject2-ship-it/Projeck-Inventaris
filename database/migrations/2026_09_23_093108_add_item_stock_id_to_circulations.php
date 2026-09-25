<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('circulations', function (Blueprint $table) {
            // 🔥 Tambah FK ke item_stocks
            $table->foreignId('item_stock_id')
                ->nullable()
                ->after('item_id')
                ->constrained('item_stocks')
                ->onDelete('set null');

            // 🔥 Simpan kode stok (untuk history)
            $table->string('stock_code')->nullable()->after('item_stock_id');
        });
    }

    public function down(): void
    {
        Schema::table('circulations', function (Blueprint $table) {
            $table->dropForeign(['item_stock_id']);
            $table->dropColumn(['item_stock_id', 'stock_code']);
        });
    }
};