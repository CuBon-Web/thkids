<?php

namespace App\Services\KiotViet;

use App\models\product\Product;
use App\models\VariantSkuValue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class StockSyncService
{
    protected $client;

    public function __construct(KiotVietClient $client)
    {
        $this->client = $client;
    }

    /**
     * Sync stock for one product (and its mapped variants) from KiotViet.
     * Used on product detail page for fresh qty.
     */
    public function syncProduct(Product $product, $force = false)
    {
        if (!$this->client->isConfigured()) {
            return false;
        }

        $cacheKey = 'kiotviet_stock_product_' . $product->id;
        if (!$force && Cache::has($cacheKey)) {
            return true;
        }

        try {
            $branchId = (int) config('kiotviet.branch_id');
            $updated = false;

            if ((int) $product->status_variant === 1) {
                $variants = VariantSkuValue::where('product_id', $product->id)
                    ->whereNotNull('kiotviet_code')
                    ->where('kiotviet_code', '!=', '')
                    ->get();

                foreach ($variants as $variant) {
                    $row = $this->client->getProductByCode($variant->kiotviet_code);
                    if (!$row || empty($row['code'])) {
                        continue;
                    }
                    $onHand = $this->resolveOnHand($row, $branchId);
                    $variant->qty = $onHand;
                    $variant->save();
                    $updated = true;
                }

                if ($updated) {
                    $sum = (int) VariantSkuValue::where('product_id', $product->id)->sum('qty');
                    $product->qty = max(0, $sum);
                    // Keep product visible even if out of stock so detail page can show "Hết hàng".
                    $product->save();
                }
            } else {
                $code = trim((string) ($product->kiotviet_code ?? ''));
                if ($code !== '') {
                    $row = $this->client->getProductByCode($code);
                    if ($row && !empty($row['code'])) {
                        $onHand = $this->resolveOnHand($row, $branchId);
                        $product->qty = $onHand;
                        $product->save();
                        $updated = true;
                    }
                }
            }

            Cache::put($cacheKey, 1, 120); // 2 phút, tránh gọi KV liên tục khi F5
            return $updated;
        } catch (\Throwable $e) {
            Log::warning('KiotViet product stock sync failed', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function syncAll()
    {
        if (!$this->client->isConfigured()) {
            throw new \RuntimeException('KiotViet chưa được cấu hình');
        }

        $branchId = (int) config('kiotviet.branch_id');
        $productMap = Product::query()
            ->whereNotNull('kiotviet_code')
            ->where('kiotviet_code', '!=', '')
            ->pluck('id', 'kiotviet_code')
            ->mapWithKeys(function ($id, $code) {
                return [strtoupper(trim((string) $code)) => (int) $id];
            })
            ->all();

        $variantMap = VariantSkuValue::query()
            ->whereNotNull('kiotviet_code')
            ->where('kiotviet_code', '!=', '')
            ->get(['id', 'product_id', 'kiotviet_code'])
            ->mapWithKeys(function ($row) {
                return [strtoupper(trim((string) $row->kiotviet_code)) => $row];
            });

        $stats = [
            'fetched' => 0,
            'products_updated' => 0,
            'variants_updated' => 0,
            'skipped' => 0,
        ];

        $pageSize = 100;
        $currentItem = 0;

        do {
            $result = $this->client->listProducts([
                'currentItem' => $currentItem,
                'pageSize' => $pageSize,
                'includeInventory' => 'true',
            ]);

            $rows = $result['data'] ?? [];
            $total = (int) ($result['total'] ?? 0);
            $stats['fetched'] += count($rows);

            foreach ($rows as $row) {
                $code = strtoupper(trim((string) ($row['code'] ?? '')));
                if ($code === '') {
                    $stats['skipped']++;
                    continue;
                }

                $onHand = $this->resolveOnHand($row, $branchId);

                if (isset($variantMap[$code])) {
                    $variant = $variantMap[$code];
                    VariantSkuValue::where('id', $variant->id)->update(['qty' => $onHand]);
                    $stats['variants_updated']++;
                    continue;
                }

                if (isset($productMap[$code])) {
                    $productId = $productMap[$code];
                    $product = Product::find($productId);
                    if ($product && (int) $product->status_variant !== 1) {
                        $product->qty = $onHand;
                        $product->status = $onHand > 0 ? 1 : 0;
                        $product->save();
                        $stats['products_updated']++;
                    } else {
                        $stats['skipped']++;
                    }
                    continue;
                }

                $stats['skipped']++;
            }

            $currentItem += $pageSize;
            if (empty($rows) || ($total > 0 && $currentItem >= $total)) {
                break;
            }
        } while (true);

        // Refresh parent qty/status for products that have variants mapped.
        $productIds = VariantSkuValue::query()
            ->whereNotNull('kiotviet_code')
            ->where('kiotviet_code', '!=', '')
            ->distinct()
            ->pluck('product_id');

        foreach ($productIds as $productId) {
            $sum = (int) VariantSkuValue::where('product_id', $productId)->sum('qty');
            Product::where('id', $productId)->update([
                'qty' => max(0, $sum),
                'status' => $sum > 0 ? 1 : 0,
            ]);
        }

        Log::info('KiotViet stock sync done', $stats);

        return $stats;
    }

    protected function resolveOnHand(array $row, $branchId)
    {
        $inventories = $row['inventories'] ?? [];
        if (!is_array($inventories) || empty($inventories)) {
            return max(0, (int) ($row['onHand'] ?? 0));
        }

        if ($branchId > 0) {
            foreach ($inventories as $inv) {
                if ((int) ($inv['branchId'] ?? 0) === $branchId) {
                    return max(0, (int) ($inv['onHand'] ?? 0));
                }
            }
        }

        $first = $inventories[0] ?? [];
        return max(0, (int) ($first['onHand'] ?? 0));
    }
}
