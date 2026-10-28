<?php

namespace App\Console\Commands;

use App\Services\KiotViet\KiotVietClient;
use Illuminate\Console\Command;
use Throwable;

class KiotVietListBranches extends Command
{
    protected $signature = 'kiotviet:list-branches';

    protected $description = 'Liệt kê chi nhánh KiotViet để lấy KIOTVIET_BRANCH_ID';

    public function handle(KiotVietClient $client)
    {
        try {
            $result = $client->listBranches();
            $rows = $result['data'] ?? [];
            if (empty($rows)) {
                $this->warn('Không có chi nhánh nào.');
                return 0;
            }

            $this->table(['id', 'branchName', 'address'], array_map(function ($row) {
                return [
                    $row['id'] ?? '',
                    $row['branchName'] ?? ($row['name'] ?? ''),
                    $row['address'] ?? '',
                ];
            }, $rows));

            $firstId = $rows[0]['id'] ?? null;
            if ($firstId) {
                $this->info('Gợi ý: đặt KIOTVIET_BRANCH_ID=' . $firstId . ' trong .env');
            }
            return 0;
        } catch (Throwable $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }
}
