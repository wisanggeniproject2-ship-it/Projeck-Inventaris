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
        // Create Units
        $units = [
            ['name' => 'SMP', 'code' => 'SMP', 'description' => 'Sekolah Menengah Pertama'],
            ['name' => 'SMA', 'code' => 'SMA', 'description' => 'Sekolah Menengah Atas'],
            ['name' => 'Administrasi', 'code' => 'ADM', 'description' => 'Unit Administrasi'],
        ];
        
        foreach ($units as $unit) {
            Unit::updateOrCreate(['code' => $unit['code']], $unit);
        }
        
        // Create Categories
        $categories = [
            ['name' => 'Elektronik', 'code' => 'ELC'],
            ['name' => 'Furniture', 'code' => 'FRN'],
            ['name' => 'Alat Tulis', 'code' => 'ATK'],
            ['name' => 'Perlengkapan', 'code' => 'PRL'],
        ];
        
        foreach ($categories as $category) {
            Category::updateOrCreate(['code' => $category['code']], $category);
        }
        
        // Create Users
        $adminUnit = Unit::where('code', 'ADM')->first();
        $smpUnit = Unit::where('code', 'SMP')->first();
        $smaUnit = Unit::where('code', 'SMA')->first();
        
        User::updateOrCreate(
            ['email' => 'admin@yayasan.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'unit_id' => $adminUnit->id,
                'is_active' => true,
            ]
        );
        
        User::updateOrCreate(
            ['email' => 'manager@smp.com'],
            [
                'name' => 'Manager SMP',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'unit_id' => $smpUnit->id,
                'is_active' => true,
            ]
        );
        
        User::updateOrCreate(
            ['email' => 'user@yayasan.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password'),
                'role' => 'user',
                'unit_id' => $smaUnit->id,
                'is_active' => true,
            ]
        );
        
        // Create sample items
        $electronics = Category::where('code', 'ELC')->first();
        
        Item::create([
            'code' => 'LAP-001',
            'name' => 'Laptop Asus',
            'category_id' => $electronics->id,
            'unit_id' => $smpUnit->id,
            'location' => 'Lab Komputer 1',
            'budget_source' => 'APBN',
            'status' => 'available',
        ]);
        
        Item::create([
            'code' => 'PRJ-001',
            'name' => 'Projector Epson',
            'category_id' => $electronics->id,
            'unit_id' => $smaUnit->id,
            'location' => 'Ruang AV',
            'budget_source' => 'APBD',
            'status' => 'available',
        ]);
    }
}