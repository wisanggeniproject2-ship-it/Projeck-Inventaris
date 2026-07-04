<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. BUAT TABEL funding_sources
        Schema::create('funding_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique()->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. TAMBAHKAN KOLOM funding_source_id DI items
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('funding_source_id')
                  ->nullable()
                  ->after('price')
                  ->constrained('funding_sources')
                  ->nullOnDelete();
        });
    }

    public function down()
    {
        // 1. HAPUS FOREIGN KEY & KOLOM DI items
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['funding_source_id']);
            $table->dropColumn('funding_source_id');
        });

        // 2. HAPUS TABEL funding_sources
        Schema::dropIfExists('funding_sources');
    }
};