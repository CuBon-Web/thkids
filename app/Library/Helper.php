<?php
if (!function_exists('handle')) {
    function handle($request)
    {
        $input = $request->all();
        if ($input) {
            array_walk_recursive($input, function (&$item) {
                $item = trim($item);
                $item = ($item == "") ? null : $item;
            });
            $request->merge($input);
        }
        return $request->all();
    }
}
if(!function_exists('uploadImage')){
    function uploadImage($img){
        $nameAvatar = rand().$img->getClientOriginalName();
        $img->move('uploads/customer/', $nameAvatar);
        return '/uploads/customer/'.$nameAvatar;
    }
}
if(!function_exists('checkExistCompare')){
    function checkExistCompare($id){
        $compare = session()->get('compareProduct', []);
        if(isset($compare[$id])){
            return true;
        }else{
            return false;
        }
    }
}
if(!function_exists('stripVN')){
    function stripVN($str) {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
    
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        return $str;
    }
}
if(!function_exists('get_price_variant')){
    function get_price_variant($proid){
        $data = new \App\models\VariantSkuValue();
        return $data->getPriceProductVariant($proid);
    }
}
if (!function_exists('getConfig')) {
    function getConfig($key)
    {
        $setting = new \App\models\Client\Setting();
        return $setting->getValue($key);
    }
}

if (!function_exists('getLanguage')) {
    function getLanguage($key)
    {
        $session =  Session::get('locale');
        if($session != null){
            $code = Session::get('locale');
        }else{
            Session::put('locale', app()->getLocale());
        }
        $code = Session::get('locale');
        $lang = new \App\models\frontend\LanguageStaticByLang();
        return $lang->getLanguageValue($key, $code);
    }
}
if(!function_exists('toArrayLanguage')){
    function toArrayLanguage($value){
        $session =  Session::get('locale');
        if($session != null){
            $code = Session::get('locale');
        }else{
            Session::put('locale', app()->getLocale());
        }
        $code = Session::get('locale');
        $arr = [];
        $obj = new stdClass();
        $obj->lang_code = $code;
        $obj->content = $value;
        $arr[] = $obj;
        return json_encode($arr);
    }
}
if(!function_exists('languageName')){
    function languageName($arrName){
        $decodeUnicodeEscapes = function ($value) {
            if (!is_string($value) || $value === '') {
                return $value;
            }

            if (strpos($value, '\u') === false) {
                return $value;
            }

            $decoded = json_decode('"' . addcslashes($value, "\\\"/\n\r\t\f\b") . '"');
            return is_string($decoded) ? $decoded : $value;
        };

        if ($arrName === null || $arrName === '') {
            return '';
        }

        if (is_array($arrName)) {
            $arr = $arrName;
        } elseif (is_object($arrName)) {
            $arr = [$arrName];
        } else {
            $arr = json_decode($arrName);
            // Fallback for plain text values that are not JSON multilingual payloads.
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($arr)) {
                return $decodeUnicodeEscapes((string) $arrName);
            }
        }

        $session =  Session::get('locale');
        if($session != null){
            $code = Session::get('locale');
        }else{
            Session::put('locale', app()->getLocale());
        }
        $code = Session::get('locale');
        foreach($arr as $item){
            if (is_object($item) && isset($item->lang_code) && $item->lang_code == $code) {
                return $decodeUnicodeEscapes($item->content ?? '');
            }
            if (is_array($item) && isset($item['lang_code']) && $item['lang_code'] == $code) {
                return $decodeUnicodeEscapes($item['content'] ?? '');
            }
        }
        // Fallback to first item content when locale entry is missing.
        $first = $arr[0] ?? null;
        if (is_object($first)) {
            return $decodeUnicodeEscapes($first->content ?? '');
        }
        if (is_array($first)) {
            return $decodeUnicodeEscapes($first['content'] ?? '');
        }
        return $decodeUnicodeEscapes((string) $arrName);
    }
}

if (!function_exists('firstBeforeAfterImage')) {
    /**
     * Lấy path ảnh từ dữ liệu before/after (JSON mảng cặp, mảng PHP, hoặc path string).
     *
     * @param mixed  $imageData  JSON string | array | object
     * @param int    $index      Chỉ số cặp ảnh (mặc định 0 = cặp đầu tiên)
     * @param string $prefer     'after' hoặc 'before' — ưu tiên ảnh nào trước
     * @return string            Đường dẫn ảnh hoặc chuỗi rỗng
     */
    function firstBeforeAfterImage($imageData, $index = 0, $prefer = 'after')
    {
        if ($imageData === null || $imageData === '') {
            return '';
        }

        $pairs = $imageData;
        if (is_string($imageData)) {
            $decoded = json_decode($imageData, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $pairs = $decoded;
            } elseif (strpos($imageData, '/') !== false) {
                return $imageData;
            } else {
                return '';
            }
        }

        if (!is_array($pairs) || !isset($pairs[$index])) {
            return '';
        }

        $pair = $pairs[$index];
        if (is_string($pair)) {
            return $pair;
        }
        if (!is_array($pair)) {
            return '';
        }

        $primary   = $prefer === 'before' ? 'before' : 'after';
        $secondary = $prefer === 'before' ? 'after' : 'before';

        return $pair[$primary] ?? $pair[$secondary] ?? '';
    }
}

if(!function_exists('checkExistCart')){
    function checkExistCart($proid){
        $cart = Session::get('cart', []);
        foreach($cart as $item){
            if($item['idpro'] == $proid){
                return true;
            }else{
                return false;
            }
        }
    }
}
if (!function_exists('getMenu')) {
    function getMenu($position)
    {
        $data = [];
        $menu = new \App\models\Client\Menu();
        $menuGroup = new \App\models\Client\MenuGroup();
        $result = $menuGroup->getMenuPosition($position);
        foreach ($result as $i => $item) {
            $data[$i] = $menu->getTree(1, $item['id'], getConfig('config_logo'));
        }
        return $data;
    }
}

if (!function_exists('getActiveLanguages')) {
    function getActiveLanguages()
    {
        $language = new \App\models\frontend\Language();
        $data = $language->getLanguages();
        return $data;
    }
}

if (!function_exists('getCurrentLang')) {

    function getCurrentLang()
    {
        $locale = request()->cookie('locale');
        if (!isset($locale)) {
            $locale = getConfig('config_language');
        }
        return $locale;
    }
}

if (!function_exists('shorten_string')) {
    function shorten_string($string, $words = 50)
    {
        $string = preg_replace('/(?<=\S,)(?=\S)/', ' ', $string);
        $string = str_replace("\n", " ", $string);
        $array = explode(" ", $string);
        if (count($array) <= $words) {
            $retval = $string;
        } else {
            array_splice($array, $words);
            $retval = implode(" ", $array) . " ...";
        }
        return $retval;
    }
}

if(!function_exists('to_slug')){
    function to_slug($str){
        $str = trim(mb_strtolower($str));
        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
        $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
        $str = preg_replace('/(đ)/', 'd', $str);
        $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
        $str = preg_replace('/([\s]+)/', '-', $str);
        return $str;
    }
}

if (!function_exists('resizeImage')) {
    function resizeImage($originalImagePath, $newImagePath, $width = 50, $height = 50)
    {
        $name = preg_split('/[\/]+/', $originalImagePath);
        if (!file_exists('cache/' . $name[0] . '/' . $name[1])) {
            mkdir('cache/' . $name[0] . '/' . $name[1], 0777, true);
        }
        if (file_exists($originalImagePath)) {
            if (!file_exists($newImagePath)) {
                $image = imagecreatefromstring(file_get_contents($originalImagePath));
                $thumb_width = $width;
                $thumb_height = $height;
                $width = imagesx($image);
                $height = imagesy($image);
                $original_aspect = $width / $height;
                $thumb_aspect = $thumb_width / $thumb_height;
                if ($original_aspect >= $thumb_aspect) {
                    $new_height = $thumb_height;
                    $new_width = $width / ($height / $thumb_height);
                } else {
                    $new_width = $thumb_width;
                    $new_height = $height / ($width / $thumb_width);
                }
                $thumb = imagecreatetruecolor($thumb_width, $thumb_height);
                imagecopyresampled($thumb,
                    $image,
                    0 - ($new_width - $thumb_width) / 2,
                    0 - ($new_height - $thumb_height) / 2,
                    0, 0,
                    $new_width, $new_height,
                    $width, $height);
                imagejpeg($thumb, $newImagePath, 80);
            }
        }
    }
}
if (!function_exists('r2_mime_type_for_path')) {
    function r2_mime_type_for_path($filePath)
    {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $map = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'html' => 'text/html',
            'htm' => 'text/html',
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject',
            'ico' => 'image/x-icon',
            'map' => 'application/json',
        ];

        if (isset($map[$ext])) {
            return $map[$ext];
        }

        if (is_file($filePath) && function_exists('mime_content_type')) {
            $detected = mime_content_type($filePath);
            if ($detected && $detected !== 'text/plain') {
                return $detected;
            }
        }

        return 'application/octet-stream';
    }
}
if (!function_exists('r2_asset')) {
    function r2_asset($path)
    {
        $base = config('filesystems.disks.r2.url');
        if (!$base) {
            return asset(ltrim($path, '/'));
        }

        return rtrim($base, '/') . '/' . ltrim($path, '/');
    }
}
if (!function_exists('lazy_img')) {
    /**
     * @param string $src
     * @param string $alt
     * @param array $options eager, class, width, height, lazy
     */
    function lazy_img($src, $alt = '', $options = [])
    {
        return view('partials.img', array_merge([
            'src' => $src,
            'alt' => $alt,
        ], $options))->render();
    }
}
if (!function_exists('static_bg')) {
    /** data-background URL for theme static images (R2 when configured). */
    function static_bg($path)
    {
        $path = ltrim($path, '/');
        if (strpos($path, 'frontend/') === 0) {
            return r2_asset($path);
        }

        return url($path);
    }
}
if (!function_exists('seo_canonical_url')) {
    /** Canonical URL without query/tracking params. */
    function seo_canonical_url(?string $override = null): string
    {
        if ($override) {
            return $override;
        }

        return url()->current();
    }
}
if (!function_exists('seo_is_noindex_route')) {
    function seo_is_noindex_route(): bool
    {
        $routeName = optional(request()->route())->getName();

        $noindexRoutes = [
            'login', 'postlogin', 'register', 'postRegister', 'logout',
            'listCart', 'checkout', 'postBill', 'orderSuccess',
            'payos.return', 'payos.cancel', 'payos.webhook',
            'accoungOrder', 'accoungOrderDetail',
            'filterProduct', 'search_result', 'autosearchcomplete',
            'compareList', 'compareProduct', 'removeCompare',
            'buildPc', 'renderProductBuild', 'addProductBuildPc',
            'removeItemBuild', 'plusQtyItemBuild', 'mineQtyItemBuild', 'addBuildToCart',
            'quickview', 'loginGoogle', 'loginFacebook',
            'getSku', 'typeproduct', 'district', 'sendmail',
            'add.to.cart', 'update.cart', 'remove.from.cart', 'languages',
        ];

        if ($routeName && in_array($routeName, $noindexRoutes, true)) {
            return true;
        }

        $path = trim(request()->path(), '/');
        $noindexPrefixes = [
            'crm', 'admin', 'api', 'clear-cache', 'auth',
            'account', 'payos', 'quickview', 'gio-hang', 'thanh-toan',
            'dang-nhap', 'dang-ky', 'dat-hang-thanh-cong',
        ];

        foreach ($noindexPrefixes as $prefix) {
            if ($path === $prefix || strpos($path, $prefix . '/') === 0) {
                return true;
            }
        }

        return false;
    }
}
if (!function_exists('seo_robots_directive')) {
    function seo_robots_directive(): string
    {
        if (seo_is_noindex_route()) {
            return 'noindex, nofollow';
        }

        $page = (int) request()->get('page', 1);
        if ($page > 1) {
            return 'noindex, follow';
        }

        return 'index, follow';
    }
}
if (!function_exists('seo_plain_text')) {
    /** Strip HTML/entities and truncate for meta description / schema text. */
    function seo_plain_text($value, int $max = 160): string
    {
        $text = html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $text) ?? $text;
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        $text = trim($text);
        if ($max > 0 && mb_strlen($text) > $max) {
            $text = rtrim(mb_substr($text, 0, max(1, $max - 1))) . '…';
        }
        return $text;
    }
}
if (!function_exists('seo_paginator_links')) {
    /**
     * Build rel prev/next URLs from a Laravel paginator.
     * @return array{prev:?string,next:?string}
     */
    function seo_paginator_links($paginator): array
    {
        if (!is_object($paginator) || !method_exists($paginator, 'previousPageUrl')) {
            return ['prev' => null, 'next' => null];
        }

        return [
            'prev' => $paginator->previousPageUrl() ?: null,
            'next' => $paginator->nextPageUrl() ?: null,
        ];
    }
}
