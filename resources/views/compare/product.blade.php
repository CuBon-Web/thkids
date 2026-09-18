@extends('layouts.main.master')
@section('title')
So sánh sản phẩm
@endsection
@section('description')
So sánh sản phẩm
@endsection
@section('image')
{{ url($setting->logo) }}
@endsection
@section('css')
<link rel="stylesheet" href="{{url('frontend/css/breadcrumb_style.scss.css')}}">
<link rel="stylesheet" href="{{url('frontend/css/paginate.scss.css')}}">
<link rel="preload" as="style" type="text/css" href="{{ asset('frontend/css/page-compare.scss.css') }}" />
<link href="{{ asset('frontend/css/page-compare.scss.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('js')
@endsection
@section('content')
@php
    $items = collect($list ?? [])->values();
    $hasItems = $items->isNotEmpty();
    $maxSlots = (int) ($maxSlots ?? 3);
@endphp
<div class="bodywrap">
   <section class="bread-crumb">
      <div class="container">
         <ul class="breadcrumb">
            <li class="home">
               <a href="{{ route('home') }}"><span>Trang chủ</span></a>
               <span class="mr_lr">&nbsp;<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-inline--fa fa-chevron-right fa-w-10"><path fill="currentColor" d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"></path></svg>&nbsp;</span>
            </li>
            <li><strong><span>So sánh sản phẩm</span></strong></li>
         </ul>
      </div>
   </section>
   <section class="page">
      <div class="container">
         <div class="wrap_background_aside padding-top-15 margin-bottom-40 pg_page">
            <div class="row">
               <div class="col-xs-12 col-sm-12 col-md-12">
                  <div class="page-title category-title">
                     <h1 class="title-head">
                        <a href="{{ route('compareList') }}" title="So sánh sản phẩm">So sánh sản phẩm</a>
                     </h1>
                  </div>
               </div>

               <div class="col-xs-12 col-sm-12 col-md-12" id="pageCompare"
                    data-remove-url="{{ route('removeCompare') }}">
                  @if ($hasItems)
                  <div class="content-page compare-table table-responsive d-block">
                     <table class="table">
                        <tbody>
                           <tr class="image">
                              <td>Hình ảnh</td>
                              @for ($i = 0; $i < $maxSlots; $i++)
                              <td>
                                 @if (isset($items[$i]))
                                 <img class="img-fluid" src="{{ url($items[$i]['image']) }}" alt="{{ $items[$i]['product_name'] }}">
                                 @endif
                              </td>
                              @endfor
                           </tr>
                           <tr class="title">
                              <td>Tên sản phẩm</td>
                              @for ($i = 0; $i < $maxSlots; $i++)
                              <td>
                                 @if (isset($items[$i]))
                                 <h3><a href="{{ $items[$i]['product_url'] }}">{{ $items[$i]['product_name'] }}</a></h3>
                                 @endif
                              </td>
                              @endfor
                           </tr>
                           <tr class="price">
                              <td>Giá</td>
                              @for ($i = 0; $i < $maxSlots; $i++)
                              <td>
                                 @if (isset($items[$i]))
                                    @if ($items[$i]['sale_price'] > 0)
                                       {{ number_format($items[$i]['sale_price']) }}₫
                                       @if ($items[$i]['original_price'] > $items[$i]['sale_price'])
                                       <del>{{ number_format($items[$i]['original_price']) }}₫</del>
                                       @endif
                                    @elseif ($items[$i]['original_price'] > 0)
                                       {{ number_format($items[$i]['original_price']) }}₫
                                    @else
                                       Liên hệ
                                    @endif
                                 @endif
                              </td>
                              @endfor
                           </tr>
                           <tr class="available">
                              <td>Tình trạng</td>
                              @for ($i = 0; $i < $maxSlots; $i++)
                              <td>@if (isset($items[$i])) Còn hàng @endif</td>
                              @endfor
                           </tr>
                           <tr class="type">
                              <td>Danh mục</td>
                              @for ($i = 0; $i < $maxSlots; $i++)
                              <td>@if (isset($items[$i])) {{ $items[$i]['category_name'] }} @endif</td>
                              @endfor
                           </tr>
                           <tr class="description">
                              <td>Cấu hình sản phẩm</td>
                              @for ($i = 0; $i < $maxSlots; $i++)
                              <td>
                                 @if (isset($items[$i]))
                                 <h4 class="d-none">{{ $items[$i]['product_name'] }}</h4>
                                 @if (!empty($items[$i]['specs']))
                                 <table>
                                    <tbody>
                                       @foreach ($items[$i]['specs'] as $spec)
                                       <tr>
                                          <td><p>{{ $spec['title'] !== '' ? $spec['title'] : 'Thông số' }}</p></td>
                                          <td><p>{{ $spec['detail'] !== '' ? $spec['detail'] : '-' }}</p></td>
                                       </tr>
                                       @endforeach
                                    </tbody>
                                 </table>
                                 @else
                                 <p>Chưa có thông số kỹ thuật.</p>
                                 @endif
                                 <div class="itemMainCompare">
                                    <a class="removeItem2" href="javascript:;"
                                       data-id="{{ $items[$i]['idpro'] }}"
                                       data-compare="{{ $items[$i]['pro_slug'] }}">Xóa</a>
                                 </div>
                                 @endif
                              </td>
                              @endfor
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  @endif
               </div>

               <div class="col-xs-12 col-sm-12 col-md-12">
                  <div class="null-table {{ $hasItems ? 'd-none' : 'd-block' }}">
                     <p class="img-empty">
                        <svg width="120" height="120" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                           <path d="M29.6434 151.291C35.0834 164.566 43.9534 167.329 50.1897 164.566C62.2505 159.222 93.5802 153.252 125.757 167.286C168.373 185.874 177.441 157.83 163.84 129.462C150.239 101.095 145.629 72.4073 161.308 56.6022C176.987 40.7971 156.469 16.0722 120.032 41.8091C96.4842 58.4412 79.2031 54.7377 69.9232 50.0359C61.1772 45.6046 51.4506 40.9719 42.3377 44.5891C31.2802 48.9784 26.9232 58.4946 43.7467 79.7539C67.587 109.88 17.4489 121.536 29.6434 151.291Z" fill="#fff0cd"></path>
                           <path d="M51.792 87.043H136.455V152.154C136.455 152.286 136.402 152.414 136.309 152.507C136.215 152.601 136.088 152.654 135.955 152.654H52.292C52.1594 152.654 52.0322 152.601 51.9384 152.507C51.8447 152.414 51.792 152.286 51.792 152.154V87.043Z" fill="#ffb400"></path>
                           <path d="M115.216 61.5273H75.8493L51.792 87.0426H136.455L115.216 61.5273Z" fill="#db9a00"></path>
                           <path d="M59.8118 51.3242L75.85 61.5304L51.7927 87.0457L33.5674 73.1945L59.8118 51.3242Z" fill="#ffb400"></path>
                           <path d="M131.254 51.3242L115.216 61.5304L136.455 87.0457L157.498 73.1945L131.254 51.3242Z" fill="#ffb400"></path>
                        </svg>
                     </p>
                     <p>Bạn chưa có sản phẩm nào để so sánh hãy thêm vào nhé</p>
                     <p class="mt-3"><a href="{{ route('home') }}" class="btn btn-primary">Về trang chủ</a></p>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>
</div>
@endsection
