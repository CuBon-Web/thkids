@php
    $cateName = languageName($category->name);
    $cateUrl = route('allListProCate', ['danhmuc' => $category->slug]);
    $sectionKey = 'ajax-tab-' . $category->id;
    $products = $category->product ?? collect();
@endphp
@if ($products->isNotEmpty())
<section class="section-index section_product_tab_2">
    <div class="container">
        <div class="wrap_tab_index not-dqtab e-tabs {{ $sectionKey }}" data-section-2="{{ $sectionKey }}">
            <div class="section-title">
                <h2>
                    <a href="{{ $cateUrl }}" title="{{ $cateName }}">{{ $cateName }}</a>
                </h2>
            </div>
            <div class="block-products">
                <div class="tab-content current">
                    <div class="row">
                        @foreach ($products as $pro)
                            @include('layouts.product.home_category_item', ['pro' => $pro])
                        @endforeach
                    </div>
                    <div class="view-more clearfix">
                        <a href="{{ $cateUrl }}" title="Xem tất cả" class="show-more btn btn-extent frame">
                            Xem tất cả
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
