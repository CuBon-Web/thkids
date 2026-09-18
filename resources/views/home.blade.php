@extends('layouts.main.master')
@section('title')
    {{ trim(($setting->webname ?: '') . ($setting->webname && $setting->company ? ' | ' : '') . ($setting->company ?: '')) ?: config('app.name') }}
@endsection
@section('description')
    @php
        $homeDescParts = array_filter([
            $setting->webname ?? null,
            $setting->company ?? null,
            !empty($setting->address1) ? 'Địa chỉ: ' . $setting->address1 : null,
            !empty($setting->phone1) ? 'Hotline: ' . $setting->phone1 : null,
        ]);
        $homeDescription = seo_plain_text(implode('. ', $homeDescParts), 160);
    @endphp
    {{ $homeDescription }}
@endsection
@section('canonical')
    {{ route('home') }}
@endsection
@section('image')
    @php
        $ogBanner = $banner->first();
        $ogImage = $ogBanner && $ogBanner->image ? url($ogBanner->image) : url($setting->logo ?? '');
    @endphp
    {{ $ogImage }}
@endsection
@section('css')
    <style>
        .section_slider .swiper-slide a {
            display: block;
            width: 100%;
            height: 100%;
        }

        .section_slider .swiper-slide img.banner-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .section_slider .banner-img--mobile {
            display: none;
        }

        @media (max-width: 767px) {
            .section_slider .banner-img--desktop {
                display: none;
            }

            .section_slider .banner-img--mobile {
                display: block;
            }
        }

        .section_slider .banner-youtube {
            position: relative;
            width: 100%;
            height: 100%;
            background: #000 center / cover no-repeat;
        }

        .section_slider .banner-youtube iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
    </style>
@endsection
@section('js')
@endsection
@section('content')
    <h1 class="d-none">{{ $setting->webname }}</h1>

    @if (isset($banner) && $banner->isNotEmpty())
        <div class="section_slider">
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    @foreach ($banner as $item)
                        {{-- {{dd($item)}} --}}
                        @continue(!$item->isYoutube() && empty($item->image))
                        @php
                            $bannerTitle = $item->title ?: $setting->webname ?? 'Banner';
                            $bannerLink = 'javascript:void(0)';
                            if (!empty($item->link) && preg_match('~^(https?://|/|#)~i', $item->link)) {
                                $bannerLink = $item->link;
                            }
                            $bannerImage = '';
                            if (!empty($item->image)) {
                                $bannerImage = preg_match('~^https?://~i', $item->image)
                                    ? $item->image
                                    : url($item->image);
                            }
                            $bannerImageMobile = '';
                            if (!empty($item->image_mobile)) {
                                $bannerImageMobile = preg_match('~^https?://~i', $item->image_mobile)
                                    ? $item->image_mobile
                                    : url($item->image_mobile);
                            }
                            if ($bannerImageMobile === '') {
                                $bannerImageMobile = $bannerImage;
                            }
                        @endphp
                        <div class="swiper-slide">
                            @if ($item->isYoutube())
                                <div class="banner-youtube"
                                    @if ($bannerImage) style="background-image: url('{{ $bannerImage }}')" @endif>
                                    <iframe
                                        src="https://www.youtube.com/embed/{{ $item->youtube_id }}?autoplay=1&mute=1&controls=0&loop=1&playlist={{ $item->youtube_id }}&playsinline=1&rel=0&modestbranding=1"
                                        title="{{ $bannerTitle }}" allow="autoplay; encrypted-media; picture-in-picture"
                                        allowfullscreen></iframe>
                                </div>
                            @else
                                <a href="{{ $bannerLink }}" class="clearfix" title="{{ $bannerTitle }}">
                                    <img width="1920" height="600" src="{{ $bannerImage }}" alt="{{ $bannerTitle }}"
                                        class="img-responsive center-block duration-300 banner-img banner-img--desktop" />
                                    <img width="767" height="800" src="{{ $bannerImageMobile }}"
                                        alt="{{ $bannerTitle }}"
                                        class="img-responsive center-block duration-300 banner-img banner-img--mobile" />
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="swiper-button-prev">
                    <svg width="58" height="58" viewBox="0 0 58 58" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M18.5 29H39.5" stroke="white" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M29 18.5L39.5 29L29 39.5" stroke="white" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </div>
                <div class="swiper-button-next">
                    <svg width="58" height="58" viewBox="0 0 58 58" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M18.5 29H39.5" stroke="white" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M29 18.5L39.5 29L29 39.5" stroke="white" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
        <script>
            let swiperSlider = null;

            function initSwiperSlider() {
                swiperSlider = new Swiper('.section_slider .swiper-container', {
                    speed: 1000,
                    spaceBetween: 14,
                    effect: 'fade',
                    navigation: {
                        nextEl: '.section_slider .swiper-button-next',
                        prevEl: '.section_slider .swiper-button-prev',
                    },
                    autoplay: {
                        delay: 8000,
                        disableOnInteraction: false,
                    },
                    pagination: {
                        el: '.section_slider .swiper-pagination',
                        clickable: true,
                    },
                });
            }
            initSwiperSlider();
        </script>
    @endif

    <section class="section-index section_about">
        <div class="bg-banner">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-lg-5 col-md-6 col-right">
                        <div class="banner-product">
                            <img width="727" height="700"
                                src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"
                                data-src="{{ json_decode($gioithieu->image)[0] }}" alt="Banner"
                                class="img-responsive center-block lazyload" />
                        </div>
                    </div>
                    <div class="col-12 col-lg-7 col-md-6 col-left">
                        <div class="product-content">
                            <div class="img_icon">
                                <img width="64" height="64"
                                    src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"
                                    data-src="/frontend/images/baby-bib.png" alt="Icon 1"
                                    class="img-responsive center-block lazyload icon1" />
                                <img width="64" height="64"
                                    src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"
                                    data-src="/frontend/images/romper.png" alt="Icon 2"
                                    class="img-responsive center-block lazyload icon2" />
                            </div>
                            <div class="section-title">
                                <h2>Về TH KIDS</h2>
                            </div>
                            <div class="desc">
                                {!! $gioithieu->description !!}
                            </div>
                            <a href="{{ route('aboutUs') }}" title="Xem thêm" class="show-more btn btn-extent frame">
                                Xem thêm
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-index section_flash_sale">
        <div class="container">
            <div class="block-product-sale  has-deal-time">
                <div class="time_box">
                    <h2 class="title">
                        <a href="{{ route('flashSale') }}" title="Sản Phẩm Bán Chạy">
                            <img width="32" height="32" alt="Sản Phẩm bán chạy"
                                src="{{ asset('frontend/images/flash.png') }}">
                            Sản Phẩm Bán Chạy
                        </a>
                    </h2>

                    <div class="text_flas_slide">

                        <ul class="ul-default txtsale-list js-txtsale">



                            <li class="txtsale-item  txtsale-block">
                                <a class="text-white line_1" href="" title="Giảm ngay 20% tổng giá trị đơn hàng">Giảm
                                    ngay 20% tổng giá trị đơn hàng
                                </a>
                            </li>



                            <li class="txtsale-item  txtsale-none">
                                <a class="text-white line_1" href=""
                                    title="Giảm ngay 120k cho đơn hàng trên 500k">Giảm ngay 120k cho đơn hàng trên
                                    500k
                                </a>
                            </li>



                        </ul>

                    </div>


                    {{-- <div class="count-down">
                            <div class="timer-view" data-countdown="countdown" data-date="12-30-2026-09-15-45">
                            </div>
                        </div> --}}
                </div>


                <div class="swiper_sale swiper-container">
                    <div class="swiper-wrapper load-after" data-section="section_flash_sale">
                        @forelse(($homePro ?? collect()) as $pro)
                            @include('layouts.product.flash_item', ['pro' => $pro])
                        @empty
                            <div class="swiper-slide">
                                <div class="item_product_main p-3">
                                    <p class="mb-0">Nội dung đang được cập nhật</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <div class="swiper-button-prev">
                        <svg width="58" height="58" viewBox="0 0 58 58" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M18.5 29H39.5" stroke="white" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M29 18.5L39.5 29L29 39.5" stroke="white" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="swiper-button-next">
                        <svg width="58" height="58" viewBox="0 0 58 58" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M18.5 29H39.5" stroke="white" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M29 18.5L39.5 29L29 39.5" stroke="white" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <script>
        $(document).ready(function($) {
            function runSwiperSale() {
                var swiper_sale = null;

                function initSwiperSale() {
                    swiper_sale = new Swiper('.swiper_sale', {
                        slidesPerView: 5,
                        spaceBetween: 20,
                        slidesPerGroup: 1,
                        margin: 10,
                        navigation: {
                            nextEl: '.swiper_sale .swiper-button-next',
                            prevEl: '.swiper_sale .swiper-button-prev',
                        },
                        breakpoints: {
                            768: {
                                slidesPerView: 3,
                                spaceBetween: 20
                            },
                            993: {
                                slidesPerView: 4,
                                spaceBetween: 20
                            },
                            1025: {
                                slidesPerView: 5,
                                spaceBetween: 20
                            }
                        }
                    });
                }

                function destroySwiperSale() {
                    if (swiper_sale) {
                        swiper_sale.destroy(true, true);
                        swiper_sale = null;
                    }
                }

                function toggleSwiperSale() {
                    if ($(window).width() <= 767 && swiper_sale) {
                        destroySwiperSale();
                    } else if ($(window).width() > 767 && !swiper_sale) {
                        initSwiperSale();
                    }
                }
                toggleSwiperSale();
                $(window).resize(toggleSwiperSale);
            }
            lazyBlockProduct('section_flash_sale', '0px 0px -250px 0px', runSwiperSale);
        });
    </script>

    <section class="section-index section_4_banner">
        <div class="container">
            <div class="row load-after" data-section="section_4_banner">
                @foreach ($bannerads as $item)
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="three_banner">
                            <a class="duration-300" href="{{ $item->link }}" title="Banner">
                                <picture>
                                    <source media="(max-width: 480px)" srcset="{{ $item->image }}" />
                                    <img width="675" height="228" loading="lazy" class="lazyload duration-300"
                                        src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"
                                        data-src="{{ $item->image }}" alt="Banner" />
                                </picture>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <script>
        $(document).ready(function($) {
            lazyBlockProduct('section_4_banner', '0px 0px -300px 0px');
        });
    </script>

    <section class="section-index section_category">
        <div class="container">
            <div class="section-title">
                <h2>
                    Danh mục nổi bật
                </h2>
            </div>
            <div class="swiper_category swiper-container">
                <div class="swiper-wrapper load-after" data-section="section_category">
                    @foreach ($categoryhome as $item)
                        <div class="swiper-slide box-cate">
                            <a class="duration-300 cate-item"
                                href="{{ route('allListProCate', ['danhmuc' => $item->slug]) }}"
                                title="{{ languageName($item->name) }}">
                                <picture>
                                    <source media="(max-width: 480px)" srcset="{{ $item->image }}" />
                                    <img width="220" height="240" loading="lazy" class="lazyload duration-300"
                                        src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"
                                        data-src="{{ $item->avatar }}" alt="{{ languageName($item->name) }}" />
                                </picture>
                                <h3 class="cate-name line-clamp line-clamp-1 duration-300">
                                    {{ languageName($item->name) }}
                                </h3>
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>
    <script>
        $(document).ready(function($) {
            function runSwiperCate() {
                var swiper_cate = null;

                function initSwiper() {
                    swiper_cate = new Swiper('.swiper_category', {
                        slidesPerView: 5,
                        spaceBetween: 20,
                        watchOverflow: true,
                        slidesPerGroup: 1,
                        pagination: {
                            el: '.swiper_category .swiper-pagination',
                            clickable: true,
                        },
                        breakpoints: {
                            640: {
                                slidesPerView: 5,
                                spaceBetween: 15
                            },
                            768: {
                                slidesPerView: 5,
                                spaceBetween: 15
                            },
                            992: {
                                slidesPerView: 5,
                                spaceBetween: 20
                            },
                            1024: {
                                slidesPerView: 6,
                                spaceBetween: 20
                            }
                        }
                    });
                }

                function destroySwiper() {
                    if (swiper_cate) {
                        swiper_cate.destroy(true, true);
                        swiper_cate = null;
                    }
                }

                function toggleSwiper() {
                    if ($(window).width() <= 767 && swiper_cate) {
                        destroySwiper();
                    } else if ($(window).width() > 767 && !swiper_cate) {
                        initSwiper();
                    }
                }
                toggleSwiper();
                $(window).on('resize', function() {
                    toggleSwiper();
                });
            }
            lazyBlockProduct('section_category', '0px 0px -210px 0px', runSwiperCate);
        });
    </script>
    @foreach ($categoryhome ?? collect() as $category)
        @include('layouts.product.home_category_section', ['category' => $category])
    @endforeach




    <section class="section-index section_feedback">
        <div class="container">
            <div class="section-title">
                <h2>Hơn 5K khách hàng tin tưởng</h2>
            </div>
            <div class="swiper_feedback swiper-container control-top">
                <div class="swiper-wrapper load-after" data-section="section_feedback">
                    @foreach ($ReviewCus as $item)
                        <div class="swiper-slide">
                            <div class="avatar">
                                <img data-src="{{ $item->avatar }}" alt="{{ languageName($item->name) }}"
                                    width="200" height="200" class="lazyload"
                                    src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC" />
                            </div>
                            <div class="feedback-item">
                                <div class="block-content">
                                    <b>{{ languageName($item->name) }}</b>
                                    <span>{{ languageName($item->position) }}</span>
                                    <div class="feedback-content">
                                        {!! languageName($item->content) !!}
                                    </div>
                                </div>
                                <div class="maker-quote"></div>
                            </div>
                        </div>
                    @endforeach

                </div>
                <div class="swiper-button-prev">
                    <svg width="58" height="58" viewBox="0 0 58 58" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M18.5 29H39.5" stroke="white" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M29 18.5L39.5 29L29 39.5" stroke="white" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </div>
                <div class="swiper-button-next">
                    <svg width="58" height="58" viewBox="0 0 58 58" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M18.5 29H39.5" stroke="white" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M29 18.5L39.5 29L29 39.5" stroke="white" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </div>
            </div>
        </div>
    </section>
    <script>
        $(document).ready(function($) {
            function runSwiperFeedback() {
                var swiper_feedback = null;

                function initSwiperFeedback() {
                    swiper_feedback = new Swiper('.swiper_feedback', {
                        slidesPerView: 3,
                        spaceBetween: 20,
                        watchOverflow: true,
                        slidesPerGroup: 1,
                        grabCursor: true,
                        navigation: {
                            nextEl: '.section_feedback .swiper-button-next',
                            prevEl: '.section_feedback .swiper-button-prev',
                        },
                        breakpoints: {
                            640: {
                                slidesPerView: 2,
                                spaceBetween: 14
                            },
                            768: {
                                slidesPerView: 2,
                                spaceBetween: 14
                            },
                            992: {
                                slidesPerView: 2,
                                spaceBetween: 20
                            },
                            1024: {
                                slidesPerView: 2.5,
                                spaceBetween: 20
                            },
                            1200: {
                                slidesPerView: 3,
                                spaceBetween: 20
                            }
                        }
                    });
                }

                function destroySwiperFeedback() {
                    if (swiper_feedback) {
                        swiper_feedback.destroy(true, true);
                        swiper_feedback = null;
                    }
                }

                function toggleSwiperFeedback() {
                    if ($(window).width() <= 767 && swiper_feedback) {
                        destroySwiperFeedback();
                    } else if ($(window).width() > 767 && !swiper_feedback) {
                        initSwiperFeedback();
                    }
                }
                toggleSwiperFeedback();
                $(window).on('resize', function() {
                    toggleSwiperFeedback();
                });
            }
            lazyBlockProduct('section_feedback', '0px 0px -286px 0px', runSwiperFeedback);
        });
    </script>

    <section class="section-index section_blog">
        <div class="container">
            <div class="section-title">
                <h2>
                    <a href="{{ route('allListBlog') }}" title="Tin tức mới nhất">
                        Tin tức mới nhất
                    </a>
                </h2>
                <div class="see-more">
                    <a href="{{ route('allListBlog') }}" title="Xem tất cả">Xem thêm
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                            <path fill="#fff"
                                d="M342.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L274.7 256 105.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z">
                            </path>
                        </svg>
                    </a>
                </div>
            </div>
            @if (isset($hotnews) && $hotnews->isNotEmpty())
                <div class="swiper_blogs swiper-container">
                    <div class="swiper-wrapper load-after" data-section="section_blog">
                        @foreach ($hotnews->take(4) as $nullItem)
                            <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 swiper-slide item_null">
                                <div class="item_blog_index"></div>
                            </div>
                        @endforeach
                        <script type="text/x-custom-template" data-template="section_blog">
                            @foreach($hotnews as $item)
                                @php
                                    $blogTitle = languageName($item->title);
                                    $blogUrl = route('detailBlog', ['slug' => $item->slug]);
                                    $blogImage = !empty($item->image)
                                        ? (preg_match('~^https?://~i', $item->image) ? $item->image : url($item->image))
                                        : '';
                                    $blogDesc = seo_plain_text(languageName($item->description), 120);
                                    $blogAuthor = $item->author ?: ($setting->webname ?? 'Admin');
                                    $blogDate = optional($item->created_at)->format('d/m/Y');
                                @endphp
                                <div class="swiper-slide">
                                    <div class="item_blog_index">
                                        <a class="image-blog" href="{{ $blogUrl }}" title="{{ $blogTitle }}">
                                            <img
                                                src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"
                                                data-src="{{ $blogImage }}"
                                                alt="{{ $blogTitle }}"
                                                class="lazyload duration-300" />
                                        </a>
                                        <div class="blog_content">
                                            <h3>
                                                <a href="{{ $blogUrl }}" title="{{ $blogTitle }}">{{ $blogTitle }}</a>
                                            </h3>
                                            <p class="update_date clearfix">
                                                <span class="user_name">Đăng bởi: <b>{{ $blogAuthor }}</b></span>
                                                <span class="user_date"> {{ $blogDate }}</span>
                                            </p>
                                            @if($blogDesc !== '')
                                                <p class="blog_description">{{ $blogDesc }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </script>
                    </div>
                    <div class="swiper-button-prev">
                        <svg width="58" height="58" viewBox="0 0 58 58" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M18.5 29H39.5" stroke="white" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M29 18.5L39.5 29L29 39.5" stroke="white" stroke-width="2" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="swiper-button-next">
                        <svg width="58" height="58" viewBox="0 0 58 58" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M18.5 29H39.5" stroke="white" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M29 18.5L39.5 29L29 39.5" stroke="white" stroke-width="2" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
            @endif
        </div>
    </section>
    <script>
        $(document).ready(function($) {
            function runSwiperBlogs() {
                var blogs_pro = null;

                function initSwiperBlogs() {
                    blogs_pro = new Swiper('.swiper_blogs', {
                        slidesPerView: 4,
                        spaceBetween: 20,
                        slidesPerGroup: 1,
                        grabCursor: true,
                        navigation: {
                            nextEl: '.swiper_blogs .swiper-button-next',
                            prevEl: '.swiper_blogs .swiper-button-prev',
                        },
                        breakpoints: {
                            640: {
                                slidesPerView: 1,
                                spaceBetween: 15
                            },
                            768: {
                                slidesPerView: 2,
                                spaceBetween: 20
                            },
                            992: {
                                slidesPerView: 2,
                                spaceBetween: 20
                            },
                            1024: {
                                slidesPerView: 3,
                                spaceBetween: 20
                            },
                            1200: {
                                slidesPerView: 4,
                                spaceBetween: 20
                            },
                            1500: {
                                slidesPerView: 4,
                                spaceBetween: 20
                            }
                        }
                    });
                }

                function destroySwiperBlogs() {
                    if (blogs_pro) {
                        blogs_pro.destroy(true, true);
                        blogs_pro = null;
                    }
                }

                function toggleSwiperBlogs() {
                    if ($(window).width() <= 767 && blogs_pro) {
                        destroySwiperBlogs();
                    } else if ($(window).width() > 767 && !blogs_pro) {
                        initSwiperBlogs();
                    }
                }
                toggleSwiperBlogs();
                $(window).resize(toggleSwiperBlogs);
            }
            lazyBlockProduct('section_blog', '0px 0px -250px 0px', runSwiperBlogs);
        });
    </script>

    <section class="section-index section_topic">
        <div class="container">
            <div class="section-title">
                <h2>Chủ đề cho bạn</h2>
            </div>
            <div class="box-top-pic load-after" data-section="section_topic">
               @foreach ($blogCate as $item)
               <div class="item-topic">
                  <a class="opaci-topic" href="{{route('listCateBlog',['slug'=>$item->slug])}}" title="{{languageName($item->name)}}"></a>
                  <img width="64" height="64" loading="lazy" class="img-fluid lazyload duration-300"
                      src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"
                      data-src="{{$item->avatar}}"
                      alt="{{languageName($item->name)}}" />
                  <div class="content-topic">
                      <h3>{{languageName($item->name)}}</h3>
                      <span> {{count($item->listBlog)}} bài viết</span>
                  </div>
              </div>
               @endforeach
            </div>
        </div>
    </section>
    <script>
        $(document).ready(function($) {
            lazyBlockProduct('section_topic', '0px 0px -300px 0px');
        });
    </script>

    <div id="js-global-alert" class="alert alert-success" role="alert">
        <button type="button" class="close"><span aria-hidden="true"><span
                    aria-hidden="true">&times;</span></span></button>
        <h5 class="alert-heading"></h5>
        <p class="alert-content"></p>
    </div>



    <div class="section-index section_services">
        <div class="container">
            <div class="row promo-box">
                <div class="col-lg-15 col-md-3 col-sm-6 col-6 promo-item duration-300">
                    <div class="icon aspect-1">
                        <img width="108" height="108" class="lazyload"
                            data-src="/frontend/images/ser_1.png?1773396324053"
                            alt="Lớn lên <br>cũng bé"
                            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC" />
                    </div>
                    <div class="info">
                        <h3>Lớn lên <br>cũng bé</h3>
                    </div>
                </div>
                <div class="col-lg-15 col-md-3 col-sm-6 col-6 promo-item duration-300">
                    <div class="icon aspect-1">
                        <img width="108" height="108" class="lazyload"
                            data-src="/frontend/images/ser_2.png?1773396324053"
                            alt="Giao hàng <br>siêu tốc"
                            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC" />
                    </div>
                    <div class="info">
                        <h3>Giao hàng <br>siêu tốc</h3>
                    </div>
                </div>
                <div class="col-lg-15 col-md-3 col-sm-6 col-6 promo-item duration-300">
                    <div class="icon aspect-1">
                        <img width="108" height="108" class="lazyload"
                            data-src="/frontend/images/ser_3.png?1773396324053"
                            alt="Sản phẩm <br> chính hãng"
                            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC" />
                    </div>
                    <div class="info">
                        <h3>Sản phẩm <br> chính hãng</h3>
                    </div>
                </div>
                <div class="col-lg-15 col-md-3 col-sm-6 col-6 promo-item duration-300">
                    <div class="icon aspect-1">
                        <img width="108" height="108" class="lazyload"
                            data-src="/frontend/images/ser_4.png?1773396324053"
                            alt="Đổi trả <br> miễn phí"
                            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC" />
                    </div>
                    <div class="info">
                        <h3>Đổi trả <br> miễn phí</h3>
                    </div>
                </div>
                <div class="col-lg-15 col-md-3 col-sm-6 col-6 promo-item duration-300">
                    <div class="icon aspect-1">
                        <img width="108" height="108" class="lazyload"
                            data-src="/frontend/images/ser_5.png?1773396324053"
                            alt="Hỗ trợ <br> 24/7"
                            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC" />
                    </div>
                    <div class="info">
                        <h3>Hỗ trợ <br> 24/7</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
