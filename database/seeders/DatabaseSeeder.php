<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Unit;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ==================== UNITS ====================
        $units = [
            ['name' => 'Daycare', 'code' => 'DCP'],
            ['name' => 'Preschool', 'code' => 'PRS'],
            ['name' => 'KBIT', 'code' => 'KBT'],
            ['name' => 'TKIT', 'code' => 'TKT'],
            ['name' => 'TKIP', 'code' => 'TKP'],
            ['name' => 'SDIT', 'code' => 'SDT'],
            ['name' => 'MI', 'code' => 'MIN'],
            ['name' => 'SMPIT', 'code' => 'SMT'],
            ['name' => 'MA', 'code' => 'MAA'],
        ];
        
        foreach ($units as $unit) {
            Unit::updateOrCreate(['code' => $unit['code']], [
                'name' => $unit['name'],
                'description' => 'Unit ' . $unit['name'],
                'is_active' => true,
            ]);
        }

        // ==================== CATEGORIES ====================
        $categories = [
            ['name' => 'Elektronik', 'code' => 'ELC'],
            ['name' => 'Furniture', 'code' => 'FRN'],
            ['name' => 'Alat Tulis', 'code' => 'ATK'],
            ['name' => 'Perlengkapan', 'code' => 'PRL'],
            ['name' => 'Kendaraan', 'code' => 'KND'],
            ['name' => 'Bangunan', 'code' => 'BGN'],
        ];
        
        foreach ($categories as $category) {
            Category::updateOrCreate(['code' => $category['code']], [
                'name' => $category['name'],
            ]);
        }

        // ==================== GET UNITS ====================
        $units = Unit::all()->keyBy('code');

        // ==================== GET CATEGORIES ====================
        $categories = Category::all()->keyBy('code');

        // ==================== SUPER ADMIN ====================
        User::updateOrCreate(
            ['email' => 'superadmin@yayasan.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'unit_id' => null,
                'is_active' => true,
            ]
        );

        // ==================== ADMIN UNIT ====================
        $adminUnitEmails = [
            'Daycare' => 'admin.daycare@yayasan.com',
            'Preschool' => 'admin.preschool@yayasan.com',
            'KBIT' => 'admin.kbit@yayasan.com',
            'TKIT' => 'admin.tkit@yayasan.com',
            'TKIP' => 'admin.tkip@yayasan.com',
            'SDIT' => 'admin.sdit@yayasan.com',
            'MI' => 'admin.mi@yayasan.com',
            'SMPIT' => 'admin.smpit@yayasan.com',
            'MA' => 'admin.ma@yayasan.com',
        ];

        foreach ($adminUnitEmails as $unitName => $email) {
            $unit = Unit::where('name', $unitName)->first();
            if ($unit) {
                User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => 'Admin ' . $unitName,
                        'password' => Hash::make('password'),
                        'role' => 'admin_unit',
                        'unit_id' => $unit->id,
                        'is_active' => true,
                    ]
                );
            }
        }

        // ==================== MANAGER ====================
        User::updateOrCreate(
            ['email' => 'manager@yayasan.com'],
            [
                'name' => 'Manager Yayasan',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'unit_id' => null,
                'is_active' => true,
            ]
        );

        // ==================== USERS ====================
        User::updateOrCreate(
            ['email' => 'user.daycare@yayasan.com'],
            [
                'name' => 'User Daycare',
                'password' => Hash::make('password'),
                'role' => 'user',
                'unit_id' => $units['DCP']->id ?? null,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'user.smpit@yayasan.com'],
            [
                'name' => 'User SMPIT',
                'password' => Hash::make('password'),
                'role' => 'user',
                'unit_id' => $units['SMT']->id ?? null,
                'is_active' => true,
            ]
        );

        // ==================== SAMPLE ITEMS ====================
        // ✅ AMBIL CATEGORY ELEKTRONIK
        $electronics = $categories['ELC'] ?? Category::where('code', 'ELC')->first();
        
        // ✅ AMBIL CATEGORY FURNITURE (untuk sample tambahan)
        $furniture = $categories['FRN'] ?? Category::where('code', 'FRN')->first();
        
        // Sample item untuk Daycare
        if ($electronics && isset($units['DCP'])) {
            Item::create([
                // ✅ BENAR: 2 PARAMETER (unit_id, category_id)
                'code' => Item::generateCode($units['DCP']->id, $electronics->id),
                'name' => 'TV LED 32 Inch',
                'category_id' => $electronics->id,
                'unit_id' => $units['DCP']->id,
                'purchase_date' => now()->subMonths(6),
                'condition' => 'baik',
                'price' => 3500000,
                'stock' => 1,
                'location' => 'Ruang Bermain',
                'status' => 'available',
                'description' => 'TV untuk menonton video edukasi anak',
            ]);

            // ✅ TAMBAHKAN SAMPLE ITEM LAIN DENGAN FURNITURE
            if ($furniture) {
                Item::create([
                    'code' => Item::generateCode($units['DCP']->id, $furniture->id),
                    'name' => 'Meja Belajar Anak',
                    'category_id' => $furniture->id,
                    'unit_id' => $units['DCP']->id,
                    'purchase_date' => now()->subMonths(12),
                    'condition' => 'baik',
                    'price' => 750000,
                    'stock' => 5,
                    'location' => 'Ruang Kelas',
                    'status' => 'available',
                    'description' => 'Meja belajar untuk anak daycare',
                ]);
            }
        }

        // Sample item untuk SMPIT
        if ($electronics && isset($units['SMT'])) {
            Item::create([
                // ✅ BENAR: 2 PARAMETER (unit_id, category_id)
                'code' => Item::generateCode($units['SMT']->id, $electronics->id),
                'name' => 'Projector Epson',
                'category_id' => $electronics->id,
                'unit_id' => $units['SMT']->id,
                'purchase_date' => now()->subMonths(3),
                'condition' => 'baik',
                'price' => 8500000,
                'stock' => 1,
                'location' => 'Ruang AV',
                'status' => 'available',
                'description' => 'Projector untuk pembelajaran multimedia',
            ]);

            // ✅ TAMBAHKAN SAMPLE ITEM LAIN
            if ($furniture) {
                Item::create([
                    'code' => Item::generateCode($units['SMT']->id, $furniture->id),
                    'name' => 'Kursi Lipat',
                    'category_id' => $furniture->id,
                    'unit_id' => $units['SMT']->id,
                    'purchase_date' => now()->subMonths(2),
                    'condition' => 'baik',
                    'price' => 250000,
                    'stock' => 20,
                    'location' => 'Aula',
                    'status' => 'available',
                    'description' => 'Kursi lipat untuk acara sekolah',
                ]);
            }
        }
    }
}