<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixR2MimeTypes extends Command
{
    protected $signature = 'r2:fix-mime {prefix=frontend : R2 object prefix}';

    protected $description = 'Fix Content-Type on existing R2 files (no re-upload)';

    public function handle()
    {
        $disk = Storage::disk('r2');
        $adapter = $disk->getDriver()->getAdapter();
        $client = $adapter->getClient();
        $bucket = config('filesystems.disks.r2.bucket');
        $prefix = trim($this->argument('prefix'), '/');

        $files = $disk->allFiles($prefix);
        if (empty($files)) {
            $this->warn("Không có file nào với prefix: {$prefix}");
            return 0;
        }

        $this->info('Sửa Content-Type cho ' . count($files) . ' file...');
        $fixed = 0;

        foreach ($files as $path) {
            $mime = r2_mime_type_for_path($path);
            if ($mime === 'application/octet-stream') {
                continue;
            }

            try {
                $client->copyObject([
                    'Bucket' => $bucket,
                    'Key' => $path,
                    'CopySource' => $bucket . '/' . $path,
                    'MetadataDirective' => 'REPLACE',
                    'ContentType' => $mime,
                    'ACL' => 'public-read',
                ]);
                $fixed++;
                $this->line("OK: {$path} → {$mime}");
            } catch (\Exception $e) {
                $this->error("Lỗi {$path}: " . $e->getMessage());
            }
        }

        $this->info("Hoàn tất: {$fixed} file.");
        return 0;
    }
}
