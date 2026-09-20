<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE circulations 
                       SET created_at = DATE_ADD(created_at, INTERVAL 7 HOUR) 
                       WHERE created_at IS NOT NULL");

        DB::statement("UPDATE circulations 
                       SET updated_at = DATE_ADD(updated_at, INTERVAL 7 HOUR) 
                       WHERE updated_at IS NOT NULL");
    }

    public function down(): void
    {
        DB::statement("UPDATE circulations 
                       SET created_at = DATE_SUB(created_at, INTERVAL 7 HOUR) 
                       WHERE created_at IS NOT NULL");

        DB::statement("UPDATE circulations 
                       SET updated_at = DATE_SUB(updated_at, INTERVAL 7 HOUR) 
                       WHERE updated_at IS NOT NULL");
    }
};