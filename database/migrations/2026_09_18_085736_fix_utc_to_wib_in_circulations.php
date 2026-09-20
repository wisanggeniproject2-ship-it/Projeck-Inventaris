<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 🔥 Fix: Data lama tersimpan pakai UTC (server default),
     *    sekarang timezone app sudah Asia/Jakarta (WIB).
     *    Data lama perlu ditambah 7 jam supaya sinkron.
     */
    public function up(): void
    {
        // 🔥 Tambah 7 jam ke data lama (UTC → WIB)
        DB::statement("UPDATE circulations 
                       SET borrow_date = DATE_ADD(borrow_date, INTERVAL 7 HOUR) 
                       WHERE borrow_date IS NOT NULL");

        DB::statement("UPDATE circulations 
                       SET expected_return_date = DATE_ADD(expected_return_date, INTERVAL 7 HOUR) 
                       WHERE expected_return_date IS NOT NULL");

        DB::statement("UPDATE circulations 
                       SET return_date = DATE_ADD(return_date, INTERVAL 7 HOUR) 
                       WHERE return_date IS NOT NULL");

        DB::statement("UPDATE circulations 
                       SET approved_at = DATE_ADD(approved_at, INTERVAL 7 HOUR) 
                       WHERE approved_at IS NOT NULL");

        DB::statement("UPDATE circulations 
                       SET rejected_at = DATE_ADD(rejected_at, INTERVAL 7 HOUR) 
                       WHERE rejected_at IS NOT NULL");

        // Kolom return_confirmed_at baru ditambahkan pada migration
        // 2026_09_19_141212. Guard supaya tidak error saat migrate:fresh.
        if (Schema::hasColumn('circulations', 'return_confirmed_at')) {
            DB::statement("UPDATE circulations 
                           SET return_confirmed_at = DATE_ADD(return_confirmed_at, INTERVAL 7 HOUR) 
                           WHERE return_confirmed_at IS NOT NULL");
        }

        DB::statement("UPDATE circulations 
                       SET created_at = DATE_ADD(created_at, INTERVAL 7 HOUR) 
                       WHERE created_at IS NOT NULL");

        DB::statement("UPDATE circulations 
                       SET updated_at = DATE_ADD(updated_at, INTERVAL 7 HOUR) 
                       WHERE updated_at IS NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * 🔥 Balik ke UTC (kurangi 7 jam)
     */
    public function down(): void
    {
        DB::statement("UPDATE circulations 
                       SET borrow_date = DATE_SUB(borrow_date, INTERVAL 7 HOUR) 
                       WHERE borrow_date IS NOT NULL");

        DB::statement("UPDATE circulations 
                       SET expected_return_date = DATE_SUB(expected_return_date, INTERVAL 7 HOUR) 
                       WHERE expected_return_date IS NOT NULL");

        DB::statement("UPDATE circulations 
                       SET return_date = DATE_SUB(return_date, INTERVAL 7 HOUR) 
                       WHERE return_date IS NOT NULL");

        DB::statement("UPDATE circulations 
                       SET approved_at = DATE_SUB(approved_at, INTERVAL 7 HOUR) 
                       WHERE approved_at IS NOT NULL");

        DB::statement("UPDATE circulations 
                       SET rejected_at = DATE_SUB(rejected_at, INTERVAL 7 HOUR) 
                       WHERE rejected_at IS NOT NULL");

        // Guard yang sama untuk rollback
        if (Schema::hasColumn('circulations', 'return_confirmed_at')) {
            DB::statement("UPDATE circulations 
                           SET return_confirmed_at = DATE_SUB(return_confirmed_at, INTERVAL 7 HOUR) 
                           WHERE return_confirmed_at IS NOT NULL");
        }

        DB::statement("UPDATE circulations 
                       SET created_at = DATE_SUB(created_at, INTERVAL 7 HOUR) 
                       WHERE created_at IS NOT NULL");

        DB::statement("UPDATE circulations 
                       SET updated_at = DATE_SUB(updated_at, INTERVAL 7 HOUR) 
                       WHERE updated_at IS NOT NULL");
    }
};