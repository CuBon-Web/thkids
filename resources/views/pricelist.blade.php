@extends('layouts.main.master')
@section('title')
Price List | {{ $setting->company }}
@endsection
@section('description')
View our photography and visual services pricing. {{ $setting->company }}
@endsection
@section('image')
{{ url('' . $setting->logo) }}
@endsection
@section('css')
@endsection
@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.price-list-content table').forEach(function (table) {
        if (table.parentElement && table.parentElement.classList.contains('price-list-table-wrap')) {
            return;
        }
        var wrap = document.createElement('div');
        wrap.className = 'price-list-table-wrap';
        table.parentNode.insertBefore(wrap, table);
        wrap.appendChild(table);
    });
});
</script>
@endsection
@section('content')

<section class="page-header">
    <div class="bg-img bg-eager" data-background="{{ static_bg('frontend/img/page-header-bg.png') }}"></div>
    <div class="overlay"></div>
    <div class="container">
        <div class="page-header-content">
            <h1 class="title">Price List</h1>
            <h4 class="sub-title">
                <a class="home" href="{{ route('home') }}">Home</a>
                <span class="icon">-</span>
                <span class="inner-page">Price List</span>
            </h4>
        </div>
    </div>
</section>

<section class="price-list-section pt-60 pb-80">
    <div class="container">
        <div class="row section-heading-wrap w-100 ml-0 mb-40">
            <div class="col-lg-4 col-md-12">
                <div class="section-heading mb-0">
                    <h4 class="sub-heading" data-text-animation="fade-in-right" data-split="char" data-duration="0.9" data-stagger="0.03">pricing</h4>
                </div>
            </div>
            <div class="col-lg-8 col-md-12">
                <div class="section-heading section-heading-2 mb-0">
                    <h2 class="section-title title-2">Transparent <span>Packages</span> for Every Project</h2>
                </div>
            </div>
        </div>

        @php
            $priceContent = trim((string) ($setting->footer_content ?? ''));
        @endphp

        @if ($priceContent !== '')
            <div class="price-list-content">
                {!! $priceContent !!}
            </div>
            <div class="price-list-cta mt-50">
                <p class="price-list-cta-text">Need a custom quote or have questions about a package?</p>
                <a href="{{ route('lienHe') }}" class="tl-primary-btn">Contact Us</a>
            </div>
        @else
            <div class="price-list-empty">
                <p>Pricing information is being updated. Please check back soon or contact us for a quote.</p>
                <a href="{{ route('lienHe') }}" class="tl-primary-btn">Contact Us</a>
            </div>
        @endif
    </div>
</section>

@endsection
