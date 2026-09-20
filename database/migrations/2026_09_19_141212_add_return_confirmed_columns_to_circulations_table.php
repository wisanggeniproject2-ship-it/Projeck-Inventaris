<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('circulations', function (Blueprint $table) {
            if (!Schema::hasColumn('circulations', 'return_confirmed_by')) {
                $table->foreignId('return_confirmed_by')->nullable()->after('approved_at')->constrained('users');
            }
            if (!Schema::hasColumn('circulations', 'return_confirmed_at')) {
                $table->timestamp('return_confirmed_at')->nullable()->after('return_confirmed_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('circulations', function (Blueprint $table) {
            $table->dropColumn(['return_confirmed_by', 'return_confirmed_at']);
        });
    }
};