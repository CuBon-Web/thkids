<?php

namespace App\Console\Commands;

use App\Services\KiotViet\KiotVietClient;
use Illuminate\Console\Command;
use Throwable;

class KiotVietListProducts extends Command
{
    protected $signature = 'kiotviet:list-products {--page=0 : currentItem offset} {--size=50 : pageSize}';

    protected $description = 'Liệt kê mã hàng KiotViet để map vào products.kiotviet_code';

    public function handle(KiotVietClient $client)
    {
        try {
            $result = $client->listProducts([
                'currentItem' => (int) $this->option('page'),
                'pageSize' => (int) $this->option('size'),
                'includeInventory' => 'true',
            ]);

            $rows = $result['data'] ?? [];
            if (empty($rows)) {
                $this->warn('Không có sản phẩm nào.');
                return 0;
            }

            $this->table(['id', 'code', 'name', 'basePrice'], array_map(function ($row) {
                return [
                    $row['id'] ?? '',
                    $row['code'] ?? '',
                    mb_substr((string) ($row['name'] ?? ''), 0, 60),
                    $row['basePrice'] ?? ($row['price'] ?? ''),
                ];
            }, $rows));

            $total = $result['total'] ?? count($rows);
            $this->info('Hiển thị ' . count($rows) . ' / tổng ~' . $total);
            $this->line('Dùng mã (code) gán vào field "Mã hàng KiotViet" trên admin sản phẩm.');
            return 0;
        } catch (Throwable $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }
}
