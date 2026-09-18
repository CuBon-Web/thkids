<div class="opacity_menu"></div>
<header class="header">

    <div class="topbar">
        <div class="container header-promo">

            <ul class="ul-default promo-list js-promo">
                <li class="promo-item  see-block">
                    <p>Nhận MIỄN PHÍ* <strong><a href="" title="Giao hàng">giao hàng</a></strong> cho tất cả đơn
                        hàng trên 1 triệu - áp dụng có hạn.</p>
                </li>
                <li class="promo-item  see-none">
                    <p>Nhận ƯU ĐÃI* <strong><a href="" title="đặc biệt">đặc biệt</a></strong> từ các chương trình
                        khuyến mãi mới nhất</p>
                </li>
            </ul>

        </div>
    </div>

    <div class="main-header">
        <div class="container">
            <div class="box-hearder">
                <div class="row align-items-center">
                    <div class="col-6 col-lg-3 col-md-4 header-logo">

                        <a href="{{ route('home') }}" class="logo-wrapper" title="TH KIDS">
                            <img width="494" height="175"
                                src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"
                                data-src="{{ $setting->logo }}" alt="TH KIDS" class="lazyload">
                        </a>

                        <a href="javascript:void(0)" title="Danh mục" class="js-menu-toggle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                class="bi bi-list" viewBox="0 0 24 24">
                                <path
                                    d="M7 10.75H5c-2.42 0-3.75-1.33-3.75-3.75V5c0-2.42 1.33-3.75 3.75-3.75h2c2.42 0 3.75 1.33 3.75 3.75v2c0 2.42-1.33 3.75-3.75 3.75zm-2-8c-1.58 0-2.25.67-2.25 2.25v2c0 1.58.67 2.25 2.25 2.25h2c1.58 0 2.25-.67 2.25-2.25V5c0-1.58-.67-2.25-2.25-2.25zM19 10.75h-2c-2.42 0-3.75-1.33-3.75-3.75V5c0-2.42 1.33-3.75 3.75-3.75h2c2.42 0 3.75 1.33 3.75 3.75v2c0 2.42-1.33 3.75-3.75 3.75zm-2-8c-1.58 0-2.25.67-2.25 2.25v2c0 1.58.67 2.25 2.25 2.25h2c1.58 0 2.25-.67 2.25-2.25V5c0-1.58-.67-2.25-2.25-2.25zM19 22.75h-2c-2.42 0-3.75-1.33-3.75-3.75v-2c0-2.42 1.33-3.75 3.75-3.75h2c2.42 0 3.75 1.33 3.75 3.75v2c0 2.42-1.33 3.75-3.75 3.75zm-2-8c-1.58 0-2.25.67-2.25 2.25v2c0 1.58.67 2.25 2.25 2.25h2c1.58 0 2.25-.67 2.25-2.25v-2c0-1.58-.67-2.25-2.25-2.25zM7 22.75H5c-2.42 0-3.75-1.33-3.75-3.75v-2c0-2.42 1.33-3.75 3.75-3.75h2c2.42 0 3.75 1.33 3.75 3.75v2c0 2.42-1.33 3.75-3.75 3.75zm-2-8c-1.58 0-2.25.67-2.25 2.25v2c0 1.58.67 2.25 2.25 2.25h2c1.58 0 2.25-.67 2.25-2.25v-2c0-1.58-.67-2.25-2.25-2.25z">
                                </path>
                            </svg>
                        </a>
                    </div>
                    <div class="col-12 col-md-12 col-lg-6 header-mid">
                        <div class="list-top-item header_tim_kiem">
                            <form action="{{ route('search_result') }}" method="get"
                                class="header-search-form input-group search-bar" role="search">
                                <input name="keyword" required
                                    class="input-group-field auto-search search-auto form-control"
                                    placeholder="Tìm sản phẩm..." autocomplete="off" type="text"
                                    value="{{ request('keyword') }}">
                                <button type="submit" class="btn icon-fallback-text" aria-label="Tìm kiếm"
                                    title="Tìm kiếm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                        <path
                                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                    </svg>
                                </button>

                                <div class="search-suggest">

                                    <div class="search-recent d-none">
                                        <div class="search-title">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                                                <path
                                                    d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z" />
                                                <path
                                                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z" />
                                            </svg>
                                            Tìm kiếm gần đây
                                        </div>
                                        <div class="search-list">
                                        </div>
                                    </div>


                                    <div class="list-search">
                                    </div>

                                </div>

                            </form>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-8 header-right">
                        <div class="sudes-header-stores sm-hidden">
                            <a href="" title="Hệ thống">
                                <span class="box-icon">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M10 0C6.12297 0 2.96875 3.15422 2.96875 7.03125C2.96875 8.34117 3.3316 9.61953 4.01832 10.7286L9.59977 19.723C9.70668 19.8953 9.89504 20 10.0976 20C10.0992 20 10.1007 20 10.1023 20C10.3066 19.9984 10.4954 19.8905 10.6003 19.7152L16.0395 10.6336C16.6883 9.54797 17.0312 8.3023 17.0312 7.03125C17.0312 3.15422 13.877 0 10 0ZM15.0338 10.032L10.0888 18.2885L5.01434 10.1112C4.44273 9.18805 4.13281 8.12305 4.13281 7.03125C4.13281 3.80039 6.76914 1.16406 10 1.16406C13.2309 1.16406 15.8633 3.80039 15.8633 7.03125C15.8633 8.09066 15.5738 9.12844 15.0338 10.032Z"
                                            fill="white" />
                                        <path
                                            d="M10 3.51562C8.06148 3.51562 6.48438 5.09273 6.48438 7.03125C6.48438 8.95738 8.03582 10.5469 10 10.5469C11.9884 10.5469 13.5156 8.93621 13.5156 7.03125C13.5156 5.09273 11.9385 3.51562 10 3.51562ZM10 9.38281C8.7009 9.38281 7.64844 8.32684 7.64844 7.03125C7.64844 5.73891 8.70766 4.67969 10 4.67969C11.2923 4.67969 12.3477 5.73891 12.3477 7.03125C12.3477 8.30793 11.3197 9.38281 10 9.38281Z"
                                            fill="white" />
                                    </svg>
                                </span>
                                <span class="item-title sm-hidden">Hệ thống</span>
                            </a>
                        </div>
                        <div class="sudes-header-cart header-action_cart">
                            @php
                                $headerCartCount = (int) collect($cartcontent ?? [])->sum('quantity');
                            @endphp
                            <a class="a-hea" href="{{ route('listCart') }}" aria-label="Giỏ hàng" title="Giỏ hàng">
                                <span class="box-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30"
                                        fill="currentColor" class="bi bi-cart3" viewBox="0 0 16 16">
                                        <path
                                            d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z">
                                        </path>
                                    </svg>
                                    <span class="count_item count_item_pr" id="header-cart-count">{{ $headerCartCount }}</span>
                                </span>
                                <span class="item-title sm-hidden">Giỏ hàng</span>
                            </a>
                            <div class="top-cart-content">
                                <div class="CartHeaderContainer">
                                </div>
                            </div>
                        </div>
                        <button class="menu-icon md-hidden" aria-label="Menu" id="btn-menu-mobile" title="Menu">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="header-menu">
        <div class="container">
            <div class="navigation-horizontal">
                <div class="title_menu md-hidden">
                    <ul id="tabs-menu-mb">
                        <li class="tab-link" data-tab="tab-menu-1">Menu</li>
                        <li class="tab-link" data-tab="tab-menu-2">Danh mục</li>
                    </ul>
                    <div class="close-mb-menu"></div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-sm-12 col-xs-12 col-12 sudes-main-header tab-content-mb"
                        id="tab-menu-1">
                        <div class="col-menu">
                            <ul id="nav" class="nav">




                                <li class="nav-item active">
                                    <a class="nav-link" href="{{ route('home') }}" title="Trang  chủ">Trang chủ</a>
                                </li>




                                <li class="nav-item ">
                                    <a class="nav-link" href="{{ route('aboutUs') }}" title="Giới thiệu">Giới
                                        thiệu</a>
                                </li>




                                <li class="nav-item has-childs   has-mega has-mega-1 hover-left"
                                    data-section="header_nav">
                                    <a href="{{ route('allProduct') }}" class="nav-link" title="Sản phẩm">Sản phẩm
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708" />
                                        </svg>
                                    </a>
                                    <i class="open_mnu down_icon"></i>

                                    <div class="mega-content dropdown-menu">
                                        <div class="row d-flex justify-content-center">
                                            <div class="mega-menu-list col-lg-7 mega-sp">
                                                <ul class="level0">
                                                    @foreach ($categoryhome ?? collect() as $category)
                                                        @php
                                                            $cateName = languageName($category->name);
                                                            $cateUrl = route('allListProCate', [
                                                                'danhmuc' => $category->slug,
                                                            ]);
                                                            $types = $category->typeCate ?? collect();
                                                            $hasTypes = $types->isNotEmpty();
                                                        @endphp
                                                        <li
                                                            class="level1 parent item fix-navs nav-item-lv2{{ $hasTypes ? ' has-childs2' : '' }}">
                                                            <a class="hmega" href="{{ $cateUrl }}"
                                                                title="{{ $cateName }}">{{ $cateName }}</a>
                                                            @if ($hasTypes)
                                                                <i class="open_mnu down_icon"></i>
                                                                <ul class="level1 dropdown-menu">
                                                                    @foreach ($types as $type)
                                                                        @php
                                                                            $typeName = languageName($type->name);
                                                                            $typeUrl = route('allListType', [
                                                                                'danhmuc' => $category->slug,
                                                                                'loaidanhmuc' => $type->slug,
                                                                            ]);
                                                                        @endphp
                                                                        <li class="level2 nav-item-lv3">
                                                                            <a href="{{ $typeUrl }}"
                                                                                title="{{ $typeName }}">{{ $typeName }}</a>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </li>




                                <li class="nav-item ">
                                    <a class="nav-link" href="{{ route('fag') }}" title="Câu hỏi thường gặp">Câu
                                        hỏi thường gặp</a>
                                </li>




                                <li class="nav-item has-childs   hover-left" data-section="header_nav">
                                    <a href="javascript:void(0)" class="nav-link" title="Blog">Blog
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708" />
                                        </svg>
                                    </a>
                                    <i class="open_mnu down_icon"></i>

                                    <ul class="dropdown-menu">
										@foreach ($blogCate as $item)
										<li class="nav-item-lv2">
                                            <a class="nav-link" href="{{ route('listCateBlog', ['slug' => $item->slug]) }}" title="{{languageName($item->name)}}">{{languageName($item->name)}}</a>
                                        </li>

										@endforeach


                                    </ul>
                                </li>




                                <li class="nav-item ">
                                    <a class="nav-link" href="{{route('lienHe')}}" title="Liên hệ">Liên hệ</a>
                                </li>


                            </ul>
                        </div>
                        <div class="control-menu">
                            <a href="" id="prev"><svg xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 384 512">
                                    <path fill="#fff"
                                        d="M41.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.3 256 278.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z" />
                                </svg></a>
                            <a href="" id="next"><svg xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 384 512">
                                    <path fill="#fff"
                                        d="M342.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L274.7 256 105.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                                </svg></a>
                        </div>

                    </div>
                    <div class="col-lg-3 col-sm-12 col-xs-12 col-12 sudes-cate-header tab-content-mb" id="tab-menu-2">

                        <div class="sudes-list-cate" data-section="header_nav_cate">
                            <ul class="sudes-main-cate">
                                @foreach(($categoryhome ?? collect()) as $category)
                                    @php
                                        $cateName = languageName($category->name);
                                        $cateUrl = route('allListProCate', ['danhmuc' => $category->slug]);
                                        $types = $category->typeCate ?? collect();
                                        $tagCates = ($category->tagCate ?? collect())->filter(function ($tagCate) {
                                            return ($tagCate->tags ?? collect())->isNotEmpty();
                                        });
                                        $hasChildren = $types->isNotEmpty() || $tagCates->isNotEmpty();
                                        $cateAvatar = $category->avatar ?? '';
                                        if ($cateAvatar !== '' && !preg_match('~^https?://~i', $cateAvatar)) {
                                            $cateAvatar = url($cateAvatar);
                                        }
                                        $lazySrc = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC';
                                    @endphp
                                    <li class="sudes-main-cate-has-child menu-item-count">
                                        <a href="{{ $cateUrl }}" title="{{ $cateName }}">
                                            @if ($cateAvatar !== '')
                                                <img class="lazyload"
                                                    src="{{ $lazySrc }}"
                                                    data-src="{{ $cateAvatar }}"
                                                    alt="{{ $cateName }}" />
                                            @endif
                                            {{ $cateName }}<i class="icon_mask"></i>
                                        </a>
                                        @if ($hasChildren)
                                            <i class="open_mnu down_icon"></i>
                                            <ul class="menu-child sub-menu sudes-sub-mega-menu">
                                                @if ($types->isNotEmpty())
                                                    <li class="sudes-main-cate-has-child clearfix">
                                                        <a href="{{ $cateUrl }}" title="Loại sản phẩm">Loại sản phẩm</a>
                                                        <i class="open_mnu down_icon"></i>
                                                        <ul class="menu-child menu-child-2 sub-menu clearfix">
                                                            @foreach ($types as $type)
                                                                @php
                                                                    $typeName = languageName($type->name);
                                                                    $typeUrl = route('allListType', [
                                                                        'danhmuc' => $category->slug,
                                                                        'loaidanhmuc' => $type->slug,
                                                                    ]);
                                                                    $typeTwos = $type->typetwo ?? collect();
                                                                @endphp
                                                                <li class="{{ $typeTwos->isNotEmpty() ? 'sudes-main-cate-has-child clearfix' : '' }}">
                                                                    <a href="{{ $typeUrl }}" title="{{ $typeName }}">{{ $typeName }}</a>
                                                                    @if ($typeTwos->isNotEmpty())
                                                                        <i class="open_mnu down_icon"></i>
                                                                        <ul class="menu-child menu-child-3 sub-menu clearfix">
                                                                            @foreach ($typeTwos as $typeTwo)
                                                                                @php
                                                                                    $typeTwoName = languageName($typeTwo->name);
                                                                                    $typeTwoUrl = route('allListTypeTwo', [
                                                                                        'danhmuc' => $category->slug,
                                                                                        'loaidanhmuc' => $type->slug,
                                                                                        'loai2' => $typeTwo->slug,
                                                                                    ]);
                                                                                @endphp
                                                                                <li>
                                                                                    <a href="{{ $typeTwoUrl }}" title="{{ $typeTwoName }}">{{ $typeTwoName }}</a>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </li>
                                                @endif

                                                @foreach ($tagCates as $tagCate)
                                                    @php
                                                        $tagCateName = $tagCate->name;
                                                        $tagCateUrl = route('allTagCateList', ['tagCateSlug' => $tagCate->slug]);
                                                    @endphp
                                                    <li class="sudes-main-cate-has-child clearfix">
                                                        <a href="{{ $tagCateUrl }}" title="{{ $tagCateName }}">{{ $tagCateName }}</a>
                                                        <i class="open_mnu down_icon"></i>
                                                        <ul class="menu-child menu-child-2 sub-menu clearfix">
                                                            @foreach ($tagCate->tags as $tag)
                                                                <li>
                                                                    <a href="{{ route('allListTags', ['tag' => $tag->slug]) }}" title="{{ $tag->name }}">{{ $tag->name }}</a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                    </div>
                </div>
                {{-- <ul class="md-hidden list-menu-account">
                    <li class="li-account">
                        <a href="" title="Danh sách yêu thích">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                <path
                                    d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z" />
                            </svg>
                            Danh sách Yêu thích <div class="js-wishlist-count">0</div>
                        </a>
                    </li>
                    <li class="li-account">
                        <a href="" title="Hệ thống">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10 0C6.12297 0 2.96875 3.15422 2.96875 7.03125C2.96875 8.34117 3.3316 9.61953 4.01832 10.7286L9.59977 19.723C9.70668 19.8953 9.89504 20 10.0976 20C10.0992 20 10.1007 20 10.1023 20C10.3066 19.9984 10.4954 19.8905 10.6003 19.7152L16.0395 10.6336C16.6883 9.54797 17.0312 8.3023 17.0312 7.03125C17.0312 3.15422 13.877 0 10 0ZM15.0338 10.032L10.0888 18.2885L5.01434 10.1112C4.44273 9.18805 4.13281 8.12305 4.13281 7.03125C4.13281 3.80039 6.76914 1.16406 10 1.16406C13.2309 1.16406 15.8633 3.80039 15.8633 7.03125C15.8633 8.09066 15.5738 9.12844 15.0338 10.032Z"
                                    fill="white" />
                                <path
                                    d="M10 3.51562C8.06148 3.51562 6.48438 5.09273 6.48438 7.03125C6.48438 8.95738 8.03582 10.5469 10 10.5469C11.9884 10.5469 13.5156 8.93621 13.5156 7.03125C13.5156 5.09273 11.9385 3.51562 10 3.51562ZM10 9.38281C8.7009 9.38281 7.64844 8.32684 7.64844 7.03125C7.64844 5.73891 8.70766 4.67969 10 4.67969C11.2923 4.67969 12.3477 5.73891 12.3477 7.03125C12.3477 8.30793 11.3197 9.38281 10 9.38281Z"
                                    fill="white" />
                            </svg>
                            Hệ thống
                        </a>
                    </li>
                </ul> --}}
            </div>
        </div>
    </div>
    <div class="mobile-nav-overflow"></div>
</header>
<script>
    const header = document.querySelector('header.header');
    let headerHeight = header.offsetHeight;
    let resizeWindow = window.innerWidth;
    let offsetStickyHeader = header.offsetHeight;
    let offsetStickyDown = 0;
    let resizeTimer;

    const tabLinks = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.tab-content-mb');

    const handleResize = () => {
        if (resizeTimer) clearTimeout(resizeTimer);

        resizeTimer = setTimeout(() => {
            const newWidth = window.innerWidth;

            if (resizeWindow !== newWidth) {
                header.classList.remove('hSticky');
                header.style.minHeight = '';

                headerHeight = header.offsetHeight;
                header.style.minHeight = `${headerHeight}px`;

                resizeWindow = newWidth;
            }
        }, 200);
    };

    const handleScroll = () => {
        const scrollTop = window.scrollY;

        if (scrollTop > offsetStickyHeader && scrollTop > offsetStickyDown) {
            header.classList.add('hSticky');
        }

        if (scrollTop <= offsetStickyDown && scrollTop <= offsetStickyHeader) {
            header.classList.remove('hSticky');
        }

        offsetStickyDown = scrollTop;
    };

    const handleTabClick = (tabLink) => {
        const tabId = tabLink.dataset.tab;

        tabLinks.forEach((link) => link.classList.remove('active'));
        tabLink.classList.add('active');

        tabContents.forEach((tabContent) => tabContent.classList.remove('active'));
        document.getElementById(tabId).classList.add('active');
    };

    const initializeTabs = () => {
        if (window.innerWidth <= 991) {
            const tabMenu1 = document.getElementById('tab-menu-1');
            const tabLinkMenu1 = document.querySelector('.tab-link[data-tab="tab-menu-1"]');

            tabMenu1.classList.add('active');
            tabLinkMenu1.classList.add('active');

            tabLinks.forEach((tabLink) => {
                tabLink.addEventListener('click', () => handleTabClick(tabLink));
            });
        }
    };

    window.addEventListener('resize', handleResize);
    window.addEventListener('scroll', handleScroll);

    tabLinks.forEach((tabLink) => {
        tabLink.addEventListener('click', () => handleTabClick(tabLink));
    });

    document.addEventListener('DOMContentLoaded', initializeTabs);
</script>
