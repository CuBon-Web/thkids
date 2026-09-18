@extends('layouts.main.master')
@section('title')
Câu hỏi thường gặp
@endsection
@section('description')
Câu hỏi thường gặp
@endsection
@section('image')
{{ url($setting->logo ?? 'frontend/images/12.jpg') }}
@endsection
@section('css')
<link href="{{ asset('frontend/css/breadcrumb_style.scss.css') }}" rel="stylesheet" type="text/css" media="all" />
<link href="{{ asset('frontend/css/style_page.scss.css') }}" rel="stylesheet" type="text/css" media="all" />
@endsection
@section('js')
@endsection
@section('content')
<div class="bodywrap faq-page">
   <section class="bread-crumb">
      <div class="container">
         <ul class="breadcrumb">
            <li class="home">
               <a href="{{ route('home') }}"><span>Trang chủ</span></a>
               <span class="mr_lr">
                  &nbsp;
                  <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="8" height="8">
                     <path fill="currentColor" d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"></path>
                  </svg>
                  &nbsp;
               </span>
            </li>
            <li><strong><span>Câu hỏi thường gặp</span></strong></li>
         </ul>
      </div>
   </section>
   <section class="page page-faq">
      <div class="container">
         <div class="page-title category-title text-center mb-3">
            <h1 class="title-head">Câu hỏi thường gặp</h1>
         </div>
         <div class="pg_page padding-top-15">
            <div class="row">
               <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col-12 order-lg-2 order-md-2">
                  <div class="box_right_faq">
                     <div class="form-contact box_hoi_dap">
                        <h2 class="box_muc_luc">LIÊN HỆ VỚI CHÚNG TÔI</h2>
                        <span class="content-form">
                           Nếu bạn có thắc mắc gì, có thể gửi yêu cầu cho chúng tôi, và chúng tôi sẽ liên lạc lại với bạn sớm nhất có thể.
                        </span>
                        <div id="pagelogin">
                           <form method="post" action="{{ route('postcontact') }}" id="contact" accept-charset="UTF-8">
                              @csrf
                              <input type="hidden" name="redirect_url" value="{{ route('fag') }}">
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
               </div>
               <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 col-12 order-lg-1 order-md-1">
                  <div class="content-page rte">
                     <div class="product-fpt-with-stick-tab" style="margin-top: 0">
                        <div class="panel-group" id="accordion1" role="tablist" aria-multiselectable="true">
                           @forelse ($homeFaqs ?? [] as $index => $faq)
                           <div class="faq{{ $index === 0 ? ' active' : '' }}">
                              <h4>{{ $loop->iteration }}. {{ $faq->question }}</h4>
                              <div class="content" style="{{ $index === 0 ? 'display:block' : 'display:none' }}">
                                 {!! $faq->answer !!}
                              </div>
                           </div>
                           @empty
                           <p class="mb-0">Chưa có câu hỏi thường gặp.</p>
                           @endforelse
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>
   <script>
      $('.faq h4').on('click', function (e) {
         e.preventDefault();
         var $faq = $(this).parents('.faq');
         var $content = $faq.find('.content');
         var isOpen = $faq.hasClass('active');

         $('.faq').not($faq).removeClass('active').find('.content').slideUp(200);
         if (isOpen) {
            $faq.removeClass('active');
            $content.slideUp(200);
         } else {
            $faq.addClass('active');
            $content.slideDown(200);
         }
         return false;
      });
   </script>
</div>
@endsection
