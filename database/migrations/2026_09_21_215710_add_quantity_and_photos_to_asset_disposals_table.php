<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_disposals', function (Blueprint $table) {
            $table->integer('quantity')->default(1)->after('item_id');
            $table->json('photos')->nullable()->after('reason');
        });
    }

    public function down(): void
    {
        Schema::table('asset_disposals', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'photos']);
        });
    }
};