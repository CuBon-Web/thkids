@php
    $compareList = $compare ?? [];
    $compareCount = count($compareList);
    $compareSlugs = collect($compareList)->pluck('pro_slug')->values()->all();
@endphp
<div class="compare-sidebar"
    data-add-url="{{ route('compareProduct') }}"
    data-remove-url="{{ route('removeCompare') }}"
    data-compare-page="{{ route('compareList') }}"
    data-compare-slugs="{{ json_encode($compareSlugs) }}">
    <div class="sidebarAllMainCompare {{ $compareCount ? 'active' : '' }}">
        <div class="container">
            <div class="box_sidebar_compare">
                <div class="sidebarAllHeader">
                    <div class="title">
                        <span class="closeSidebar">
                            Ẩn so sánh
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="#fff">
                                <path d="M12.9996 12.9996L1 1" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M13 1.00024L12.25 1.75022M1.00035 12.9999L7.00018 7.00007L9.50037 4.49988" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="sidebarAllBody">
                    @include('layouts.product.compare-sidebar-items', ['list' => $compareList])
                </div>
                @if ($compareCount > 0)
                <div class="sidebarAllFooter">
                    <a href="{{ route('compareList') }}" class="mainCompareButton">So sánh ngay</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
