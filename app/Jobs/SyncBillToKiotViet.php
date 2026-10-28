<?php

namespace App\Jobs;

use App\models\Bill\Bill;
use App\Services\KiotViet\OrderSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncBillToKiotViet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $timeout = 60;

    protected $billId;

    public function __construct($billId)
    {
        $this->billId = $billId;
    }

    public function handle(OrderSyncService $syncService)
    {
        if (!config('kiotviet.enabled')) {
            return;
        }

        $bill = Bill::find($this->billId);
        if (!$bill) {
            Log::warning('KiotViet sync skipped: bill not found', ['bill_id' => $this->billId]);
            return;
        }

        if (!empty($bill->kiotviet_order_id) && $bill->kiotviet_sync_status === 'synced') {
            return;
        }

        $bill->kiotviet_sync_status = 'pending';
        $bill->kiotviet_sync_error = null;
        $bill->save();

        try {
            $syncService->syncBill($bill);
            Log::info('KiotViet sync success', [
                'code_bill' => $bill->code_bill,
                'kiotviet_order_id' => $bill->kiotviet_order_id,
            ]);
        } catch (Throwable $e) {
            $syncService->markFailed($bill, $e->getMessage());
            Log::error('KiotViet sync failed', [
                'code_bill' => $bill->code_bill,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function failed(Throwable $e)
    {
        $bill = Bill::find($this->billId);
        if ($bill && $bill->kiotviet_sync_status !== 'synced') {
            $bill->kiotviet_sync_status = 'failed';
            $bill->kiotviet_sync_error = mb_substr($e->getMessage(), 0, 2000);
            $bill->save();
        }
    }
}
