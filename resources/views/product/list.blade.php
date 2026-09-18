@extends('layouts.main.master')
@php
    $listDescription = '';
    if (!empty($content) && $content !== 'none') {
        $listDescription = seo_plain_text(languageName($content), 160);
    }
    if ($listDescription === '') {
        $listDescription = 'Khám phá danh mục ' . $title . ' chất lượng tại ' . ($setting->webname ?? $setting->company ?? 'cửa hàng') . '.';
    }
    $listPager = seo_paginator_links($list ?? null);
@endphp
@section('title')
{{ $title }}
@endsection
@section('description')
{{ $listDescription }}
@endsection
@section('seo_prev')
{{ $listPager['prev'] ?? '' }}
@endsection
@section('seo_next')
{{ $listPager['next'] ?? '' }}
@endsection
@section('image')
@php
    $listOgImage = isset($banner[0]) && !empty($banner[0]->image)
        ? url($banner[0]->image)
        : url($setting->logo ?? '');
@endphp
{{ $listOgImage }}
@endsection
@section('css')
<link rel="preload" as="style" type="text/css" href="{{ asset('frontend/css/shop-list.css') }}" />
<link href="{{ asset('frontend/css/shop-list.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('js')
<script>
    function initProductFilters() {
        const tagCheckboxes = Array.from(document.querySelectorAll('.js-tag-filter'));
        const keywordInput = document.getElementById('js-dom-filter-keyword');
        const clearBtn = document.getElementById('js-dom-filter-clear');
        const resultCount = document.getElementById('js-dom-filter-count');
        const sortSelect = document.getElementById('js-dom-sort-select');
        const minPriceInput = document.getElementById('js-dom-filter-price-min');
        const maxPriceInput = document.getElementById('js-dom-filter-price-max');
        const applyPriceBtn = document.getElementById('js-dom-filter-price-apply');
        const productRow = document.querySelector('.all-products .product-list-row');
        const paginationWrap = document.querySelector('.shop-pagination');
        const csrfToken = document.querySelector('meta[name="csrf-token"]');

        function getFilterState() {
            const selectedTags = tagCheckboxes.filter((checkbox) => checkbox.checked).map((checkbox) => checkbox.value);
            const keyword = (keywordInput ? keywordInput.value : '').trim().toLowerCase();
            const minPrice = minPriceInput && minPriceInput.value !== '' ? Number(minPriceInput.value) : null;
            const maxPrice = maxPriceInput && maxPriceInput.value !== '' ? Number(maxPriceInput.value) : null;
            const sortValue = sortSelect ? sortSelect.value : 'newest';
            return { selectedTags, keyword, minPrice, maxPrice, sortValue };
        }

        function updateVisibleCount(count) {
            const total = Number(count || 0);
            if (resultCount) {
                resultCount.textContent = total;
            }
            const toolbarCount = document.getElementById('js-dom-filter-count-toolbar');
            if (toolbarCount) {
                toolbarCount.textContent = total;
            }
        }

        function applyLocalDomFilter(state) {
            if (!productRow) return;
            const productItems = Array.from(productRow.querySelectorAll('.product-list-col.item'));
            const selectedTags = state.selectedTags || [];
            const keyword = state.keyword || '';
            const minPrice = state.minPrice;
            const maxPrice = state.maxPrice;
            const sortValue = state.sortValue || 'newest';

            productItems.forEach((item) => {
                const itemTags = (item.dataset.tags || '').split(',').map((value) => value.trim()).filter(Boolean);
                const itemName = (item.dataset.name || '').toLowerCase();
                const itemPrice = item.dataset.price ? Number(item.dataset.price) : 0;

                const matchedTag = selectedTags.length === 0 || selectedTags.some((tag) => itemTags.includes(tag));
                const matchedKeyword = keyword === '' || itemName.includes(keyword);
                const matchedMinPrice = minPrice === null || itemPrice >= minPrice;
                const matchedMaxPrice = maxPrice === null || itemPrice <= maxPrice;

                item.style.display = matchedTag && matchedKeyword && matchedMinPrice && matchedMaxPrice ? '' : 'none';
            });

            const sortedItems = [...productItems].sort((a, b) => {
                const aPrice = Number(a.dataset.price || 0);
                const bPrice = Number(b.dataset.price || 0);
                const aId = Number(a.dataset.productId || 0);
                const bId = Number(b.dataset.productId || 0);

                if (sortValue === 'oldest') return aId - bId;
                if (sortValue === 'price_asc') return aPrice - bPrice;
                if (sortValue === 'price_desc') return bPrice - aPrice;
                return bId - aId;
            });
            sortedItems.forEach((item) => productRow.appendChild(item));

            updateVisibleCount(productItems.filter((item) => item.style.display !== 'none').length);
        }

        function applyDomFilter() {
            if (!productRow) return;
            const state = getFilterState();
            applyLocalDomFilter(state);
            const sortMap = {
                newest: 'created-desc',
                oldest: 'created-oldest',
                price_asc: 'price-asc',
                price_desc: 'price-desc'
            };
            const payload = {
                fillter: state.selectedTags,
                sortby: sortMap[state.sortValue] || 'created-desc',
                pricemin: state.minPrice,
                pricemax: state.maxPrice,
                keyword: state.keyword,
                cate: productRow.dataset.cate || '',
                type: productRow.dataset.type || '',
                typetwo: productRow.dataset.typetwo || ''
            };

            fetch('{{ route('filterProduct') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : ''
                },
                body: JSON.stringify(payload)
            })
            .then((response) => response.json())
            .then((response) => {
                productRow.innerHTML = response.items_html || '';
                if (paginationWrap) {
                    paginationWrap.innerHTML = response.pagination_html || '';
                }
                applyLocalDomFilter(state);
                updateVisibleCount(response.total || response.count || 0);
                if (typeof window.awe_lazyloadImage === 'function') {
                    window.awe_lazyloadImage();
                }
            })
            .catch(() => {});
        }

        function syncTagCheckedClass(checkbox) {
            const label = checkbox.closest('.tag-filter-label');
            if (!label) return;
            label.classList.toggle('is-checked', checkbox.checked);
        }

        tagCheckboxes.forEach((checkbox) => {
            syncTagCheckedClass(checkbox);
            checkbox.addEventListener('change', function () {
                syncTagCheckedClass(checkbox);
                applyDomFilter();
            });
        });

        if (keywordInput) {
            keywordInput.addEventListener('input', applyDomFilter);
        }
        if (sortSelect) {
            sortSelect.addEventListener('change', applyDomFilter);
            sortSelect.addEventListener('input', applyDomFilter);
        }
        document.addEventListener('change', function (event) {
            if (event.target && event.target.id === 'js-dom-sort-select') {
                applyDomFilter();
            }
        });
        document.addEventListener('click', function (event) {
            const optionEl = event.target.closest('.selector .nice-select .option');
            if (!optionEl) return;
            setTimeout(applyDomFilter, 0);
        });
        if (applyPriceBtn) {
            applyPriceBtn.addEventListener('click', function (event) {
                event.preventDefault();
                applyDomFilter();
            });
        }
        if (minPriceInput) {
            minPriceInput.addEventListener('keyup', function (event) {
                if (event.key === 'Enter') {
                    applyDomFilter();
                }
            });
        }
        if (maxPriceInput) {
            maxPriceInput.addEventListener('keyup', function (event) {
                if (event.key === 'Enter') {
                    applyDomFilter();
                }
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function (event) {
                event.preventDefault();
                tagCheckboxes.forEach((checkbox) => {
                    checkbox.checked = false;
                    syncTagCheckedClass(checkbox);
                });
                if (keywordInput) keywordInput.value = '';
                if (minPriceInput) minPriceInput.value = '';
                if (maxPriceInput) maxPriceInput.value = '';
                if (sortSelect) sortSelect.value = 'newest';
                applyDomFilter();
            });
        }

        updateVisibleCount(resultCount ? resultCount.textContent : 0);
    }

    function initShopListUi() {
        const filterToggle = document.querySelector('.js-shop-filter-toggle');
        const sidebarClose = document.getElementById('js-shop-sidebar-close');
        const sidebarOverlay = document.getElementById('js-shop-sidebar-overlay');
        const productRow = document.querySelector('.all-products .product-list-row');
        const gridViewItems = document.querySelectorAll('.grid-view li');

        function closeSidebar() {
            document.body.classList.remove('shop-filter-open');
            if (filterToggle) {
                filterToggle.setAttribute('aria-expanded', 'false');
            }
        }

        function openSidebar() {
            document.body.classList.add('shop-filter-open');
            if (filterToggle) {
                filterToggle.setAttribute('aria-expanded', 'true');
            }
        }

        if (filterToggle) {
            filterToggle.addEventListener('click', function () {
                if (document.body.classList.contains('shop-filter-open')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });
        }
        if (sidebarClose) {
            sidebarClose.addEventListener('click', closeSidebar);
        }
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }

        const gridClassMap = {
            2: ['col-lg-6', 'col-md-6', 'col-sm-6', 'col-6'],
            3: ['col-xl-4', 'col-lg-4', 'col-md-4', 'col-sm-6', 'col-6'],
            4: ['col-xl-3', 'col-lg-3', 'col-md-4', 'col-sm-6', 'col-6'],
        };
        const gridRemoveClasses = [
            'col-xl-3', 'col-xl-4', 'col-lg-3', 'col-lg-4', 'col-lg-6',
            'col-md-4', 'col-md-6', 'col-sm-6', 'col-6'
        ];

        function applyGridColumns(cols) {
            if (!productRow) return;
            const classes = gridClassMap[cols] || gridClassMap[4];
            productRow.querySelectorAll('.product-list-col.item').forEach((item) => {
                item.classList.remove(...gridRemoveClasses);
                classes.forEach((className) => item.classList.add(className));
            });
        }

        gridViewItems.forEach((item) => {
            item.addEventListener('click', function () {
                gridViewItems.forEach((el) => el.classList.remove('active'));
                item.classList.add('active');
                const cols = Number(item.dataset.cols || 4);
                applyGridColumns(cols);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initProductFilters();
            initShopListUi();
        });
    } else {
        initProductFilters();
        initShopListUi();
    }
</script>
@endsection
@section('content')
@php
    $hasSeoContent = !empty($content) && $content !== 'none';
@endphp
<div class="bodywrap shop-list-page">
   <section class="bread-crumb">
      <div class="container">
         <ul class="breadcrumb">
            <li class="home">
               <a href="{{ route('home') }}"><span>Trang chủ</span></a>
               <span class="mr_lr">
                  <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="8" height="8"><path fill="currentColor" d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"></path></svg>
               </span>
            </li>
            @if (!empty($cate_name ?? ''))
            <li>
               <a href="{{ route('allListProCate', ['danhmuc' => $cate_slug ?? '']) }}"><span>{{ $cate_name }}</span></a>
               <span class="mr_lr">
                  <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="8" height="8"><path fill="currentColor" d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"></path></svg>
               </span>
            </li>
            @endif
            <li><strong><span>{{ $title }}</span></strong></li>
         </ul>
      </div>
   </section>

   <section class="page shop-list-section">
      <div class="container">
         <div class="wrap_background_aside padding-top-15 margin-bottom-40 pg_page">
            <div class="row">
               <div class="col-12">
                  <div class="page-title category-title">
                     <h1 class="title-head">{{ $title }}</h1>
                  </div>
               </div>
            </div>

            <div class="row shop-list-layout">
               <div class="col-lg-3 col-md-12 shop-sidebar-col">
                  <aside class="shop-sidebar" id="shop-filter-sidebar">
                     <div class="shop-sidebar-widget">
                        <h2 class="widget-title">Danh mục</h2>
                        <ul class="shop-cate-list">
                           <li class="{{ empty($cate_slug ?? '') ? 'active' : '' }}">
                              <a href="{{ route('allProduct') }}">Tất cả sản phẩm</a>
                           </li>
                           @foreach ($categoryhome as $cateItem)
                           <li class="{{ ($cate_slug ?? '') === $cateItem->slug ? 'active' : '' }}">
                              <a href="{{ route('allListProCate', ['danhmuc' => $cateItem->slug]) }}">{{ languageName($cateItem->name) }}</a>
                           </li>
                           @endforeach
                        </ul>
                     </div>

                     <div class="shop-sidebar-widget">
                        <h2 class="widget-title">Khoảng giá</h2>
                        <div class="price-filter-row">
                           <input id="js-dom-filter-price-min" type="number" min="0" class="form-control" placeholder="Giá từ">
                           <input id="js-dom-filter-price-max" type="number" min="0" class="form-control" placeholder="Giá đến">
                        </div>
                        <button type="button" id="js-dom-filter-price-apply" class="shop-filter-btn">Áp dụng giá</button>
                     </div>

                     <div class="shop-sidebar-widget">
                        <h2 class="widget-title">Lọc theo tag</h2>
                        <div class="dom-filter-head">
                           <span>Đang hiển thị: <b id="js-dom-filter-count">{{ $list->total() }}</b></span>
                           <a href="#" id="js-dom-filter-clear">Xóa lọc</a>
                        </div>
                        @if (!empty($filter) && count($filter) > 0)
                           @foreach ($filter as $tagCate)
                              @if (!empty($tagCate->tags) && count($tagCate->tags) > 0)
                              <div class="mb-3">
                                 <p class="tag-filter-group-title">{{ $tagCate->name }}</p>
                                 <div class="tag-filter-list">
                                    @foreach ($tagCate->tags as $tag)
                                    @php $tagValue = $tag->slug . '-' . $tagCate->slug; @endphp
                                    <div class="tag-filter-item">
                                       <label class="tag-filter-label" for="tag-filter-{{ $tagCate->id }}-{{ $tag->id }}">
                                          <input
                                             type="checkbox"
                                             class="js-tag-filter"
                                             id="tag-filter-{{ $tagCate->id }}-{{ $tag->id }}"
                                             value="{{ $tagValue }}"
                                          >
                                          <span class="tag-filter-check" aria-hidden="true"></span>
                                          <span class="tag-filter-text">{{ $tag->name }}</span>
                                       </label>
                                    </div>
                                    @endforeach
                                 </div>
                              </div>
                              @endif
                           @endforeach
                        @else
                           <p class="shop-filter-empty">Chưa có dữ liệu tag để lọc.</p>
                        @endif
                     </div>
                  </aside>
                  <button type="button" class="shop-sidebar-close" id="js-shop-sidebar-close" aria-label="Đóng bộ lọc">&times;</button>
               </div>

               <div class="col-lg-9 col-md-12 shop-list-main">
                  <div class="shop-toolbar">
                     <p class="shop-toolbar-count">Hiển thị <b id="js-dom-filter-count-toolbar">{{ $list->total() }}</b> sản phẩm</p>
                     <div class="shop-toolbar-actions">
                        <button type="button" class="shop-filter-toggle js-shop-filter-toggle" aria-expanded="false" aria-controls="shop-filter-sidebar">
                           <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M2 4h12v1.5H2V4zm0 3.25h12v1.5H2v-1.5zm0 3.25h12V12H2v-1.5z"/></svg>
                           <span>Bộ lọc</span>
                        </button>
                        <select id="js-dom-sort-select" class="shop-sort-select" aria-label="Sắp xếp sản phẩm">
                           <option value="newest">Mới nhất</option>
                           <option value="oldest">Cũ nhất</option>
                           <option value="price_asc">Giá thấp đến cao</option>
                           <option value="price_desc">Giá cao đến thấp</option>
                        </select>
                        <ul class="grid-view" aria-label="Chế độ hiển thị lưới">
                           <li data-cols="2" title="2 cột">
                              <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><rect x="1" y="1" width="6" height="14" rx="1"/><rect x="9" y="1" width="6" height="14" rx="1"/></svg>
                           </li>
                           <li data-cols="3" title="3 cột">
                              <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><rect x="1" y="1" width="4" height="14" rx="1"/><rect x="6" y="1" width="4" height="14" rx="1"/><rect x="11" y="1" width="4" height="14" rx="1"/></svg>
                           </li>
                           <li class="active" data-cols="4" title="4 cột">
                              <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><rect x="1" y="1" width="3" height="6" rx="1"/><rect x="5.5" y="1" width="3" height="6" rx="1"/><rect x="10" y="1" width="3" height="6" rx="1"/><rect x="1" y="9" width="3" height="6" rx="1"/><rect x="5.5" y="9" width="3" height="6" rx="1"/><rect x="10" y="9" width="3" height="6" rx="1"/></svg>
                           </li>
                        </ul>
                     </div>
                  </div>

                  <div class="all-products">
                     <div
                        class="row row-fix product-list-row"
                        data-cate="{{ $cate_slug ?? '' }}"
                        data-type="{{ $type_slug ?? '' }}"
                        data-typetwo="{{ $type_two_slug ?? '' }}"
                     >
                        @include('layouts.product.filter_grid_items', ['product' => $list])
                     </div>
                  </div>

                  <nav class="shop-pagination" aria-label="Phân trang sản phẩm">
                     {{ $list->links() }}
                  </nav>
               </div>
            </div>

            @if ($hasSeoContent)
            <div class="row">
               <div class="col-12">
                  <div class="shop-list-seo">{!! $content !!}</div>
               </div>
            </div>
            @endif
         </div>
      </div>
   </section>

   <div class="shop-sidebar-overlay" id="js-shop-sidebar-overlay"></div>
</div>
@endsection
