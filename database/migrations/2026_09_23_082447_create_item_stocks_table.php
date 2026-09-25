<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->integer('stock_number');          // 1, 2, 3, ...
            $table->string('stock_code')->unique();   // Kode lengkap
            $table->enum('status', [
                'available',   // tersedia
                'borrowed',    // sedang dipinjam
                'disposed',    // sudah dihapus (rusak)
                'maintenance', // perbaikan
            ])->default('available');
            $table->text('notes')->nullable();        // catatan (mis. alasan disposal)
            $table->timestamps();

            // Index untuk performa
            $table->index(['item_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_stocks');
    }
};