<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\ItemStock;
use Illuminate\Console\Command;

class SyncItemStocks extends Command
{
    protected $signature = 'items:sync-stock-codes {--force : Hapus & bikin ulang}';
    protected $description = 'Sync kode stok barang ke tabel item_stocks';

    public function handle(): int
    {
        $items = Item::where('stock', '>', 0)->get();
        $this->info("🔄 Sync " . $items->count() . " barang...");

        $bar = $this->output->createProgressBar($items->count());
        $bar->start();

        foreach ($items as $item) {
            // Skip kalau sudah ada (kecuali --force)
            if (!$this->option('force') && $item->stockCodes()->count() > 0) {
                $bar->advance();
                continue;
            }

            if ($this->option('force')) {
                $item->stockCodes()->delete();
            }

            // Pastikan full_code ada
            if (!$item->full_code) {
                $item->generateFullCode();
                $item->refresh();
            }

            for ($i = 1; $i <= $item->stock; $i++) {
                $code = $item->getStockCode($i);
                if (!$code) continue;

                ItemStock::create([
                    'item_id'      => $item->id,
                    'stock_number' => $i,
                    'stock_code'   => $code,
                    'status'       => 'available',
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('✅ Selesai! Total item_stocks: ' . ItemStock::count());
        return self::SUCCESS;
    }
}