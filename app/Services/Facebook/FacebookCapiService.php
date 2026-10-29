<?php

namespace App\Services\Facebook;

use App\models\Bill\Bill;
use App\models\Bill\BillDetail;
use App\models\website\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FacebookCapiService
{
    public static function pixelId()
    {
        try {
            $setting = Setting::query()->first(['fb_pixel_id']);
            if ($setting && trim((string) $setting->fb_pixel_id) !== '') {
                return trim((string) $setting->fb_pixel_id);
            }
        } catch (\Throwable $e) {
            // ignore during migrate / missing column
        }

        return (string) config('facebook.pixel_id');
    }

    public static function accessToken()
    {
        try {
            $setting = Setting::query()->first(['fb_capi_token']);
            if ($setting && trim((string) $setting->fb_capi_token) !== '') {
                return trim((string) $setting->fb_capi_token);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return (string) config('facebook.access_token');
    }

    public function isConfigured()
    {
        return (bool) config('facebook.enabled')
            && static::pixelId()
            && static::accessToken();
    }

    public function makeEventId($prefix = 'evt')
    {
        return $prefix . '_' . Str::uuid()->toString();
    }

    public function sendPurchase(Bill $bill, $eventId = null, array $requestMeta = [])
    {
        if (!$this->isConfigured()) {
            Log::info('Facebook CAPI skipped: missing access token or disabled', [
                'code_bill' => $bill->code_bill ?? null,
            ]);
            return false;
        }

        $eventId = $eventId ?: $this->makeEventId('purchase');
        $details = BillDetail::where('code_bill', $bill->code_bill)->get();
        $contents = [];
        $numItems = 0;
        foreach ($details as $detail) {
            $qty = (int) $detail->qty;
            $numItems += $qty;
            $contents[] = [
                'id' => (string) $detail->code_product,
                'quantity' => $qty,
                'item_price' => (float) $detail->price,
            ];
        }

        $userData = $this->buildUserData($bill, $requestMeta);
        $customData = [
            'currency' => 'VND',
            'value' => (float) $bill->total_money,
            'content_type' => 'product',
            'contents' => $contents,
            'num_items' => $numItems,
            'order_id' => (string) $bill->code_bill,
        ];

        $event = [
            'event_name' => 'Purchase',
            'event_time' => time(),
            'event_id' => $eventId,
            'event_source_url' => $requestMeta['event_source_url'] ?? url('/'),
            'action_source' => 'website',
            'user_data' => $userData,
            'custom_data' => $customData,
        ];

        return $this->sendEvents([$event]);
    }

    public function sendPageView(array $requestMeta = [], $eventId = null)
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $event = [
            'event_name' => 'PageView',
            'event_time' => time(),
            'event_id' => $eventId ?: $this->makeEventId('pv'),
            'event_source_url' => $requestMeta['event_source_url'] ?? url('/'),
            'action_source' => 'website',
            'user_data' => $this->buildUserDataFromRequest($requestMeta),
        ];

        return $this->sendEvents([$event]);
    }

    protected function sendEvents(array $events)
    {
        $pixelId = static::pixelId();
        $token = static::accessToken();
        $version = config('facebook.api_version', 'v21.0');
        $url = "https://graph.facebook.com/{$version}/{$pixelId}/events";

        $payload = [
            'data' => $events,
            'access_token' => $token,
        ];

        $testCode = config('facebook.test_event_code');
        if (!empty($testCode)) {
            $payload['test_event_code'] = $testCode;
        }

        try {
            $response = Http::asJson()->timeout(15)->post($url, $payload);
            if (!$response->ok()) {
                Log::error('Facebook CAPI error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return false;
            }

            Log::info('Facebook CAPI success', [
                'events' => array_column($events, 'event_name'),
                'response' => $response->json(),
            ]);
            return true;
        } catch (\Throwable $e) {
            Log::error('Facebook CAPI exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    protected function buildUserData(Bill $bill, array $requestMeta = [])
    {
        $userData = $this->buildUserDataFromRequest($requestMeta);

        $email = strtolower(trim((string) ($bill->cus_email ?? '')));
        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $userData['em'] = [$this->hash($email)];
        }

        $phone = preg_replace('/\D+/', '', (string) ($bill->cus_phone ?? ''));
        if ($phone !== '') {
            if (strpos($phone, '84') === 0) {
                // already country code
            } elseif (strpos($phone, '0') === 0) {
                $phone = '84' . substr($phone, 1);
            } else {
                $phone = '84' . $phone;
            }
            $userData['ph'] = [$this->hash($phone)];
        }

        $name = trim((string) ($bill->cus_name ?? ''));
        if ($name !== '') {
            $parts = preg_split('/\s+/u', mb_strtolower($name));
            if (!empty($parts)) {
                $userData['ln'] = [$this->hash(end($parts))];
                if (count($parts) > 1) {
                    $userData['fn'] = [$this->hash($parts[0])];
                }
            }
        }

        $userData['country'] = [$this->hash('vn')];

        return $userData;
    }

    protected function buildUserDataFromRequest(array $requestMeta = [])
    {
        $userData = [];
        $clientIp = $requestMeta['client_ip_address'] ?? null;
        $userAgent = $requestMeta['client_user_agent'] ?? null;
        $fbp = $requestMeta['fbp'] ?? null;
        $fbc = $requestMeta['fbc'] ?? null;

        if ($clientIp) {
            $userData['client_ip_address'] = $clientIp;
        }
        if ($userAgent) {
            $userData['client_user_agent'] = $userAgent;
        }
        if ($fbp) {
            $userData['fbp'] = $fbp;
        }
        if ($fbc) {
            $userData['fbc'] = $fbc;
        }

        return $userData;
    }

    protected function hash($value)
    {
        return hash('sha256', (string) $value);
    }
}
