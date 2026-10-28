@extends('layouts.main.master')
@section('title')
Thanh toán đơn hàng
@endsection
@section('description')
Hoàn tất đơn hàng của bạn
@endsection
@section('image')
{{ url($setting->logo ?? 'frontend/images/page-header-bg.jpg') }}
@endsection
@section('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Order",
  "name": {!! json_encode('Thanh toán đơn hàng') !!},
  "description": {!! json_encode('Hoàn tất đơn hàng của bạn') !!},
  "image": {!! json_encode(url($setting->logo ?? 'frontend/images/page-header-bg.jpg')) !!},
  "sku": {!! json_encode('') !!},
  "url": {!! json_encode(url()->current()) !!},
  "brand": {
    "@type": "Brand",
    "name": {!! json_encode(config('app.name')) !!}
  }
}
</script>
@endsection
@section('css')
<link href="{{ asset('frontend/css/checkout-page.css') }}" rel="stylesheet" type="text/css" media="all" />
@endsection
@section('js')
<script src="{{ asset('frontend/js/checkout-page.js') }}"></script>
@endsection
@section('content')
@php
    $total = 0;
    $shippingFee = 30000;
    $cartItems = (array) ($cart ?? []);
    foreach ($cartItems as $id => $item) {
        $unitPrice = (int) ($item['status_variant'] == 1 ? $item['price'] : (($item['discount'] ?? 0) > 0 ? $item['discount'] : $item['price']));
        $total += $unitPrice * (int) ($item['quantity'] ?? 1);
    }
    $grandTotal = $total + $shippingFee;
@endphp

<div class="bodywrap checkout-page">
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
                <li>
                    <a href="{{ route('listCart') }}"><span>Giỏ hàng</span></a>
                    <span class="mr_lr">
                        &nbsp;
                        <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                            <path fill="currentColor" d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"></path>
                        </svg>
                        &nbsp;
                    </span>
                </li>
                <li><strong><span>Thanh toán</span></strong></li>
            </ul>
        </div>
    </section>

    <section class="checkout-page-section">
        <div class="container" id="checkout-page"
             data-update-url="{{ route('update.cart') }}"
             data-remove-url="{{ route('remove.from.cart') }}"
             data-cart-url="{{ route('listCart') }}"
             data-shipping-fee="{{ $shippingFee }}">

            @if (session('error'))
            <div class="checkout-alert checkout-alert--error">{{ session('error') }}</div>
            @endif

            @if (count($cartItems) === 0)
            <div class="checkout-empty">
                <svg class="checkout-empty__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor">
                    <path d="M0 24C0 10.7 10.7 0 24 0H69.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 27 39.6 53.9l-64 256c-4.5 18.1-21.3 30.9-40.1 30.9H192c-17.7 0-32-14.3-32-32s14.3-32 32-32h318.5l64-256H96.9c-9.2 0-17.7 5.8-20.7 14.1L83.1 96H24C10.7 96 0 85.3 0 72S10.7 48 24 48h8.3l14.3-57.1C51.4 9.7 69.7 0 89.1 0H192c0 .1 0 .1 0 0H69.5C50.1 0 31.8 9.7 24.6 24H24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"/>
                </svg>
                <p class="checkout-empty__title">Giỏ hàng trống</p>
                <p class="checkout-empty__text">Bạn cần thêm sản phẩm trước khi thanh toán.</p>
                <a href="{{ route('allProduct') }}" class="checkout-empty__btn">Khám phá sản phẩm</a>
            </div>
            @else
            <h1 class="checkout-page-title">Thanh toán đơn hàng</h1>

            <form method="POST" action="{{ route('postBill') }}" id="checkout-form">
                @csrf
                <input type="hidden" name="total_money" id="checkout-total-money" value="{{ $grandTotal }}">
                <input type="hidden" name="shippingMethod" id="checkout-shipping-method" value="{{ $shippingFee }}">
                <input type="hidden" name="payment_method" id="checkout-payment-method" value="cod">

                <div class="row">
                    <div class="col-lg-7">
                        <div class="checkout-card form-wrap">
                            <h2 class="checkout-card__title">Thông tin nhận hàng</h2>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-inner">
                                        <label for="billingName">Họ và tên *</label>
                                        <input type="text" id="billingName" name="billingName" required value="{{ old('billingName', $profile->name ?? '') }}" placeholder="Nhập họ và tên">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-inner">
                                        <label for="billingPhone">Số điện thoại *</label>
                                        <input type="tel" id="billingPhone" name="billingPhone" required value="{{ old('billingPhone', $profile->phone ?? '') }}" placeholder="Nhập số điện thoại">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-inner">
                                        <label for="billingEmail">Email</label>
                                        <input type="email" id="billingEmail" name="billingEmail" value="{{ old('billingEmail', $profile->email ?? '') }}" placeholder="Nhập email (không bắt buộc)">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-inner">
                                        <label for="billingAddress">Địa chỉ nhận hàng *</label>
                                        <input type="text" id="billingAddress" name="billingAddress" required value="{{ old('billingAddress', $profile->address ?? '') }}" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-inner">
                                        <label for="note">Ghi chú đơn hàng</label>
                                        <textarea id="note" name="note" rows="4" placeholder="Ghi chú thêm (không bắt buộc)">{{ old('note') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <aside class="checkout-sidebar">
                            <div class="checkout-card added-product-summary">
                                <h2 class="checkout-card__title">Đơn hàng của bạn</h2>
                                <ul class="added-products" id="checkout-products">
                                    @foreach ($cartItems as $id => $item)
                                    @php
                                        $unitPrice = (int) ($item['status_variant'] == 1 ? $item['price'] : (($item['discount'] ?? 0) > 0 ? $item['discount'] : $item['price']));
                                        $lineTotal = $unitPrice * (int) ($item['quantity'] ?? 1);
                                        $thumb = $item['image'] ?? '';
                                        if ($thumb !== '' && !preg_match('~^https?://~i', $thumb)) {
                                            $thumb = url($thumb);
                                        }
                                        $itemName = is_string($item['name'] ?? null)
                                            ? $item['name']
                                            : (function_exists('languageName') ? languageName($item['name'] ?? '') : '');
                                    @endphp
                                    <li class="single-product" id="checkout-item-{{ $id }}" data-id="{{ $id }}" data-price="{{ $unitPrice }}">
                                        <div class="product-area">
                                            <div class="product-img">
                                                <img src="{{ $thumb ?: asset('frontend/images/lazy.png') }}" alt="{{ $itemName }}">
                                            </div>
                                            <div class="product-info">
                                                <h5><span>{{ $itemName }}</span></h5>
                                                @if (($item['status_variant'] ?? 0) == 1 && !empty($item['variant']))
                                                <small>{{ $item['variant'] }}</small>
                                                @endif
                                                <div class="product-total">
                                                    <div class="quantity-counter">
                                                        <button type="button" class="quantity-btn js-qty-minus" data-id="{{ $id }}" aria-label="Giảm">−</button>
                                                        <input type="number" min="1" class="quantity__input js-qty-input" data-id="{{ $id }}" value="{{ (int) ($item['quantity'] ?? 1) }}" aria-label="Số lượng">
                                                        <button type="button" class="quantity-btn js-qty-plus" data-id="{{ $id }}" aria-label="Tăng">+</button>
                                                    </div>
                                                    <strong><span class="product-price js-line-total" id="line-total-{{ $id }}">{{ number_format($lineTotal) }}₫</span></strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="delete-btn">
                                            <button type="button" class="js-remove-item" data-id="{{ $id }}" title="Xóa">Xóa</button>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="checkout-card cost-summary">
                                <table class="cost-summary-table">
                                    <tbody>
                                        <tr>
                                            <th>Tạm tính</th>
                                            <th id="checkout-subtotal-display">{{ number_format($total) }}₫</th>
                                        </tr>
                                        <tr>
                                            <th>Phí ship ước tính</th>
                                            <th id="checkout-shipping-display">{{ number_format($shippingFee) }}₫</th>
                                        </tr>
                                        <tr>
                                            <th>Tổng cộng</th>
                                            <th id="checkout-total-display">{{ number_format($grandTotal) }}₫</th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="checkout-card payment-methods">
                                <h2 class="checkout-card__title">Phương thức thanh toán</h2>
                                <ul class="payment-list">
                                    <li>
                                        <label class="payment-check">
                                            <input type="radio" name="payment_method_choice" value="cod" checked class="js-payment-choice">
                                            <span>
                                                <strong>Thanh toán khi nhận hàng (COD)</strong>
                                                <p class="para mb-0">Thanh toán tiền mặt khi nhận được hàng.</p>
                                            </span>
                                        </label>
                                    </li>
                                    {{-- @if (env('PAYOS_CLIENT_ID') && env('PAYOS_API_KEY'))
                                    <li>
                                        <label class="payment-check">
                                            <input type="radio" name="payment_method_choice" value="online" class="js-payment-choice">
                                            <span>
                                                <strong>Thanh toán online (PayOS)</strong>
                                                <p class="para mb-0">Chuyển hướng sang cổng thanh toán an toàn.</p>
                                            </span>
                                        </label>
                                    </li>
                                    @endif --}}
                                </ul>
                            </div>

                            <div class="place-order-btn">
                                <button type="submit" class="checkout-submit-btn primary-btn1">Đặt hàng</button>
                                <a href="{{ route('listCart') }}" class="checkout-back-link">← Quay lại giỏ hàng</a>
                            </div>
                        </aside>
                    </div>
                </div>
            </form>
            @endif
        </div>
    </section>
</div>
@endsection
