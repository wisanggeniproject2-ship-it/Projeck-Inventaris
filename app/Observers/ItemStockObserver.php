<?php

namespace App\Observers;

use App\Models\ItemStock;

class ItemStockObserver
{
    /**
     * 🔥 Auto-sync saat item_stock DIBUAT
     */
    public function created(ItemStock $itemStock): void
    {
        $this->syncItem($itemStock);
    }

    /**
     * 🔥 Auto-sync saat item_stock DIUPDATE
     * (mis. status: available → borrowed, atau borrowed → available)
     */
    public function updated(ItemStock $itemStock): void
    {
        // Cuma sync kalau status berubah (biar efisien)
        if ($itemStock->wasChanged('status')) {
            $this->syncItem($itemStock);
        }
    }

    /**
     * 🔥 Auto-sync saat item_stock DIHAPUS
     */
    public function deleted(ItemStock $itemStock): void
    {
        $this->syncItem($itemStock);
    }

    /**
     * Helper: sync stok item parent
     */
    protected function syncItem(ItemStock $itemStock): void
    {
        $item = $itemStock->item;
        
        if ($item) {
            $item->syncStockFromItemStocks();
        }
    }
}