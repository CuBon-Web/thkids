@php
    $compareItems = collect($list ?? []);
    $maxCompare = 3;
@endphp
@foreach ($compareItems as $item)
    @php
        $productUrl = route('detailProduct', [
            'cate' => $item['cate_slug'],
            'type' => $item['type_slug'] ? $item['type_slug'] : 'loai',
            'id' => $item['pro_slug'],
        ]);
        $productName = languageName($item['name']);
        $originalPrice = (float) ($item['price'] ?? 0);
        $salePrice = (float) ($item['discount'] ?? 0);
    @endphp
    <div class="item-compare itemMainCompare" data-compare="{{ $item['pro_slug'] }}" data-id="{{ $item['idpro'] }}">
        <div class="item-compare-wrap">
            <a class="image_thumb" href="{{ $productUrl }}" title="{{ $productName }}">
                <img width="480" height="480" class="lazyload image1"
                     src="{{ url($item['image']) }}"
                     data-src="{{ url($item['image']) }}"
                     alt="{{ $productName }}">
            </a>
            <div class="product-info">
                <h3 class="product-name">
                    <a href="{{ $productUrl }}" title="{{ $productName }}">{{ $productName }}</a>
                </h3>
                <div class="price-box">
                    @if ($salePrice > 0)
                        <span class="price">{{ number_format($salePrice) }}₫</span>
                        @if ($originalPrice > $salePrice)
                            <span class="compare-price">{{ number_format($originalPrice) }}₫</span>
                        @endif
                    @elseif ($originalPrice > 0)
                        <span class="price">{{ number_format($originalPrice) }}₫</span>
                    @else
                        <span class="price">Liên hệ</span>
                    @endif
                </div>
                <div class="removeItem" data-id="{{ $item['idpro'] }}" data-compare="{{ $item['pro_slug'] }}">Xóa</div>
            </div>
        </div>
    </div>
@endforeach
@for ($i = $compareItems->count(); $i < $maxCompare; $i++)
    <div class="item-compare itemMainCompareNone">
        <div class="item-compare-wrap">
            <i class="icImageCompareNew"></i>
            <p>Thêm sản phẩm</p>
        </div>
    </div>
@endfor
