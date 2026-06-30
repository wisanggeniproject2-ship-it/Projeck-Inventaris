<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('circulations', function (Blueprint $table) {
            // Ubah enum status tambah 'return_pending'
            $table->enum('status', ['pending', 'approved', 'rejected', 'returned', 'return_pending'])
                  ->default('pending')
                  ->change();
            
            // Tambah field konfirmasi pengembalian
            $table->foreignId('return_confirmed_by')->nullable()->after('approved_at')->constrained('users');
            $table->timestamp('return_confirmed_at')->nullable()->after('return_confirmed_by');
        });
    }

    public function down(): void
    {
        Schema::table('circulations', function (Blueprint $table) {
            $table->dropForeign(['return_confirmed_by']);
            $table->dropColumn(['return_confirmed_by', 'return_confirmed_at']);
        });
    }
};