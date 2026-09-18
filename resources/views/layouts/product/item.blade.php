@php
    $img = json_decode($pro->images, true) ?? [];
    $productUrl = route('detailProduct', [
        'cate' => $pro->cate_slug,
        'type' => $pro->type_slug ? $pro->type_slug : 'loai',
        'id' => $pro->slug,
    ]);

    $productName = languageName($pro->name);
    $cateName = $pro->cate ? languageName($pro->cate->name) : '';
    $thumb = $img[0] ?? '';
    $lazySrc = asset('frontend/images/lazy.png');

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

    $hasVariant = (int) $pro->status_variant === 1;
@endphp
<div class="item_product_main">
    <div class="product-thumbnail">
       <a class="image_thumb scale_hover" href="{{ $productUrl }}"
          title="{{ $productName }}">
       <img width="480" height="480" class="lazyload image1"
          src="{{ $lazySrc }}"
          data-src="{{ url($thumb) }}"
          alt="{{ $productName }}">
       </a>
       @if ($discountPercent > 0)
       <div class="smart"><span>-{{ $discountPercent }}%</span></div>
       @endif
       {{-- <a href="javascript:void(0)" class="setWishlist"
          data-wish="{{ $pro->slug }}" tabindex="0"
          title="Thêm vào yêu thích">
          <svg width="24" height="24"
             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
             <path fill="#000000"
                d="M31.91 61.67L29.62 60c-9.4-6.7-16.72-13.49-21.74-20.17C3.11 33.5.48 27.39.06 21.7A17.63 17.63 0 0 1 5.45 7.16a17 17 0 0 1 11.86-4.81c3.46 0 7.93.39 11.8 3.4A19.09 19.09 0 0 1 32 8.41a19.91 19.91 0 0 1 2.91-2.67c3.89-3 8.37-3.41 11.84-3.41a16.86 16.86 0 0 1 11.85 4.8 17.51 17.51 0 0 1 5.33 14.53c-.44 5.7-3.1 11.81-7.9 18.14C51 46.5 43.63 53.3 34.21 60zM8.51 10.38a13.31 13.31 0 0 0-4 11c.35 4.83 2.69 10.15 6.94 15.79 4.7 6.24 11.59 12.65 20.48 19 8.92-6.39 15.84-12.81 20.58-19.08 4.28-5.65 6.64-11 7-15.8a13.25 13.25 0 0 0-4-11 12.53 12.53 0 0 0-8.76-3.57c-2.76 0-6.29.29-9.11 2.48a12.37 12.37 0 0 0-3.09 3.15v.07L32 16l-2.5-3.56a12.68 12.68 0 0 0-3.11-3.2c-2.8-2.17-6.32-2.46-9.07-2.46a12.58 12.58 0 0 0-8.8 3.59z">
             </path>
          </svg>
       </a> --}}
       <form action="/cart/add" method="post"
          class="variants product-action" data-cart-form
          data-id="product-actions-{{ $pro->id }}" enctype="multipart/form-data">
          @if ($hasVariant)
          <button class="btn-cart btn-views" title="Tùy chọn" type="button"
             onclick="window.location.href='{{ $productUrl }}'">
          Tùy chọn
          </button>
          @else
          <button class="btn-cart btn-views add_to_cart" title="Thêm vào giỏ" type="button">
          Thêm vào giỏ
          </button>
          @endif
       </form>
       {{-- @if (!empty($pro->discountStatus) || !empty($pro->home_status))
       <div class="badge">
          @if (!empty($pro->home_status))
          <span class="new">Hàng mới</span>
          @endif
          @if (!empty($pro->discountStatus))
          <span class="best">Bán chạy</span>
          @endif
       </div>
       @endif --}}
    </div>
    <div class="product-info">
       <h3 class="product-name text-gold"><a class="line-clamp"
          href="{{ $productUrl }}"
          title="{{ $productName }}">{{ $productName }}</a></h3>
       <div class="price-box">
          @if (!is_null($variantMinPrice) && !is_null($variantMaxPrice))
             @if ($variantMinPrice > 0 && $variantMaxPrice > 0 && $variantMinPrice != $variantMaxPrice)
                {{ number_format($variantMinPrice) }}₫ - {{ number_format($variantMaxPrice) }}₫
             @elseif ($variantMinPrice > 0)
                {{ number_format($variantMinPrice) }}₫
             @elseif ($variantMaxPrice > 0)
                {{ number_format($variantMaxPrice) }}₫
             @endif
             @if ($originalPrice > 0)
             <span class="compare-price">{{ number_format($originalPrice) }}₫</span>
             @endif
          @elseif ($salePrice > 0)
             {{ number_format($salePrice) }}₫
             @if ($originalPrice > 0 && $salePrice < $originalPrice)
             <span class="compare-price">{{ number_format($originalPrice) }}₫</span>
             @endif
          @elseif ($originalPrice > 0)
             {{ number_format($originalPrice) }}₫
          @else
             Liên hệ
          @endif
       </div>
       {{-- <div class="star_compar">
          <a href="javascript:void(0)"
             class="btn-views js-compare-product-add"
             data-compare="{{ $pro->slug }}"
             data-product-id="{{ $pro->id }}"
             data-type="{{ $cateName ?: 'default-type' }}" tabindex="0" title="So sánh">
          <i></i>
          </a>
       </div> --}}
    </div>
 </div>