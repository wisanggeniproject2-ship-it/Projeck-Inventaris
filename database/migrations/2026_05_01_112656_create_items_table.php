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
            $table->string('location', 200)->nullable();
            $table->enum('status', ['available', 'borrowed', 'maintenance'])->default('available');
            $table->string('qr_code_path')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};