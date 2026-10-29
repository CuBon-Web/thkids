<?php

namespace App\Services\KiotViet;

use App\models\product\Product;
use App\models\VariantSkuValue;

class StockService
{
    public static function variantMatches($version, $cartVariant)
    {
        $normalize = function ($value) {
            $value = mb_strtolower(trim((string) $value));
            $value = preg_replace('/\s*[\/|]\s*/u', '|', $value);
            $value = preg_replace('/\s*-\s*/u', '|', $value);
            $value = preg_replace('/\s+/u', ' ', $value);
            return $value;
        };

        $a = $normalize($version);
        $b = $normalize($cartVariant);
        if ($a === $b) {
            return true;
        }

        $tokensA = array_values(array_filter(explode('|', $a)));
        $tokensB = array_values(array_filter(explode('|', $b)));
        sort($tokensA);
        sort($tokensB);

        return $tokensA === $tokensB && !empty($tokensA);
    }

    public function findVariant(Product $product, $variantText)
    {
        $variantText = trim((string) $variantText);
        if ($variantText === '') {
            return null;
        }

        $rows = VariantSkuValue::where('product_id', $product->id)->get();
        foreach ($rows as $row) {
            if (static::variantMatches($row->version, $variantText)) {
                return $row;
            }
        }

        return null;
    }

    public function getAvailableQty(Product $product, $variantText = '')
    {
        if ((int) $product->status_variant === 1) {
            $variant = $this->findVariant($product, $variantText);
            if (!$variant) {
                return 0;
            }
            return max(0, (int) $variant->qty);
        }

        return max(0, (int) $product->qty);
    }

    /**
     * @return string|null error message, or null if ok
     */
    public function validateCartLine(Product $product, $quantity, $variantText = '')
    {
        $quantity = (int) $quantity;
        if ($quantity < 1) {
            return 'Số lượng không hợp lệ';
        }

        if ((int) $product->status_variant === 1) {
            $variantText = trim((string) $variantText);
            if ($variantText === '') {
                return 'Vui lòng chọn phân loại sản phẩm';
            }
            $variant = $this->findVariant($product, $variantText);
            if (!$variant) {
                return 'Phân loại sản phẩm không tồn tại';
            }
            $available = max(0, (int) $variant->qty);
            if ($available <= 0) {
                return 'Phân loại này đã hết hàng';
            }
            if ($quantity > $available) {
                return 'Chỉ còn ' . $available . ' sản phẩm cho phân loại này';
            }
            return null;
        }

        $available = max(0, (int) $product->qty);
        if ($available <= 0) {
            return 'Sản phẩm đã hết hàng';
        }
        if ($quantity > $available) {
            return 'Chỉ còn ' . $available . ' sản phẩm trong kho';
        }

        return null;
    }

    public function reduceStock(Product $product, $quantity, $variantText = '')
    {
        $quantity = max(0, (int) $quantity);
        if ($quantity <= 0) {
            return;
        }

        if ((int) $product->status_variant === 1) {
            $variant = $this->findVariant($product, $variantText);
            if ($variant) {
                $variant->qty = max(0, (int) $variant->qty - $quantity);
                $variant->save();
            }
            $sum = (int) VariantSkuValue::where('product_id', $product->id)->sum('qty');
            $product->qty = max(0, $sum);
            $product->status = $sum > 0 ? 1 : 0;
            $product->save();
            return;
        }

        $product->qty = max(0, (int) $product->qty - $quantity);
        $product->status = $product->qty > 0 ? 1 : 0;
        $product->save();
    }
}
