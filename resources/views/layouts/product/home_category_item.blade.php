@php
    $img = json_decode($pro->images, true) ?? [];
    $productName = languageName($pro->name);
    $productUrl = route('detailProduct', [
        'cate' => $pro->cate_slug ?: 'san-pham',
        'type' => $pro->type_slug ?: 'loai',
        'id' => $pro->slug,
    ]);
    $thumb = $img[0] ?? '';
    if ($thumb !== '' && !preg_match('~^https?://~i', $thumb)) {
        $thumb = url($thumb);
    }
    $lazySrc = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC';

    $originalPrice = (float) $pro->price;
    $salePrice = (float) $pro->discount;
    $variantMinPrice = isset($pro->variant_min_price) ? (float) $pro->variant_min_price : null;
    $variantMaxPrice = isset($pro->variant_max_price) ? (float) $pro->variant_max_price : null;
    if (!is_null($variantMinPrice) && $variantMinPrice > 0) {
        $salePrice = $variantMinPrice;
    }

    $discountPercent = 0;
    if ($originalPrice > 0 && $salePrice > 0 && $salePrice < $originalPrice) {
        $discountPercent = (int) (100 - ceil(($salePrice / $originalPrice) * 100));
    }

    $hasVariant = (int) $pro->status_variant === 1;
@endphp
<div class="col-xl-20 col-md-3 col-sm-6 col-xs-12">
    <div class="item_product_main">
        <form action="/cart/add" method="post"
            class="variants product-action item-product-main product-flash-sale duration-300"
            data-cart-form data-id="product-actions-{{ $pro->id }}"
            enctype="multipart/form-data">
            <div class="product-thumbnail">
                <a class="image_thumb scale_hover" href="{{ $productUrl }}" title="{{ $productName }}">
                    <img class="lazyload duration-300"
                        src="{{ $lazySrc }}"
                        data-src="{{ $thumb }}"
                        alt="{{ $productName }}">
                </a>
                <div class="product-button">
                    @if ($hasVariant)
                        <button class="btn-cart btn-views quick-view-option btn btn-primary quick-view"
                            title="Xem nhanh" type="button"
                            data-handle="{{ $pro->slug }}"
                            data-product-id="{{ $pro->id }}"
                            data-quickview-url="{{ route('quickview', ['id' => $pro->id]) }}">
                            <svg fill="currentColor" height="22" width="22" version="1.1"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 224.549 224.549"
                                xml:space="preserve">
                                <g>
                                    <path
                                        d="M223.476,108.41c-1.779-2.96-44.35-72.503-111.202-72.503S2.851,105.45,1.072,108.41c-1.43,2.378-1.43,5.351,0,7.729
                          c1.779,2.96,44.35,72.503,111.202,72.503s109.423-69.543,111.202-72.503C224.906,113.761,224.906,110.788,223.476,108.41z
                          M112.274,173.642c-49.925,0-86.176-47.359-95.808-61.374c9.614-14.032,45.761-61.36,95.808-61.36
                          c49.925,0,86.176,47.359,95.808,61.374C198.468,126.313,162.321,173.642,112.274,173.642z">
                                    </path>
                                    <path
                                        d="M112.274,61.731c-27.869,0-50.542,22.674-50.542,50.543c0,27.868,22.673,50.54,50.542,50.54
                          c27.868,0,50.541-22.672,50.541-50.54C162.815,84.405,140.143,61.731,112.274,61.731z M112.274,147.814
                          c-19.598,0-35.542-15.943-35.542-35.54c0-19.599,15.944-35.543,35.542-35.543s35.541,15.944,35.541,35.543
                          C147.815,131.871,131.872,147.814,112.274,147.814z"></path>
                                    <path
                                        d="M112.274,92.91c-10.702,0-19.372,8.669-19.372,19.364c0,10.694,8.67,19.363,19.372,19.363
                          c10.703,0,19.373-8.669,19.373-19.363C131.647,101.579,122.977,92.91,112.274,92.91z">
                                    </path>
                                </g>
                            </svg>
                        </button>
                    @else
                        <button class="btn-cart btn-views add_to_cart btn btn-primary" title="Thêm vào giỏ hàng"
                            type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                                <path
                                    d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z">
                                </path>
                            </svg>
                        </button>
                    @endif
                    <a href="javascript:void(0)" class="setWishlist btn-views btn-circle"
                        data-wish="{{ $pro->slug }}" tabindex="0" title="Thêm vào yêu thích">
                        <img width="25" height="25" src="{{ asset('frontend/images/heart.png') }}"
                            alt="Thêm vào yêu thích" />
                    </a>
                </div>
            </div>
            <div class="product-info">
                <h3 class="product-name line-clamp-2-new">
                    <a href="{{ $productUrl }}" title="{{ $productName }}">{{ $productName }}</a>
                </h3>
                <div class="product-price-cart">
                    @if (!is_null($variantMinPrice) && !is_null($variantMaxPrice) && $variantMinPrice > 0 && $variantMaxPrice > 0 && $variantMinPrice != $variantMaxPrice)
                        <span class="price">
                            {{ number_format($variantMinPrice) }}₫ - {{ number_format($variantMaxPrice) }}₫
                            @if ($discountPercent > 0)
                                <span class="flash-sale">- {{ $discountPercent }}%</span>
                            @endif
                        </span>
                        @if ($originalPrice > 0)
                            <span class="compare-price">{{ number_format($originalPrice) }}₫</span>
                        @endif
                    @elseif ($salePrice > 0)
                        <span class="price">
                            {{ number_format($salePrice) }}₫
                            @if ($discountPercent > 0)
                                <span class="flash-sale">- {{ $discountPercent }}%</span>
                            @endif
                        </span>
                        @if ($originalPrice > 0 && $salePrice < $originalPrice)
                            <span class="compare-price">{{ number_format($originalPrice) }}₫</span>
                        @endif
                    @elseif ($originalPrice > 0)
                        <span class="price">{{ number_format($originalPrice) }}₫</span>
                    @else
                        <span class="price">Liên hệ</span>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
