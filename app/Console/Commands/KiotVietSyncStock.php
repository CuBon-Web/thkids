<?php

namespace App\Console\Commands;

use App\Services\KiotViet\StockSyncService;
use Illuminate\Console\Command;
use Throwable;

class KiotVietSyncStock extends Command
{
    protected $signature = 'kiotviet:sync-stock';

    protected $description = 'Đồng bộ tồn kho từ KiotViet về website (products / biến thể)';

    public function handle(StockSyncService $service)
    {
        try {
            $this->info('Đang đồng bộ tồn kho KiotViet...');
            $stats = $service->syncAll();
            $this->table(
                ['fetched', 'products_updated', 'variants_updated', 'skipped'],
                [[
                    $stats['fetched'],
                    $stats['products_updated'],
                    $stats['variants_updated'],
                    $stats['skipped'],
                ]]
            );
            $this->info('Hoàn tất.');
            return 0;
        } catch (Throwable $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }
}
