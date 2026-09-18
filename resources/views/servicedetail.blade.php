@extends('layouts.main.master')
@section('title')
{{($detail_service->name)}}
@endsection
@section('description')
{{($detail_service->description)}}
@endsection
@section('image')
{{url(''.$detail_service->image)}}
@endsection
@section('css')
@endsection
@section('js')
@endsection
@section('content')
<main class="wrapper">
       
    <section class="wptb-project pd-top-140 pd-bottom-5">
        <div class="container">
            <div class="wptb-heading">
                <div class="wptb-item--inner text-center">
                    {{-- <h6 class="wptb-item--subtitle"><span>01//</span> {{ $Cate->name }}</h6> --}}
                    <h1 class="wptb-item--title"> <span>{{ $detail_service->name }}</span></h1>
                </div>
            </div>

        </div>
    </section>
    <section class="blog-details pd-top-5">
        <div class="container">
            <div class="row">
                    
                <!-- Service Navigation List -->
                

                <div class="col-lg-8 col-md-8 mb-5 mb-md-0 ps-md-0">
                    <div class="blog-details-inner">
                        <div class="post-content">
                            {!!languageName($detail_service->content)!!}
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 pe-md-5">
                    <div class="sidebar">
                        <div class="sidenav">
                            <ul class="side_menu">
                                @foreach ($servicehome as $item)
                                <li class="menu-item {{request()->get('slug') == $item->slug ? 'active' : '' }}">
                                    <a href="{{route('serviceList',['slug'=>$item->slug])}}" class="d-flex align-items-center justify-content-between">
                                        <span>
                                            {{languageName($item->name)}}
                                        </span>
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div> 
                        <div class="service-sidebar-form">
                            <div class="service-sidebar-form__card">
                                <div class="service-sidebar-form__header">
                                    <span class="service-sidebar-form__eyebrow">
                                        <i class="bi bi-heart"></i> Liên hệ
                                    </span>
                                    <h3 class="service-sidebar-form__title">Đặt lịch tư vấn</h3>
                                    <p class="service-sidebar-form__desc">Để lại thông tin — chúng tôi sẽ gọi lại và tư vấn riêng cho bạn.</p>
                                    <div class="service-sidebar-form__divider"></div>
                                </div>

                                @if(session('success'))
                                    <div class="booking-alert booking-alert--success">{{ session('success') }}</div>
                                @endif
                                @if(session('error'))
                                    <div class="booking-alert booking-alert--error">{{ session('error') }}</div>
                                @endif

                                <div class="wptb-form--wrapper">
                                    <form class="wptb-form" action="{{ route('postcontact') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="redirect_url" value="{{ url()->current() }}">
                                        <input type="hidden" name="service_cate_slug" value="{{ $detail_service->cate_slug }}">
                                        <input type="hidden" name="service_name" value="{{ languageName($detail_service->name) }}">
                                        <div class="wptb-form--inner">
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <div class="form-group">
                                                        <input type="text" name="name" class="form-control"
                                                            placeholder="Họ và tên *" required autocomplete="name">
                                                    </div>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <div class="form-group">
                                                        <input type="tel" name="phone" class="form-control"
                                                            placeholder="Số điện thoại *" required autocomplete="tel">
                                                    </div>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <div class="form-group">
                                                        <input type="email" name="email" class="form-control"
                                                            placeholder="Email (tuỳ chọn)" autocomplete="email">
                                                    </div>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <div class="form-group">
                                                        <textarea name="mess" class="form-control" rows="4"
                                                            placeholder="Bạn muốn tư vấn điều gì? (tuỳ chọn)"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="wptb-item--button">
                                                        <button class="btn" type="submit">
                                                            <span class="btn-wrap">
                                                                <span class="text-first">Gửi yêu cầu</span>
                                                            </span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <p class="service-sidebar-form__note">Thông tin của bạn được bảo mật tuyệt đối.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Details Content -->
</main>

@endsection
