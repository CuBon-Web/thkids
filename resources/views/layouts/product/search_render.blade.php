@if (count($data) > 0)
@foreach ($data as $item)
@php
    $img = json_decode($item->images, true) ?? [];
    $thumb = $img[0] ?? '';
    if ($thumb !== '' && !preg_match('#^https?://#i', $thumb)) {
        $thumb = url($thumb);
    }
    $productUrl = route('detailProduct', [
        'cate' => $item->cate_slug ?: 'san-pham',
        'type' => $item->type_slug ?: 'loai',
        'id' => $item->slug,
    ]);
    $productName = languageName($item->name);
    $originalPrice = (float) $item->price;
    $salePrice = (float) $item->discount;
    if ((int) $item->status_variant === 1 && isset($item->variant_min_price)) {
        $salePrice = (float) $item->variant_min_price;
    }
    $showCompare = $originalPrice > 0 && $salePrice > 0 && $salePrice < $originalPrice;
@endphp
<a class="product-smart" href="{{ $productUrl }}" title="{{ $productName }}">
    <div class="image_thumb">
        <img width="80" height="80" src="{{ $thumb ?: asset('frontend/images/lazy.png') }}" alt="{{ $productName }}">
    </div>
    <div class="product-info">
        <h3 class="product-name line-clamp line-clamp-1">
            <span>{{ $productName }}</span>
        </h3>
        <div class="price-box">
            @if ($originalPrice > 0 || $salePrice > 0)
                <span class="price">{{ number_format($salePrice > 0 ? $salePrice : $originalPrice) }}₫</span>
                @if ($showCompare)
                <span class="compare-price">{{ number_format($originalPrice) }}₫</span>
                @endif
            @else
                <span class="price">Liên hệ</span>
            @endif
        </div>
    </div>
</a>
@endforeach
<a href="{{ route('search_result', ['keyword' => $keyword]) }}" class="see-all-search" title="Xem tất cả">
    Xem tất cả kết quả »
</a>
@else
<div class="not-pro">Không tìm thấy sản phẩm phù hợp</div>
@endif
