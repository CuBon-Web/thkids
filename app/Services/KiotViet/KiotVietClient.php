<?php

namespace App\Services\KiotViet;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class KiotVietClient
{
    public function isConfigured()
    {
        return (bool) config('kiotviet.enabled')
            && config('kiotviet.client_id')
            && config('kiotviet.client_secret')
            && config('kiotviet.retailer');
    }

    public function getAccessToken()
    {
        $cacheKey = config('kiotviet.token_cache_key');
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        $response = Http::asForm()->post(config('kiotviet.token_url'), [
            'scopes' => 'PublicApi.Access',
            'grant_type' => 'client_credentials',
            'client_id' => config('kiotviet.client_id'),
            'client_secret' => config('kiotviet.client_secret'),
        ]);

        if (!$response->ok()) {
            Log::error('KiotViet token failed', ['body' => $response->body()]);
            throw new RuntimeException('Không lấy được access token KiotViet: ' . $response->body());
        }

        $json = $response->json();
        $token = is_array($json) ? ($json['access_token'] ?? null) : null;
        if (!$token || !is_string($token)) {
            throw new RuntimeException('KiotViet không trả về access_token');
        }

        $ttl = (int) ((is_array($json) ? ($json['expires_in'] ?? 0) : 0) ?: config('kiotviet.token_ttl'));
        Cache::put($cacheKey, $token, max(60, $ttl - 300));

        return $token;
    }

    public function get($path, array $query = [])
    {
        return $this->request('get', $path, ['query' => $query]);
    }

    public function post($path, array $payload = [])
    {
        return $this->request('post', $path, ['json' => $payload]);
    }

    public function listBranches()
    {
        return $this->get('/branches');
    }

    public function listProducts(array $query = [])
    {
        return $this->get('/products', $query);
    }

    public function findCustomerByPhone($phone)
    {
        $phone = preg_replace('/\D+/', '', (string) $phone);
        if ($phone === '') {
            return null;
        }

        $result = $this->get('/customers', [
            'contactNumber' => $phone,
            'pageSize' => 1,
        ]);

        $items = $result['data'] ?? [];
        return $items[0] ?? null;
    }

    public function createCustomer(array $payload)
    {
        return $this->post('/customers', $payload);
    }

    public function createOrder(array $payload)
    {
        return $this->post('/orders', $payload);
    }

    protected function request($method, $path, array $options = [])
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('KiotViet chưa được cấu hình (KIOTVIET_*)');
        }

        $url = rtrim(config('kiotviet.api_base'), '/') . '/' . ltrim($path, '/');
        $headers = [
            'Retailer' => config('kiotviet.retailer'),
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json',
        ];

        $pending = Http::withHeaders($headers)->timeout(30);

        if ($method === 'get') {
            $response = $pending->get($url, $options['query'] ?? []);
        } else {
            $response = $pending->post($url, $options['json'] ?? []);
        }

        if ($response->status() === 401) {
            Cache::forget(config('kiotviet.token_cache_key'));
            $headers['Authorization'] = 'Bearer ' . $this->getAccessToken();
            $pending = Http::withHeaders($headers)->timeout(30);
            if ($method === 'get') {
                $response = $pending->get($url, $options['query'] ?? []);
            } else {
                $response = $pending->post($url, $options['json'] ?? []);
            }
        }

        if (!$response->ok()) {
            Log::error('KiotViet API error', [
                'method' => $method,
                'path' => $path,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new RuntimeException('KiotViet API lỗi [' . $response->status() . ']: ' . $response->body());
        }

        return $response->json() ?: [];
    }
}
