@extends('layouts.main.master')
@section('title')
Kết quả tìm kiếm: {{ $keyword }}
@endsection
@section('description')
Tìm thấy {{ $product->total() }} sản phẩm cho từ khóa "{{ $keyword }}"
@endsection
@section('image')
{{ url($setting->logo ?? '') }}
@endsection
@section('css')
<link href="{{ asset('frontend/css/shop-list.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('frontend/css/search-result.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<div class="bodywrap search-result-page shop-list-page">
    <section class="bread-crumb">
        <div class="container">
            <ul class="breadcrumb">
                <li class="home">
                    <a href="{{ route('home') }}"><span>Trang chủ</span></a>
                    <span class="mr_lr">
                        <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                            <path fill="currentColor" d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"></path>
                        </svg>
                    </span>
                </li>
                <li><strong><span>Kết quả tìm kiếm</span></strong></li>
            </ul>
        </div>
    </section>

    <section class="search-result-section">
        <div class="container">
            <div class="search-result-card">
                <div class="search-result-head">
                    <h1 class="search-result-title">Kết quả tìm kiếm</h1>
                    <p class="search-result-meta">
                        Có <strong>{{ $product->total() }}</strong> sản phẩm phù hợp với từ khóa
                        <strong>"{{ $keyword }}"</strong>
                    </p>
                    <form action="{{ route('search_result') }}" method="get" class="search-result-form" role="search">
                        <input type="text" name="keyword" value="{{ $keyword }}" placeholder="Tìm lại sản phẩm..." required>
                        <button type="submit">Tìm kiếm</button>
                    </form>
                </div>

                @if ($product->total() > 0)
                <div class="row search-result-grid">
                    @foreach ($product as $item)
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-6">
                        @include('layouts.product.item', ['pro' => $item])
                    </div>
                    @endforeach
                </div>
                <div class="shop-pagination">
                    {{ $product->links() }}
                </div>
                @else
                <div class="search-result-empty">
                    <svg class="search-result-empty__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                        <path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/>
                    </svg>
                    <p class="search-result-empty__title">Không tìm thấy sản phẩm</p>
                    <p class="search-result-empty__text">Thử từ khóa khác hoặc xem toàn bộ danh mục sản phẩm.</p>
                    <a href="{{ route('allProduct') }}" class="search-result-empty__btn">Xem tất cả sản phẩm</a>
                </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
