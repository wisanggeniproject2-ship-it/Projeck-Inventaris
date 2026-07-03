<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Hapus kolom funding_source (ENUM)
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('funding_source');
        });

        // Tambah funding_source_id (foreign key)
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
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['funding_source_id']);
            $table->dropColumn('funding_source_id');
            $table->enum('funding_source', ['BOS', 'APBY', 'WAKAF'])->nullable()->after('price');
        });
    }
};