@extends('layouts.main.master')
@section('title')
Giỏ hàng của bạn
@endsection
@section('description')
{{ $setting->webname ?? 'Giỏ hàng' }}
@endsection
@section('image')
{{ url($setting->logo ?? '') }}
@endsection
@section('css')
<link href="{{ asset('frontend/css/cart-page.css') }}" rel="stylesheet" type="text/css" media="all" />
@endsection
@section('js')
<script>
window.cartPageRoutes = {
    update: @json(route('update.cart')),
    remove: @json(route('remove.from.cart')),
    clear: @json(route('clear.cart'))
};
</script>
<script src="{{ asset('frontend/js/cart-page.js') }}"></script>
@endsection
@section('content')
@php
    $cartItems = $cart ?? $cartcontent ?? [];
    $total = 0;
    $qty = 0;

    foreach ($cartItems as $details) {
        if ((int) ($details['status_variant'] ?? 0) === 1) {
            $linePrice = (float) ($details['price'] ?? 0);
        } elseif ((float) ($details['discount'] ?? 0) > 0) {
            $linePrice = (float) $details['discount'];
        } else {
            $linePrice = (float) ($details['price'] ?? 0);
        }
        $total += $linePrice * (int) ($details['quantity'] ?? 1);
        $qty += (int) ($details['quantity'] ?? 1);
    }
@endphp
<div class="bodywrap cart-page">
    <section class="bread-crumb">
        <div class="container">
            <ul class="breadcrumb">
                <li class="home">
                    <a href="{{ route('home') }}"><span>Trang chủ</span></a>
                    <span class="mr_lr">
                        &nbsp;
                        <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                            <path fill="currentColor" d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"></path>
                        </svg>
                        &nbsp;
                    </span>
                </li>
                <li><strong><span>Giỏ hàng</span></strong></li>
            </ul>
        </div>
    </section>

    <section class="cart-page-section">
        <div class="container">
            @if (count($cartItems) > 0)
            <div class="row">
                <div class="col-lg-8 mb-4 mb-lg-0">
                    <div class="cart-page-card">
                        <h1 class="cart-page-title">
                            Giỏ hàng
                            <span class="cart-page-count js-cart-count-label">({{ $qty }} sản phẩm)</span>
                        </h1>

                        <div class="cart-items js-cart">
                            @foreach ($cartItems as $cartKey => $item)
                            @php
                                if ((int) ($item['status_variant'] ?? 0) === 1) {
                                    $unitPrice = (float) ($item['price'] ?? 0);
                                } elseif ((float) ($item['discount'] ?? 0) > 0) {
                                    $unitPrice = (float) $item['discount'];
                                } else {
                                    $unitPrice = (float) ($item['price'] ?? 0);
                                }
                                $lineTotal = $unitPrice * (int) ($item['quantity'] ?? 1);
                                $productUrl = $productUrls[$item['idpro']]
                                    ?? (!empty($item['slug'])
                                        ? route('detailProduct', [
                                            'cate' => $item['cate_slug'] ?? 'san-pham',
                                            'type' => $item['type_slug'] ?? 'loai',
                                            'id' => $item['slug'],
                                        ])
                                        : route('allProduct'));
                                $thumb = $item['image'] ?? '';
                                if ($thumb !== '' && !preg_match('~^https?://~i', $thumb)) {
                                    $thumb = url($thumb);
                                }
                                $itemName = is_string($item['name'] ?? null) ? $item['name'] : (function_exists('languageName') ? languageName($item['name'] ?? '') : '');
                            @endphp
                            <article class="cart-item" data-cart-key="{{ $cartKey }}">
                                <a href="{{ $productUrl }}" class="cart-item__thumb" title="{{ $itemName }}">
                                    <img src="{{ $thumb ?: asset('frontend/images/lazy.png') }}" alt="{{ $itemName }}">
                                </a>
                                <div class="cart-item__body">
                                    <div class="cart-item__top">
                                        <div>
                                            <a href="{{ $productUrl }}" class="cart-item__name" title="{{ $itemName }}">
                                                {{ $itemName }}
                                            </a>
                                            @if ((int) ($item['status_variant'] ?? 0) === 1 && !empty($item['variant']))
                                            <span class="cart-item__variant">{{ $item['variant'] }}</span>
                                            @endif
                                        </div>
                                        <span class="cart-item__unit-price">
                                            <strong>{{ number_format($unitPrice) }}₫</strong>
                                        </span>
                                    </div>
                                    <div class="cart-item__footer">
                                        <div class="cart-qty">
                                            <button type="button" class="cart-qty__btn cart-qty__btn--minus" aria-label="Giảm số lượng">−</button>
                                            <input class="cart-qty__input js-quantity" readonly min="1" value="{{ $item['quantity'] }}" type="number" aria-label="Số lượng">
                                            <button type="button" class="cart-qty__btn cart-qty__btn--plus" aria-label="Tăng số lượng">+</button>
                                        </div>
                                        <span class="cart-item__line-total js-line-total">{{ number_format($lineTotal) }}₫</span>
                                        <button type="button" class="cart-item__remove" title="Xóa">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor"><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
                                            Xóa
                                        </button>
                                    </div>
                                </div>
                            </article>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <aside class="cart-summary">
                        <div class="cart-summary__box">
                            <h2 class="cart-summary__title">Tóm tắt đơn hàng</h2>
                            <div class="cart-summary__row">
                                <span>Số lượng</span>
                                <span class="js-cart-count-label">{{ $qty }} sản phẩm</span>
                            </div>
                            <div class="cart-summary__row cart-summary__row--total">
                                <span>Tổng tiền</span>
                                <strong class="js-cart-total">{{ number_format($total) }}₫</strong>
                            </div>
                            <a class="cart-summary__btn cart-summary__btn--checkout" href="{{ route('checkout') }}">Đặt hàng</a>
                            <a class="cart-summary__btn cart-summary__btn--shop" href="{{ route('allProduct') }}">Tiếp tục mua sắm</a>
                            <button type="button" class="cart-summary__btn cart-summary__btn--clear js-clearcart">Xóa tất cả</button>
                        </div>
                    </aside>
                </div>
            </div>
            @else
            <div class="cart-page-card">
                <div class="cart-empty">
                    <svg class="cart-empty__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor">
                        <path d="M0 24C0 10.7 10.7 0 24 0H69.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 27 39.6 53.9l-64 256c-4.5 18.1-21.3 30.9-40.1 30.9H192c-17.7 0-32-14.3-32-32s14.3-32 32-32h318.5l64-256H96.9c-9.2 0-17.7 5.8-20.7 14.1L83.1 96H24C10.7 96 0 85.3 0 72S10.7 48 24 48h8.3l14.3-57.1C51.4 9.7 69.7 0 89.1 0H192c0 .1 0 .1 0 0H69.5C50.1 0 31.8 9.7 24.6 24H24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"/>
                    </svg>
                    <p class="cart-empty__title">Giỏ hàng trống</p>
                    <p class="cart-empty__text">Bạn chưa có sản phẩm nào trong giỏ hàng.</p>
                    <a href="{{ route('allProduct') }}" class="cart-empty__btn">Khám phá sản phẩm</a>
                </div>
            </div>
            @endif
        </div>
    </section>
</div>
@endsection
