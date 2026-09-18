{{-- https://bean-tools.mysapo.net/ --}}
<!DOCTYPE html>
<html lang="vi">

<head>
    @php
        $seoCanonical = trim($__env->yieldContent('canonical')) ?: seo_canonical_url();
        $seoRobots = trim($__env->yieldContent('robots')) ?: seo_robots_directive();
        $seoTitle = trim($__env->yieldContent('title'));
        $seoDescription = seo_plain_text(trim($__env->yieldContent('description')), 160);
        $seoImage = trim($__env->yieldContent('image'));
        $seoOgType = trim($__env->yieldContent('og_type')) ?: 'website';
        $seoKeywords = seo_plain_text(trim($__env->yieldContent('keywords')), 0);
        $seoPrev = trim($__env->yieldContent('seo_prev'));
        $seoNext = trim($__env->yieldContent('seo_next'));
        $seoImageWidth = trim($__env->yieldContent('image_width')) ?: '1200';
        $seoImageHeight = trim($__env->yieldContent('image_height')) ?: '630';
        $seoSiteName = $setting->webname ?? ($setting->company ?? config('app.name'));
    @endphp
    <meta charset="UTF-8" />
    <meta name="theme-color" content="#0b0b0b">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $seoTitle }}</title>
    <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
    <meta http-equiv="Content-Language" content="vi" />
    <link rel="alternate" href="{{ $seoCanonical }}" hreflang="vi" />
    <meta name="description" content="{{ $seoDescription }}">
    @if ($seoKeywords !== '')
        <meta name="keywords" content="{{ $seoKeywords }}">
    @endif
    <meta name="robots" content="{{ $seoRobots }}" />
    <meta name="googlebot" content="{{ $seoRobots }}">
    <meta name="revisit-after" content="3 days" />
    <meta name="rating" content="General">
    <meta name="application-name" content="{{ $seoSiteName }}" />
    <meta name="theme-color" content="#0b0b0b" />
    <meta name="msapplication-TileColor" content="#0b0b0b" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-title" content="{{ $seoSiteName }}" />
    @if ($seoImage)
        <link rel="apple-touch-icon-precomposed" href="{{ $seoImage }}" sizes="700x700">
    @endif
    <meta property="og:url" content="{{ $seoCanonical }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:image" content="{{ $seoImage }}">
    @if ($seoImage)
        <meta property="og:image:width" content="{{ $seoImageWidth }}">
        <meta property="og:image:height" content="{{ $seoImageHeight }}">
    @endif
    <meta property="og:site_name" content="{{ $seoSiteName }}">
    <meta property="og:image:alt" content="{{ $seoTitle }}">
    <meta property="og:type" content="{{ $seoOgType }}" />
    <meta property="og:locale" content="vi_VN" />
    @yield('og_extra')
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $seoTitle }}" />
    <meta name="twitter:description" content="{{ $seoDescription }}" />
    <meta name="twitter:image" content="{{ $seoImage }}" />
    <meta name="twitter:url" content="{{ $seoCanonical }}" />
    <meta itemprop="name" content="{{ $seoTitle }}">
    <meta itemprop="description" content="{{ $seoDescription }}">
    <meta itemprop="image" content="{{ $seoImage }}">
    <meta itemprop="url" content="{{ $seoCanonical }}">
    <link rel="canonical" href="{{ $seoCanonical }}">
    @if ($seoPrev !== '')
        <link rel="prev" href="{{ $seoPrev }}">
    @endif
    @if ($seoNext !== '')
        <link rel="next" href="{{ $seoNext }}">
    @endif
    @if ($seoImage)
        <link rel="image_src" href="{{ $seoImage }}" />
    @endif
    <link rel="shortcut icon" href="{{ url('' . $setting->favicon) }}" type="image/x-icon">
    <link rel="icon" href="{{ url('' . $setting->favicon) }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    @hasSection('schema')
        @yield('schema')
    @else
        @include('partials.seo-organization')
    @endif
    <style>
        :root {
            --mainColor: #c8103d;
            --subColor: #ea6182;
            --textColor: #333333;
            --hover: #f4b631;
            --price: #d31100;
        }
    </style>
    <link rel="stylesheet" href="/frontend/css/fonts.css">
    <link rel="stylesheet" href="/frontend/css/bootstrap-4-3-min.css">
    <link rel="stylesheet" href="/frontend/css/main.css">
    <link rel="stylesheet" href="/frontend/css/index.css">
    <link rel="stylesheet" href="/frontend/css/quickviews-popup-cart.css">
    <link rel="stylesheet" href="/frontend/css/ajaxcart.css">
    <script src="/frontend/js/jquery.js"></script>
    <script src="/frontend/js/swiper.js"></script>
    <script src="/frontend/js/lazy.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            awe_lazyloadImage();
            /*Header promotion*/

            var index = 0,
                stop = false,
                num = document.querySelectorAll('.js-promo > li').length,
                liEls = document.querySelectorAll('.js-promo > li');

            function shippingflipInX() {
                if (stop) return false;
                if (index > (num - 2)) {
                    liEls.forEach(function(el) {
                        el.classList.add('see-none');
                        el.classList.remove('animated', 'flipInX', 'see-block');
                    });
                    index = 0;
                    liEls[0].classList.add('animated', 'flipInX', 'see-block');
                    liEls[0].classList.remove('see-none');
                    return;
                }
                liEls.forEach(function(el) {
                    el.classList.add('see-none');
                    el.classList.remove('animated', 'flipInX', 'see-block');
                });
                liEls[index + 1].classList.remove('see-none');
                liEls[index + 1].classList.add('animated', 'flipInX', 'see-block');
                index++;
            }
            var hdtimer = setInterval(function() {
                shippingflipInX();
            }, 5000);

            document.querySelector('.js-promo').addEventListener('mouseenter', function() {
                stop = true;
            });
            document.querySelector('.js-promo').addEventListener('mouseleave', function() {
                stop = false;
            });


            function saveRecentSearch(term) {
                term = (term || '').trim();
                if (!term) return;
                recentList = recentList.filter(function(item) { return item !== term; });
                recentList.unshift(term);
                if (recentList.length > 8) recentList = recentList.slice(0, 8);
                localStorage.setItem('search_recent_list', JSON.stringify(recentList));
            }

            function renderRecentSearch() {
                if (!searchRecent) return;
                var searchList = searchRecent.querySelector('.search-list');
                if (!searchList) return;
                searchList.innerHTML = '';
                if (!recentList.length) {
                    searchRecent.classList.add('d-none');
                    return;
                }
                searchRecent.classList.remove('d-none');
                recentList.forEach(function(item) {
                    var link = document.createElement('a');
                    link.href = "{{ route('search_result') }}?keyword=" + encodeURIComponent(item);
                    link.title = 'Tìm kiếm ' + item;
                    link.classList.add('search-item');
                    link.appendChild(document.createTextNode(item));

                    var closeSpan = document.createElement('span');
                    closeSpan.textContent = 'Đóng';
                    closeSpan.title = 'Đóng';
                    closeSpan.classList.add('close');
                    closeSpan.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        recentList = recentList.filter(function(v) { return v !== item; });
                        localStorage.setItem('search_recent_list', JSON.stringify(recentList));
                        renderRecentSearch();
                    });

                    link.appendChild(closeSpan);
                    searchList.appendChild(link);
                });
            }

            var searchRecent = document.querySelector('.search-suggest .search-recent');
            var searchRecentList = localStorage.getItem('search_recent_list');
            var recentList = [];
            try {
                recentList = searchRecentList ? JSON.parse(searchRecentList) : [];
                if (!Array.isArray(recentList)) recentList = [];
            } catch (e) {
                recentList = [];
            }
            renderRecentSearch();

            var searchUrl = "{{ route('autosearchcomplete') }}";
            var searchTimers = new WeakMap();

            function fetchSearchSuggest(term) {
                return fetch(searchUrl + '?keyword=' + encodeURIComponent(term), {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                }).then(function(res) { return res.json(); });
            }

            var searchInput = document.querySelectorAll(
                '.header_tim_kiem input[type="text"], .search-mobile input[type="text"]');
            searchInput.forEach(function(input) {
                var form = input.closest('form');
                if (form) {
                    form.addEventListener('submit', function() {
                        saveRecentSearch(input.value);
                    });
                }

                input.addEventListener('keyup', function() {
                    var term = this.value.trim();
                    var listSearch = (this.closest('.search-bar') || document)
                        .querySelector('.list-search');
                    if (!listSearch) return;

                    var timer = searchTimers.get(this);
                    if (timer) clearTimeout(timer);

                    if (term.length > 1) {
                        if (searchRecent) searchRecent.classList.add('d-none');
                        var suggestBox = this.closest('.search-bar');
                        suggestBox = suggestBox ? suggestBox.querySelector('.search-suggest') : null;
                        searchTimers.set(this, setTimeout(function() {
                            fetchSearchSuggest(term).then(function(data) {
                                if (!data || !data.html) {
                                    listSearch.innerHTML = '<div class="not-pro">Không tìm thấy sản phẩm phù hợp</div>';
                                } else {
                                    listSearch.innerHTML = data.html;
                                }
                                if (suggestBox) suggestBox.classList.add('open');
                            }).catch(function() {
                                listSearch.innerHTML = '<div class="not-pro">Không tải được kết quả tìm kiếm</div>';
                                if (suggestBox) suggestBox.classList.add('open');
                            });
                        }, 250));
                    } else {
                        listSearch.innerHTML = '';
                        renderRecentSearch();
                    }
                });
            });

        });

        function awe_lazyloadImage() {
            var ll = new LazyLoad({
                elements_selector: ".lazyload",
                load_delay: 100,
                threshold: 0
            });
        }
        window.awe_lazyloadImage = awe_lazyloadImage;
    </script>
    @yield('css')
</head>

<body>
    <div class="opacity_menu"></div>
    @include('layouts.header.index')
    <div class="bodywrap">
        @yield('content')

        @include('layouts.footer.index')
    </div>


    <div class="backdrop__body-backdrop___1rvky"></div>

    <script type="text/javascript" defer src="/frontend/js/quickview.js"></script>
    <script src="/frontend/js/main.js" type="text/javascript"></script>
    <script src="/frontend/js/index.js" type="text/javascript"></script>
    <div class="main-widget">
        <div class="def-content unsee element">
            <div class="def-header">
                Liên hệ với chúng tôi
                <div class="close-icon" title="Đóng"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                        height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                        <path
                            d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                    </svg></div>
            </div>
            <div class="item phone">
                <a href="">
                    <span class="img">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path fill="currentColor"
                                d="M493.4 24.6l-104-24c-11.3-2.6-22.9 3.3-27.5 13.9l-48 112c-4.2 9.8-1.4 21.3 6.9 28l60.6 49.6c-36 76.7-98.9 140.5-177.2 177.2l-49.6-60.6c-6.8-8.3-18.2-11.1-28-6.9l-112 48C3.9 366.5-2 378.1.6 389.4l24 104C27.1 504.2 36.7 512 48 512c256.1 0 464-207.5 464-464 0-11.2-7.7-20.9-18.6-23.4z">
                            </path>
                        </svg>
                    </span>
                    <div class="detail">
                        <b class="arcu-item-title">
                            Hotline:
                        </b>
                        <span class="arcu-item-subtitle">
                            0988921123
                        </span>
                    </div>
                </a>
            </div>

            <div class="item zalo">
                <a target="_blank" href="">
                    <span class="img">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                            version="1.1" viewBox="0 0 614.501 613.667" xml:space="preserve">
                            <path fill="currentColor"
                                d="M464.721,301.399c-13.984-0.014-23.707,11.478-23.944,28.312c-0.251,17.771,9.168,29.208,24.037,29.202   c14.287-0.007,23.799-11.095,24.01-27.995C489.028,313.536,479.127,301.399,464.721,301.399z">
                            </path>
                            <path fill="currentColor"
                                d="M291.83,301.392c-14.473-0.316-24.578,11.603-24.604,29.024c-0.02,16.959,9.294,28.259,23.496,28.502   c15.072,0.251,24.592-10.87,24.539-28.707C315.214,313.318,305.769,301.696,291.83,301.392z">
                            </path>
                            <path fill="currentColor"
                                d="M310.518,3.158C143.102,3.158,7.375,138.884,7.375,306.3s135.727,303.142,303.143,303.142   c167.415,0,303.143-135.727,303.143-303.142S477.933,3.158,310.518,3.158z M217.858,391.083   c-33.364,0.818-66.828,1.353-100.133-0.343c-21.326-1.095-27.652-18.647-14.248-36.583c21.55-28.826,43.886-57.065,65.792-85.621   c2.546-3.305,6.214-5.996,7.15-12.705c-16.609,0-32.784,0.04-48.958-0.013c-19.195-0.066-28.278-5.805-28.14-17.652   c0.132-11.768,9.175-17.329,28.397-17.348c25.159-0.026,50.324-0.06,75.476,0.026c9.637,0.033,19.604,0.105,25.304,9.789   c6.22,10.561,0.284,19.512-5.646,27.454c-21.26,28.497-43.015,56.624-64.559,84.902c-2.599,3.41-5.119,6.88-9.453,12.725   c23.424,0,44.123-0.053,64.816,0.026c8.674,0.026,16.662,1.873,19.941,11.267C237.892,379.329,231.368,390.752,217.858,391.083z    M350.854,330.211c0,13.417-0.093,26.841,0.039,40.265c0.073,7.599-2.599,13.647-9.512,17.084   c-7.296,3.642-14.71,3.028-20.304-2.968c-3.997-4.281-6.214-3.213-10.488-0.422c-17.955,11.728-39.908,9.96-56.597-3.866   c-29.928-24.789-30.026-74.803-0.211-99.776c16.194-13.562,39.592-15.462,56.709-4.143c3.951,2.619,6.201,4.815,10.396-0.053   c5.39-6.267,13.055-6.761,20.271-3.357c7.454,3.509,9.935,10.165,9.776,18.265C350.67,304.222,350.86,317.217,350.854,330.211z    M395.617,369.579c-0.118,12.837-6.398,19.783-17.196,19.908c-10.779,0.132-17.593-6.966-17.646-19.512   c-0.179-43.352-0.185-86.696,0.007-130.041c0.059-12.256,7.302-19.921,17.896-19.222c11.425,0.752,16.992,7.448,16.992,18.833   c0,22.104,0,44.216,0,66.327C395.677,327.105,395.828,348.345,395.617,369.579z M463.981,391.868   c-34.399-0.336-59.037-26.444-58.786-62.289c0.251-35.66,25.304-60.713,60.383-60.396c34.631,0.304,59.374,26.306,58.998,61.986   C524.207,366.492,498.534,392.205,463.981,391.868z">
                            </path>
                        </svg>
                    </span>
                    <div class="detail">
                        <b class="arcu-item-title">
                            Zalo chat:
                        </b>
                        <span class="arcu-item-subtitle">
                            Tài khoản Zalo: 0123 456 789
                        </span>
                    </div>
                </a>
            </div>


            <div class="item mess">
                <a target="_blank" href="">
                    <span class="img">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            fill="currentColor" class="bi bi-messenger" viewBox="0 0 16 16">
                            <path
                                d="M0 7.76C0 3.301 3.493 0 8 0s8 3.301 8 7.76-3.493 7.76-8 7.76c-.81 0-1.586-.107-2.316-.307a.639.639 0 0 0-.427.03l-1.588.702a.64.64 0 0 1-.898-.566l-.044-1.423a.639.639 0 0 0-.215-.456C.956 12.108 0 10.092 0 7.76zm5.546-1.459-2.35 3.728c-.225.358.214.761.551.506l2.525-1.916a.48.48 0 0 1 .578-.002l1.869 1.402a1.2 1.2 0 0 0 1.735-.32l2.35-3.728c.226-.358-.214-.761-.551-.506L9.728 7.381a.48.48 0 0 1-.578.002L7.281 5.98a1.2 1.2 0 0 0-1.735.32z" />
                        </svg>
                    </span>
                    <div class="detail">
                        <b class="arcu-item-title">
                            Messenger:
                        </b>
                        <span class="arcu-item-subtitle">
                            m.me/thkids
                        </span>
                    </div>
                </a>
            </div>


            <div class="item map">
                <a target="_blank" href="">
                    <span class="img">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            fill="currentColor" class="bi bi-pin-map" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M3.1 11.2a.5.5 0 0 1 .4-.2H6a.5.5 0 0 1 0 1H3.75L1.5 15h13l-2.25-3H10a.5.5 0 0 1 0-1h2.5a.5.5 0 0 1 .4.2l3 4a.5.5 0 0 1-.4.8H.5a.5.5 0 0 1-.4-.8l3-4z" />
                            <path fill-rule="evenodd"
                                d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6zM4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999z" />
                        </svg>
                    </span>
                    <div class="detail">
                        <b class="arcu-item-title">
                            Hệ thống cửa hàng:
                        </b>
                        <span class="arcu-item-subtitle">
                            Xem địa chỉ cửa hàng
                        </span>
                    </div>
                </a>
            </div>

        </div>
        <div class="out-circle">
            <div class="pregan element"></div>
            <div class="pregan element"></div>
            <div class="main-icon">
                <svg width="20" height="20" viewBox="0 0 20 20" version="1.1"
                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <g transform="translate(-825 -308)">
                        <g>
                            <path transform="translate(825 308)" fill="#FFFFFF"
                                d="M 19 4L 17 4L 17 13L 4 13L 4 15C 4 15.55 4.45 16 5 16L 16 16L 20 20L 20 5C 20 4.45 19.55 4 19 4ZM 15 10L 15 1C 15 0.45 14.55 0 14 0L 1 0C 0.45 0 0 0.45 0 1L 0 15L 4 11L 14 11C 14.55 11 15 10.55 15 10Z">
                            </path>
                        </g>
                    </g>
                </svg>
                <p>
                    Liên hệ
                </p>
            </div>
            <div class="ser-icon unsee element">
                <div class="process">
                    <span class="img phone item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                            viewBox="0 0 512 512">
                            <path fill="currentColor"
                                d="M493.4 24.6l-104-24c-11.3-2.6-22.9 3.3-27.5 13.9l-48 112c-4.2 9.8-1.4 21.3 6.9 28l60.6 49.6c-36 76.7-98.9 140.5-177.2 177.2l-49.6-60.6c-6.8-8.3-18.2-11.1-28-6.9l-112 48C3.9 366.5-2 378.1.6 389.4l24 104C27.1 504.2 36.7 512 48 512c256.1 0 464-207.5 464-464 0-11.2-7.7-20.9-18.6-23.4z">
                            </path>
                        </svg>
                    </span>

                    <span class="img zalo item">
                        <svg width="25" height="25" xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                            viewBox="0 0 614.501 613.667" xml:space="preserve">
                            <path fill="currentColor"
                                d="M464.721,301.399c-13.984-0.014-23.707,11.478-23.944,28.312c-0.251,17.771,9.168,29.208,24.037,29.202   c14.287-0.007,23.799-11.095,24.01-27.995C489.028,313.536,479.127,301.399,464.721,301.399z">
                            </path>
                            <path fill="currentColor"
                                d="M291.83,301.392c-14.473-0.316-24.578,11.603-24.604,29.024c-0.02,16.959,9.294,28.259,23.496,28.502   c15.072,0.251,24.592-10.87,24.539-28.707C315.214,313.318,305.769,301.696,291.83,301.392z">
                            </path>
                            <path fill="currentColor"
                                d="M310.518,3.158C143.102,3.158,7.375,138.884,7.375,306.3s135.727,303.142,303.143,303.142   c167.415,0,303.143-135.727,303.143-303.142S477.933,3.158,310.518,3.158z M217.858,391.083   c-33.364,0.818-66.828,1.353-100.133-0.343c-21.326-1.095-27.652-18.647-14.248-36.583c21.55-28.826,43.886-57.065,65.792-85.621   c2.546-3.305,6.214-5.996,7.15-12.705c-16.609,0-32.784,0.04-48.958-0.013c-19.195-0.066-28.278-5.805-28.14-17.652   c0.132-11.768,9.175-17.329,28.397-17.348c25.159-0.026,50.324-0.06,75.476,0.026c9.637,0.033,19.604,0.105,25.304,9.789   c6.22,10.561,0.284,19.512-5.646,27.454c-21.26,28.497-43.015,56.624-64.559,84.902c-2.599,3.41-5.119,6.88-9.453,12.725   c23.424,0,44.123-0.053,64.816,0.026c8.674,0.026,16.662,1.873,19.941,11.267C237.892,379.329,231.368,390.752,217.858,391.083z    M350.854,330.211c0,13.417-0.093,26.841,0.039,40.265c0.073,7.599-2.599,13.647-9.512,17.084   c-7.296,3.642-14.71,3.028-20.304-2.968c-3.997-4.281-6.214-3.213-10.488-0.422c-17.955,11.728-39.908,9.96-56.597-3.866   c-29.928-24.789-30.026-74.803-0.211-99.776c16.194-13.562,39.592-15.462,56.709-4.143c3.951,2.619,6.201,4.815,10.396-0.053   c5.39-6.267,13.055-6.761,20.271-3.357c7.454,3.509,9.935,10.165,9.776,18.265C350.67,304.222,350.86,317.217,350.854,330.211z    M395.617,369.579c-0.118,12.837-6.398,19.783-17.196,19.908c-10.779,0.132-17.593-6.966-17.646-19.512   c-0.179-43.352-0.185-86.696,0.007-130.041c0.059-12.256,7.302-19.921,17.896-19.222c11.425,0.752,16.992,7.448,16.992,18.833   c0,22.104,0,44.216,0,66.327C395.677,327.105,395.828,348.345,395.617,369.579z M463.981,391.868   c-34.399-0.336-59.037-26.444-58.786-62.289c0.251-35.66,25.304-60.713,60.383-60.396c34.631,0.304,59.374,26.306,58.998,61.986   C524.207,366.492,498.534,392.205,463.981,391.868z">
                            </path>
                        </svg>
                    </span>


                    <span class="img mess item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                            fill="currentColor" class="bi bi-messenger" viewBox="0 0 16 16">
                            <path
                                d="M0 7.76C0 3.301 3.493 0 8 0s8 3.301 8 7.76-3.493 7.76-8 7.76c-.81 0-1.586-.107-2.316-.307a.639.639 0 0 0-.427.03l-1.588.702a.64.64 0 0 1-.898-.566l-.044-1.423a.639.639 0 0 0-.215-.456C.956 12.108 0 10.092 0 7.76zm5.546-1.459-2.35 3.728c-.225.358.214.761.551.506l2.525-1.916a.48.48 0 0 1 .578-.002l1.869 1.402a1.2 1.2 0 0 0 1.735-.32l2.35-3.728c.226-.358-.214-.761-.551-.506L9.728 7.381a.48.48 0 0 1-.578.002L7.281 5.98a1.2 1.2 0 0 0-1.735.32z" />
                        </svg>
                    </span>


                    <span class="img map item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                            fill="currentColor" class="bi bi-pin-map" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M3.1 11.2a.5.5 0 0 1 .4-.2H6a.5.5 0 0 1 0 1H3.75L1.5 15h13l-2.25-3H10a.5.5 0 0 1 0-1h2.5a.5.5 0 0 1 .4.2l3 4a.5.5 0 0 1-.4.8H.5a.5.5 0 0 1-.4-.8l3-4z" />
                            <path fill-rule="evenodd"
                                d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6zM4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999z" />
                        </svg>
                    </span>

                </div>
            </div>
            <div class="close-icon unsee element"><svg xmlns="http://www.w3.org/2000/svg" width="25"
                    height="25" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                    <path
                        d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                </svg>
            </div>
        </div>
    </div>
    <script>
        $(function() {
            var i = 1;
            var n = $('.ser-icon .process .item').length;
            var len = $('.ser-icon .process').width() / n;
            var pos = new WebKitCSSMatrix($('.ser-icon .process').css('transform'));
            $('.ser-icon').removeClass('unsee');

            function nextFrame() {
                if (i < n) {
                    i++;
                    var pos2 = new WebKitCSSMatrix($('.ser-icon .process').css('transform'));
                    $('.ser-icon .process').css('transform', 'translateX(' + (pos2.m41 - len) + 'px)');
                    setTimeout(nextFrame, 800);
                } else {
                    $('.ser-icon').addClass('unsee');
                    i = 1;
                    $('.ser-icon .process').css('transform', 'translateX(' + (pos.m41) + 'px)');
                    setTimeout(beginFrame, 2000);
                }
            };

            function beginFrame() {
                $('.ser-icon').removeClass('unsee');
                setTimeout(nextFrame, 900);
            };
            setTimeout(beginFrame, 2000);
            $('.close-icon').click(function(event) {
                $('.element').toggleClass('unsee');
            });
        });
    </script>
    @yield('js')
</body>

</html>
