@php
    $img = json_decode($pro->images, true) ?? [];
    $productUrl = route('detailProduct', [
        'cate' => $pro->cate_slug,
        'type' => $pro->type_slug ? $pro->type_slug : 'loai',
        'id' => $pro->slug,
    ]);

    $originalPrice = (float) $pro->price;
    $salePrice = (float) $pro->discount;
    $variantMinPrice = isset($pro->variant_min_price) ? (float) $pro->variant_min_price : null;
    $variantMaxPrice = isset($pro->variant_max_price) ? (float) $pro->variant_max_price : null;
    if (!is_null($variantMinPrice)) {
        $salePrice = $variantMinPrice;
    }

    $discountPercent = 0;
    if ($originalPrice > 0 && $salePrice > 0 && $salePrice < $originalPrice) {
        $discountPercent = 100 - ceil(($salePrice / $originalPrice) * 100);
    }
@endphp

<article class="home-product-card">
    <a href="{{ $productUrl }}" class="home-product-card__media" title="{{ languageName($pro->name) }}">
        @if ($discountPercent > 0)
            <span class="home-product-card__badge">-{{ $discountPercent }}%</span>
        @endif
        <img src="{{ $img[0] ?? '' }}" alt="{{ languageName($pro->name) }}" loading="lazy" decoding="async">
    </a>
    <div class="home-product-card__body">
        @if ($pro->cate)
            <a href="{{ route('allListProCate', ['danhmuc' => $pro->cate->slug]) }}" class="home-product-card__cate">
                {{ languageName($pro->cate->name) }}
            </a>
        @endif
        <h3 class="home-product-card__title">
            <a href="{{ $productUrl }}">{{ languageName($pro->name) }}</a>
        </h3>
        <div class="home-product-card__price">
            @if (!is_null($variantMinPrice) && !is_null($variantMaxPrice))
                @if ($variantMinPrice > 0 && $variantMaxPrice > 0)
                    <span class="price-current">{{ number_format($variantMinPrice) }}₫ – {{ number_format($variantMaxPrice) }}₫</span>
                @elseif ($variantMinPrice > 0)
                    <span class="price-current">{{ number_format($variantMinPrice) }}₫</span>
                @elseif ($variantMaxPrice > 0)
                    <span class="price-current">{{ number_format($variantMaxPrice) }}₫</span>
                @endif
                @if ($originalPrice > 0)
                    <del class="price-old">{{ number_format($originalPrice) }}₫</del>
                @endif
            @elseif ($salePrice > 0 || $originalPrice > 0)
                @if ($salePrice > 0)
                    <span class="price-current">{{ number_format($salePrice) }}₫</span>
                @endif
                @if ($originalPrice > 0 && ($salePrice <= 0 || $salePrice < $originalPrice))
                    <del class="price-old">{{ number_format($originalPrice) }}₫</del>
                @endif
            @else
                <span class="price-contact">Contact</span>
            @endif
        </div>
        <a href="{{ $productUrl }}" class="home-product-card__link">
            View details <i class="fa-light fa-arrow-right"></i>
        </a>
    </div>
</article>
