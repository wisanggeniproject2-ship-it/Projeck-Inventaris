<?php

namespace Database\Seeders;

use App\Models\FundingSource;
use Illuminate\Database\Seeder;

class FundingSourceSeeder extends Seeder
{
    public function run()
    {
        $sources = [
            ['name' => 'BOS', 'code' => 'BOS', 'description' => 'Bantuan Operasional Sekolah'],
            ['name' => 'APBY', 'code' => 'APBY', 'description' => 'Anggaran Pendapatan dan Belanja Yayasan'],
            ['name' => 'WAKAF', 'code' => 'WAKAF', 'description' => 'Dana Wakaf'],
        ];

        foreach ($sources as $source) {
            FundingSource::create($source);
        }
    }
}