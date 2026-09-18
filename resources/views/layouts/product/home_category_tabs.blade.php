@php
    $cateName = languageName($category->name);
    $cateUrl = route('allListProCate', ['danhmuc' => $category->slug]);
    $sectionId = 'ajax-tab-' . $category->id;
    $hasTypes = $category->typeCate->count() > 0;
@endphp
<section class="section_product_noibat{{ $hasTypes ? ' e-tabs not-dqtab ' . $sectionId : '' }}"
    @if ($hasTypes) data-section="{{ $sectionId }}" data-view="grid_4" @endif>
    <div class="container">
        <div class="title_index">
            <h2 class="title-module-2">
                <a href="{{ $cateUrl }}" title="{{ $cateName }}">
                    {{ $cateName }}
                    <span class="icon_title">
                        <svg height="512" viewBox="0 0 24 24" width="512" xmlns="http://www.w3.org/2000/svg">
                            <g id="_19" data-name="19">
                                <path
                                    d="m12 19a1 1 0 0 1 -.71-1.71l5.3-5.29-5.3-5.29a1 1 0 0 1 1.41-1.41l6 6a1 1 0 0 1 0 1.41l-6 6a1 1 0 0 1 -.7.29z" />
                                <path
                                    d="m6 19a1 1 0 0 1 -.71-1.71l5.3-5.29-5.3-5.29a1 1 0 0 1 1.42-1.42l6 6a1 1 0 0 1 0 1.41l-6 6a1 1 0 0 1 -.71.3z" />
                            </g>
                        </svg>
                    </span>
                </a>
            </h2>
            @if ($hasTypes)
            <ul class="tabs tabs-title tab-desktop ajax clearfix">
                @foreach ($category->typeCate as $type)
                @php
                    $typeName = languageName($type->name);
                    $tabId = 'tab-' . $category->id . '-' . $type->id;
                    $tabUrl = $category->slug . '/' . $type->slug . '.html';
                @endphp
                <li class="tab-link{{ $loop->first ? ' has-content' : '' }}" data-tab="{{ $tabId }}"
                    data-url="{{ $tabUrl }}">
                    <span class="line-clamp line-clamp-1" title="{{ $typeName }}">{{ $typeName }}</span>
                </li>
                @endforeach
            </ul>
            @endif
        </div>

        @if ($hasTypes)
            @foreach ($category->typeCate as $type)
            @php
                $tabId = 'tab-' . $category->id . '-' . $type->id;
                $typeUrl = route('allListType', ['danhmuc' => $category->slug, 'loaidanhmuc' => $type->slug]);
            @endphp
            <div class="{{ $tabId }} tab-content">
                @if ($loop->first)
                <div class="row row-fix">
                    @forelse ($type->product as $pro)
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-6 col-fix">
                        @include('layouts.product.item', ['pro' => $pro])
                    </div>
                    @empty
                    <div class="col-12">
                        <p class="text-center mb-0">Chưa có sản phẩm.</p>
                    </div>
                    @endforelse
                </div>
                @endif
                <div class="see-more">
                    <a title="Xem tất cả" href="{{ $typeUrl }}">Xem tất cả</a>
                </div>
            </div>
            @endforeach
        @else
            <div class="tab-content current">
                <div class="row row-fix">
                    @forelse ($category->product as $pro)
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-6 col-fix">
                        @include('layouts.product.item', ['pro' => $pro])
                    </div>
                    @empty
                    <div class="col-12">
                        <p class="text-center mb-0">Chưa có sản phẩm.</p>
                    </div>
                    @endforelse
                </div>
                <div class="see-more">
                    <a title="Xem tất cả" href="{{ $cateUrl }}">Xem tất cả</a>
                </div>
            </div>
        @endif
    </div>
</section>
