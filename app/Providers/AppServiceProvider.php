<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Item;
use App\Models\ItemStock;
use App\Observers\ItemObserver;
use App\Observers\ItemStockObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 🔥 Observer untuk Item
        // Auto-generate full_code saat item baru dibuat / diupdate
        Item::observe(ItemObserver::class);

        // 🔥 Observer untuk ItemStock
        // Auto-sync items.stock setiap kali item_stocks berubah
        // (status berubah: available ↔ borrowed ↔ disposed ↔ maintenance)
        ItemStock::observe(ItemStockObserver::class);
    }
}