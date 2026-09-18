@php
    $lazy = $lazy ?? true;
    $eager = $eager ?? false;
    $loading = ($eager || !$lazy) ? 'eager' : 'lazy';
    $src = $src ?? '';
    $alt = $alt ?? '';
    $class = $class ?? '';
    $width = $width ?? null;
    $height = $height ?? null;

    if ($src && preg_match('#^/frontend/#', $src)) {
        $src = r2_asset(ltrim($src, '/'));
    } elseif ($src && !preg_match('#^https?://#i', $src) && strpos($src, '/') === 0) {
        $src = url($src);
    } elseif ($src && !preg_match('#^https?://#i', $src)) {
        $src = url('/' . ltrim($src, '/'));
    }

    $extraAttrs = '';
    if ($class) {
        $extraAttrs .= ' class="' . e($class) . '"';
    }
    if ($width) {
        $extraAttrs .= ' width="' . (int) $width . '"';
    }
    if ($height) {
        $extraAttrs .= ' height="' . (int) $height . '"';
    }
@endphp
<img src="{{ $src }}" alt="{{ $alt }}" loading="{{ $loading }}" decoding="async"{!! $extraAttrs !!}>
