<?php

namespace Database\Seeders;

use App\Models\FundingSource;
use Illuminate\Database\Seeder;

class FundingSourceSeeder extends Seeder
{
    public function run()
    {
        $sources = [
            [
                'name'        => 'BOS',
                'code'        => 'BOS',
                'description' => 'Bantuan Operasional Sekolah',
                'is_active'   => true,
            ],
            [
                'name'        => 'APBY',
                'code'        => 'APBY',
                'description' => 'Anggaran Pendapatan dan Belanja Yayasan',
                'is_active'   => true,
            ],
            [
                'name'        => 'WAKAF',
                'code'        => 'WAKAF',
                'description' => 'Dana Wakaf',
                'is_active'   => true,
            ],
            [
                'name'        => 'SPP',
                'code'        => 'SPP',
                'description' => 'Sumbangan Pembinaan Pendidikan',
                'is_active'   => true,
            ],
            [
                'name'        => 'Donasi',
                'code'        => 'DNS',
                'description' => 'Donasi dari pihak eksternal',
                'is_active'   => true,
            ],
            [
                'name'        => 'Hibah',
                'code'        => 'HIB',
                'description' => 'Hibah dari pemerintah atau lembaga',
                'is_active'   => true,
            ],
            [
                'name'        => 'Bantuan Pemerintah',
                'code'        => 'BP',
                'description' => 'Bantuan dari pemerintah daerah/pusat',
                'is_active'   => true,
            ],
            [
                'name'        => 'Kas Yayasan',
                'code'        => 'KY',
                'description' => 'Dana dari kas internal yayasan',
                'is_active'   => true,
            ],
            [
                'name'        => 'Komite Sekolah',
                'code'        => 'KOM',
                'description' => 'Dana dari komite sekolah',
                'is_active'   => true,
            ],
            [
                'name'        => 'Sponsor',
                'code'        => 'SPN',
                'description' => 'Dana dari sponsor/kerjasama',
                'is_active'   => true,
            ],
        ];

        foreach ($sources as $source) {
            // 🔥 updateOrCreate: insert kalau belum ada, update kalau sudah ada
            FundingSource::updateOrCreate(
                ['code' => $source['code']],  // cek berdasarkan code
                $source                        // data yang di-insert/update
            );
        }

        $this->command->info('✅ ' . count($sources) . ' sumber dana berhasil di-seed!');
    }
}