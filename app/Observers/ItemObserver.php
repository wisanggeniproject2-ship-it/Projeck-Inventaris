<?php

namespace App\Observers;

use App\Models\Item;
use App\Services\ItemCodeGenerator;

class ItemObserver
{
    /**
     * 🔥 Auto-generate full_code saat item baru dibuat
     */
    public function created(Item $item): void
    {
        // Kalau full_code belum diisi, generate otomatis
        if (empty($item->full_code)) {
            $generator = app(ItemCodeGenerator::class);
            $item->full_code = $generator->generate($item);
            $item->saveQuietly(); // save tanpa trigger event lagi
        }
    }

    /**
     * 🔥 Update full_code kalau ada perubahan nama/kategori/unit/lokasi
     */
    public function updated(Item $item): void
    {
        // Cek apakah field yang mempengaruhi kode berubah
        $dirty = $item->getDirty();
        $fields = ['name', 'category_id', 'unit_id', 'location'];

        if (!empty(array_intersect(array_keys($dirty), $fields))) {
            $generator = app(ItemCodeGenerator::class);
            $item->full_code = $generator->generate($item);
            $item->saveQuietly();
        }
    }
}