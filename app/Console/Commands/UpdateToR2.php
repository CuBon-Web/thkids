<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class UpdateToR2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:r2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload tất cả file CSS lên Cloudflare R2 và xóa cache';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $localPath = public_path('frontend'); // Thư mục chứa CSS
        $disk = Storage::disk('r2');

        if (!is_dir($localPath)) {
            $this->error("Thư mục frontend không tồn tại!");
            return;
        }

        // Duyệt tất cả file CSS
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($localPath));
        $updatedFiles = [];

        foreach ($iterator as $file) {
            if ($file->isDir() || $file->getExtension() !== 'css') continue;

            $filePath = $file->getRealPath();
            $relativePath = str_replace($localPath . DIRECTORY_SEPARATOR, '', $filePath);
            $cloudPath = "frontend/" . str_replace('\\', '/', $relativePath);

            // Upload file CSS lên Cloudflare R2
            $disk->put($cloudPath, file_get_contents($filePath));

            // Xóa cache Cloudflare
            // $fileUrl = 'https://your-cdn-domain.com/' . $cloudPath;
            // $this->purgeCloudflareCache($fileUrl);

            // $updatedFiles[] = $fileUrl;
            // $this->info("Đã upload và xóa cache: $cloudPath");
        }

        $this->info("Cập nhật CSS hoàn tất!");
    }
}
