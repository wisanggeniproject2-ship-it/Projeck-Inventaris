<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Services\ItemCodeGenerator;
use Illuminate\Console\Command;

class BackfillFullCode extends Command
{
    protected $signature = 'items:backfill-full-code {--force : Force update yang sudah ada}';
    protected $description = 'Isi full_code untuk item yang belum punya';

    public function handle(): int
    {
        $generator = app(ItemCodeGenerator::class);

        $query = Item::query();

        if (!$this->option('force')) {
            $query->whereNull('full_code');
        }

        $total = $query->count();

        if ($total === 0) {
            $this->info('✅ Tidak ada item yang perlu di-backfill.');
            return self::SUCCESS;
        }

        $this->info("🔄 Backfill {$total} item...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->chunk(100, function ($items) use ($generator, $bar) {
            foreach ($items as $item) {
                $item->full_code = $generator->generate($item);
                $item->saveQuietly();
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info('✅ Selesai!');

        return self::SUCCESS;
    }
}