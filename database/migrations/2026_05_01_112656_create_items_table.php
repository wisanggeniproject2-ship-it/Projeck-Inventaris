<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 200);
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('unit_id')->constrained('units');
            $table->date('purchase_date')->nullable();
            $table->enum('condition', ['baik', 'rusak', 'perbaikan'])->default('baik');
            $table->decimal('price', 15, 2)->nullable();
            $table->integer('stock')->default(1);
            $table->string('location', 200)->nullable();
            $table->enum('status', ['available', 'borrowed', 'maintenance'])->default('available');
            $table->string('image')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->text('description')->nullable();
            
            // COMMENT BARIS INI:
            // $table->foreignId('funding_source_id')->nullable()->constrained('funding_sources')->nullOnDelete();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};