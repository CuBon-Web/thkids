<?php

namespace App\Services\KiotViet;

use App\models\Bill\Bill;
use App\models\Bill\BillDetail;
use App\models\product\Product;
use App\models\VariantSkuValue;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class OrderSyncService
{
    protected $client;

    public function __construct(KiotVietClient $client)
    {
        $this->client = $client;
    }

    public function syncBill(Bill $bill)
    {
        if (!$this->client->isConfigured()) {
            throw new RuntimeException('KiotViet chưa bật hoặc thiếu cấu hình');
        }

        if (!empty($bill->kiotviet_order_id) && $bill->kiotviet_sync_status === 'synced') {
            return $bill;
        }

        $branchId = (int) config('kiotviet.branch_id');
        if ($branchId <= 0) {
            throw new RuntimeException('Thiếu KIOTVIET_BRANCH_ID. Chạy: php artisan kiotviet:list-branches');
        }

        $details = BillDetail::where('code_bill', $bill->code_bill)->get();
        if ($details->isEmpty()) {
            throw new RuntimeException('Đơn hàng không có dòng sản phẩm');
        }

        $orderDetails = [];
        $missingCodes = [];

        foreach ($details as $detail) {
            $resolved = $this->resolveLineProductCode($detail);
            $code = $resolved['code'];
            if ($code === '') {
                $label = $detail->name . ' (id=' . $detail->code_product . ')';
                if (!empty($detail->variant)) {
                    $label .= ' [' . $detail->variant . ']';
                }
                $missingCodes[] = $label;
                continue;
            }

            $line = [
                'productCode' => $code,
                'productName' => (string) $detail->name,
                'quantity' => (float) $detail->qty,
                'price' => (float) $detail->price,
            ];
            if (!empty($detail->variant)) {
                $line['note'] = (string) $detail->variant;
            }
            $orderDetails[] = $line;
        }

        if (!empty($missingCodes)) {
            throw new RuntimeException('Thiếu mã KiotViet cho sản phẩm: ' . implode('; ', $missingCodes));
        }

        $customerId = $this->resolveCustomerId($bill);
        $isOnline = ($bill->payment_method ?? 'cod') === 'online';
        $totalMoney = (float) $bill->total_money;

        $description = 'Web DH' . $bill->code_bill;
        $shipping = (float) ($bill->transport_price ?? 0);
        if ($shipping > 0) {
            $description .= ' | Ship: ' . number_format($shipping, 0, ',', '.') . 'đ';
        }
        if (!empty($bill->note)) {
            $description .= ' | ' . mb_substr((string) $bill->note, 0, 200);
        }

        // Không gửi purchaseDate (gian hàng chưa bật đổi thời gian bán hàng).
        // makeInvoice=false: chỉ tạo Đặt hàng, không lập HĐ ngay → tránh trạng thái "Hoàn thành".
        // Shop xử lý giao hàng / lập hóa đơn trên KiotViet.
        $payload = [
            'branchId' => $branchId,
            'description' => $description,
            'method' => $isOnline ? 'Transfer' : 'Cash',
            'totalPayment' => 0,
            'makeInvoice' => false,
            'orderDetails' => $orderDetails,
            'orderDelivery' => [
                'receiver' => (string) ($bill->cus_name ?: 'Khách web'),
                'contactNumber' => (string) ($bill->cus_phone ?: ''),
                'address' => (string) ($bill->cus_address ?: ''),
            ],
        ];

        if ($customerId) {
            $payload['customerId'] = $customerId;
        }

        Log::info('KiotViet sync order payload', [
            'code_bill' => $bill->code_bill,
            'payload' => $payload,
        ]);

        $result = $this->client->createOrder($payload);
        $orderId = $result['id'] ?? ($result['data']['id'] ?? null);
        $orderCode = $result['code'] ?? ($result['data']['code'] ?? null);

        $bill->kiotviet_order_id = (string) ($orderId ?: $orderCode);
        $bill->kiotviet_sync_status = 'synced';
        $bill->kiotviet_synced_at = now();
        $bill->kiotviet_sync_error = null;
        $bill->save();

        return $bill;
    }

    public function markFailed(Bill $bill, $message)
    {
        $bill->kiotviet_sync_status = 'failed';
        $bill->kiotviet_sync_error = mb_substr((string) $message, 0, 2000);
        $bill->save();
    }

    /**
     * Prefer variant kiotviet_code when bill line has a selected variant;
     * otherwise fall back to parent product kiotviet_code.
     */
    protected function resolveLineProductCode(BillDetail $detail)
    {
        $productId = (int) $detail->code_product;
        $variantText = trim((string) ($detail->variant ?? ''));

        if ($variantText !== '' && $productId > 0) {
            $variants = VariantSkuValue::where('product_id', $productId)->get();
            foreach ($variants as $variant) {
                if (!$this->variantMatches($variant->version, $variantText)) {
                    continue;
                }
                return [
                    'code' => trim((string) ($variant->kiotviet_code ?? '')),
                    'source' => 'variant',
                    'version' => $variant->version,
                ];
            }

            // Khách đã chọn biến thể nhưng không map được → không fallback mã cha (tránh trừ sai tồn).
            return [
                'code' => '',
                'source' => 'unmatched_variant',
                'version' => $variantText,
            ];
        }

        $product = Product::find($productId);
        return [
            'code' => $product ? trim((string) ($product->kiotviet_code ?? '')) : '',
            'source' => 'product',
            'version' => null,
        ];
    }

    protected function variantMatches($version, $cartVariant)
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

    protected function resolveCustomerId(Bill $bill)
    {
        $phone = preg_replace('/\D+/', '', (string) $bill->cus_phone);
        if ($phone === '') {
            return null;
        }

        $existing = $this->client->findCustomerByPhone($phone);
        if ($existing && !empty($existing['id'])) {
            return (int) $existing['id'];
        }

        $created = $this->client->createCustomer([
            'name' => (string) ($bill->cus_name ?: 'Khách web'),
            'contactNumber' => $phone,
            'email' => (string) ($bill->cus_email ?: ''),
            'address' => (string) ($bill->cus_address ?: ''),
            'branchId' => (int) config('kiotviet.branch_id'),
        ]);

        $id = (int) ($created['data']['id'] ?? $created['id'] ?? 0);

        return $id > 0 ? $id : null;
    }
}
