@extends('layouts.main.master')
@section('title')
Liên hệ với chúng tôi
@endsection
@section('description')
Liên hệ với chúng tôi
@endsection
@section('image')
{{url(''.$setting->logo)}}
@endsection
@section('css')
<link href="{{ asset('frontend/css/breadcrumb_style.scss.css') }}" rel="stylesheet" type="text/css" media="all" />
<link href="{{ asset('frontend/css/contact_style.scss.css') }}" rel="stylesheet" type="text/css" media="all" />
@endsection
@section('js')
@endsection
@section('content')
@php
    $contactPhone1 = trim((string) ($setting->phone1 ?? ''));
    $contactPhone2 = trim((string) ($setting->phone2 ?? ''));
    $contactPhone1Digits = preg_replace('/[^0-9+]/', '', $contactPhone1);
    $contactPhone2Digits = preg_replace('/[^0-9+]/', '', $contactPhone2);
    $contactEmail = trim((string) ($setting->email ?? ''));
    $contactAddressMain = trim((string) ($setting->address2 ?? ''));
    $contactAddressOffice = trim((string) ($setting->address1 ?? ''));
    $contactAddress = $contactAddressMain !== '' ? $contactAddressMain : $contactAddressOffice;
@endphp
<div class="bodywrap contact-page">
  <section class="bread-crumb">
     <div class="container">
        <ul class="breadcrumb" >
           <li class="home">
              <a href="{{ route('home') }}"><span>Trang chủ</span></a>
              <span class="mr_lr">
                 &nbsp;
                 <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-inline--fa fa-chevron-right fa-w-10">
                    <path fill="currentColor" d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z" class=""></path>
                 </svg>
                 &nbsp;
              </span>
           </li>
           <li><strong><span>Liên hệ</span></strong></li>
        </ul>
     </div>
  </section>
  <h1 class="title-head-contact a-left d-none">Liên hệ</h1>
  <div class="layout-contact">
     <div class="container">
        <div class="row">
           <div class="col-lg-6 col-12">
              <div class="contact">
                 <h4>
                    {{ $setting->company ? strtoupper($setting->company) : 'LIÊN HỆ VỚI CHÚNG TÔI' }}
                 </h4>
                 <div class="des_foo">
                    @if (!empty($setting->footer_content))
                       {!! $setting->footer_content !!}
                    @else
                       {{ $setting->company }}@if (!empty($setting->webname)) - {{ $setting->webname }}@endif
                    @endif
                 </div>
                 <div class="info-contact">
                    <div class="group-address">
                       <ul>
                          @if ($contactAddress !== '')
                          <li>
                             <div class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                                   <path d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 256c-35.3 0-64-28.7-64-64s28.7-64 64-64s64 28.7 64 64s-28.7 64-64 64z"/>
                                </svg>
                             </div>
                             <div class="info">
                                <b>Địa chỉ</b>
                                <span>{{ $contactAddress }}</span>
                             </div>
                          </li>
                          @endif
                          @if ($contactAddressOffice !== '' && $contactAddressOffice !== $contactAddress)
                          <li>
                             <div class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                                   <path d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 256c-35.3 0-64-28.7-64-64s28.7-64 64-64s64 28.7 64 64s-28.7 64-64 64z"/>
                                </svg>
                             </div>
                             <div class="info">
                                <b>Văn phòng đại diện</b>
                                <span>{{ $contactAddressOffice }}</span>
                             </div>
                          </li>
                          @endif
                          @if ($contactPhone1 !== '')
                          <li>
                             <div class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                   <path d="M280 0C408.1 0 512 103.9 512 232c0 13.3-10.7 24-24 24s-24-10.7-24-24c0-101.6-82.4-184-184-184c-13.3 0-24-10.7-24-24s10.7-24 24-24zm8 192a32 32 0 1 1 0 64 32 32 0 1 1 0-64zm-32-72c0-13.3 10.7-24 24-24c75.1 0 136 60.9 136 136c0 13.3-10.7 24-24 24s-24-10.7-24-24c0-48.6-39.4-88-88-88c-13.3 0-24-10.7-24-24zM117.5 1.4c19.4-5.3 39.7 4.6 47.4 23.2l40 96c6.8 16.3 2.1 35.2-11.6 46.3L144 207.3c33.3 70.4 90.3 127.4 160.7 160.7L345 318.7c11.2-13.7 30-18.4 46.3-11.6l96 40c18.6 7.7 28.5 28 23.2 47.4l-24 88C481.8 499.9 466 512 448 512C200.6 512 0 311.4 0 64C0 46 12.1 30.2 29.5 25.4l88-24z"></path>
                                </svg>
                             </div>
                             <div class="info">
                                <b>Hotline</b>
                                <a title="{{ $contactPhone1 }}" href="tel:{{ $contactPhone1Digits }}">{{ $contactPhone1 }}</a>
                             </div>
                          </li>
                          @endif
                          @if ($contactPhone2 !== '')
                          <li>
                             <div class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                   <path d="M280 0C408.1 0 512 103.9 512 232c0 13.3-10.7 24-24 24s-24-10.7-24-24c0-101.6-82.4-184-184-184c-13.3 0-24-10.7-24-24s10.7-24 24-24zm8 192a32 32 0 1 1 0 64 32 32 0 1 1 0-64zm-32-72c0-13.3 10.7-24 24-24c75.1 0 136 60.9 136 136c0 13.3-10.7 24-24 24s-24-10.7-24-24c0-48.6-39.4-88-88-88c-13.3 0-24-10.7-24-24zM117.5 1.4c19.4-5.3 39.7 4.6 47.4 23.2l40 96c6.8 16.3 2.1 35.2-11.6 46.3L144 207.3c33.3 70.4 90.3 127.4 160.7 160.7L345 318.7c11.2-13.7 30-18.4 46.3-11.6l96 40c18.6 7.7 28.5 28 23.2 47.4l-24 88C481.8 499.9 466 512 448 512C200.6 512 0 311.4 0 64C0 46 12.1 30.2 29.5 25.4l88-24z"></path>
                                </svg>
                             </div>
                             <div class="info">
                                <b>Hotline phụ</b>
                                <a title="{{ $contactPhone2 }}" href="tel:{{ $contactPhone2Digits }}">{{ $contactPhone2 }}</a>
                             </div>
                          </li>
                          @endif
                          @if ($contactEmail !== '')
                          <li>
                             <div class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                   <path d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"></path>
                                </svg>
                             </div>
                             <div class="info">
                                <b>Email</b>
                                <a title="{{ $contactEmail }}" href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                             </div>
                          </li>
                          @endif
                       </ul>
                    </div>
                 </div>
              </div>
              <div class="form-contact">
                 <h4>LIÊN HỆ VỚI CHÚNG TÔI</h4>
                 <span class="content-form">
                    Nếu bạn có thắc mắc gì, có thể gửi yêu cầu cho chúng tôi, và chúng tôi sẽ liên lạc lại với bạn sớm nhất có thể.
                 </span>
                 @if (session('success'))
                 <div class="alert alert-success mt-3 mb-0">{{ session('success') }}</div>
                 @endif
                 @if (session('error'))
                 <div class="alert alert-danger mt-3 mb-0">{{ session('error') }}</div>
                 @endif
                 <div id="pagelogin">
                    <form method="post" action="{{ route('postcontact') }}" id="contact" accept-charset="UTF-8">
                       @csrf
                       <input type="hidden" name="redirect_url" value="{{ route('lienHe') }}">
                       <div class="group_contact">
                          <input placeholder="Họ và tên" type="text" class="form-control form-control-lg" required name="name" value="{{ old('name') }}">
                          <input placeholder="Email" type="email" required class="form-control form-control-lg" name="email" value="{{ old('email') }}">
                          <input type="tel" placeholder="Điện thoại*" name="phone" class="form-control form-control-lg" required value="{{ old('phone') }}">
                          <textarea placeholder="Nội dung" name="mess" class="form-control content-area form-control-lg" rows="5" required>{{ old('mess') }}</textarea>
                          <button type="submit" class="btn-lienhe">Gửi thông tin</button>
                       </div>
                    </form>
                 </div>
              </div>
           </div>
           <div class="col-lg-6 col-12">
              <div id="contact_map" class="map">
                 @if (!empty($setting->iframe_map))
                    {!! $setting->iframe_map !!}
                 @else
                    <iframe src="https://www.google.com/maps?q={{ urlencode($contactAddress) }}&output=embed" style="border:0" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                 @endif
              </div>
           </div>
        </div>
     </div>
  </div>
  <div id="js-global-alert" class="alert alert-success" role="alert">
     <button type="button" class="close"><span aria-hidden="true"><span aria-hidden="true">&times;</span></span></button>
     <h5 class="alert-heading"></h5>
     <p class="alert-content"></p>
  </div>
</div>
@endsection