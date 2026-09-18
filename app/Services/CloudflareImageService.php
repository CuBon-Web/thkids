<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class CloudflareImageService
{
    protected $apiToken;
    protected $accountId;

    public function __construct()
    {
        $this->apiToken = env('CLOUDFLARE_IMAGES_API_TOKEN');
        $this->accountId = env('CLOUDFLARE_ACCOUNT_ID');
    }

    /**
     * Upload ảnh lên Cloudflare Images
     */
    public function uploadImage($image)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken
        ])->attach(
            'file', file_get_contents($image), $image->getClientOriginalName()
        )->post("https://api.cloudflare.com/client/v4/accounts/{$this->accountId}/images/v1");

       

        return $response->json();
    }

    /**
     * Xóa ảnh trên Cloudflare Images (nhận URL imagedelivery.net hoặc image id).
     */
    public function deleteImage($imageUrl)
    {
        $imageUrl = $this->normalizeImageUrl($imageUrl);
        if (empty($imageUrl) || empty($this->apiToken) || empty($this->accountId)) {
            return null;
        }

        $id = $this->extractImageId($imageUrl);
        if (!$id) {
            return null;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken,
        ])->delete("https://api.cloudflare.com/client/v4/accounts/{$this->accountId}/images/v1/{$id}");

        return $response->json();
    }

    /**
     * Xóa ảnh theo URL đã lưu: Cloudflare Images hoặc file local (dữ liệu cũ).
     */
    public function deleteImageByUrl($url)
    {
        $url = $this->normalizeImageUrl($url);
        if (empty($url)) {
            return null;
        }

        if (stripos($url, 'imagedelivery.net') !== false) {
            return $this->deleteImage($url);
        }

        $path = parse_url($url, PHP_URL_PATH) ?: $url;
        $file = public_path(ltrim($path, '/'));

        if (is_file($file)) {
            @unlink($file);
        }

        return null;
    }

    /**
     * @param mixed $value URL string hoặc mảng/object từ JSON (vd: ['url' => '...'])
     */
    protected function normalizeImageUrl($value)
    {
        if (is_string($value)) {
            $value = trim($value);
            return $value !== '' ? $value : null;
        }

        if (is_array($value)) {
            foreach (['url', 'path', 'src', 'image'] as $key) {
                if (!empty($value[$key]) && is_string($value[$key])) {
                    return trim($value[$key]);
                }
            }
            foreach ($value as $item) {
                $normalized = $this->normalizeImageUrl($item);
                if ($normalized) {
                    return $normalized;
                }
            }
        }

        if (is_object($value)) {
            return $this->normalizeImageUrl((array) $value);
        }

        return null;
    }

    protected function extractImageId($imageUrl)
    {
        if (!is_string($imageUrl)) {
            return null;
        }

        if (preg_match('/^[a-f0-9\-]{36}$/i', $imageUrl)) {
            return $imageUrl;
        }

        if (preg_match('/imagedelivery\.net\/[^\/]+\/([a-f0-9\-]+)\//i', $imageUrl, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
