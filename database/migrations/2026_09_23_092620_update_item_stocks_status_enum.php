<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE item_stocks MODIFY COLUMN status ENUM('available', 'borrowed', 'disposed', 'maintenance') NOT NULL DEFAULT 'available'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE item_stocks MODIFY COLUMN status ENUM('available', 'disposed') NOT NULL DEFAULT 'available'");
    }
};