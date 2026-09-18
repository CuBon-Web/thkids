<?php

namespace App\Http\Controllers;

class RobotsController extends Controller
{
    public function index()
    {
        $base = rtrim(url('/'), '/');
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /crm',
            'Disallow: /admin',
            'Disallow: /api/',
            'Disallow: /clear-cache',
            'Disallow: /auth/',
            'Disallow: /account/',
            'Disallow: /payos/',
            'Disallow: /quickview/',
            'Disallow: /gio-hang',
            'Disallow: /thanh-toan',
            'Disallow: /dang-nhap',
            'Disallow: /dang-ky',
            'Disallow: /dat-hang-thanh-cong',
            'Disallow: /ket-qua-tim-kiem',
            'Disallow: /filter.html',
            'Disallow: /san-pham/so-sanh-san-pham',
            '',
            'Sitemap: ' . $base . '/sitemap.xml',
        ];

        return response(implode("\n", $lines) . "\n", 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
