@extends('layouts.main.master')
@section('title')
{{$detail->name}}
@endsection
@section('description')
{{$detail->description}}
@endsection
@section('image')
{{ url(firstBeforeAfterImage($detail->images ?? '') ?: 'frontend/img/page-header-bg.png') }}
@endsection
@section('js')
@endsection
@section('css')
    <style>
        .project-detail-gallery .grid-item > a {
            display: block;
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            background: #f3ebe6;
            box-shadow: 0 8px 28px rgba(58, 45, 40, 0.07);
            transition: box-shadow 0.35s ease, transform 0.35s ease;
        }

        .project-detail-gallery .grid-item > a::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 1;
            background: linear-gradient(
                180deg,
                rgba(58, 51, 48, 0) 40%,
                rgba(58, 51, 48, 0.45) 100%
            );
            opacity: 0;
            transition: opacity 0.35s ease;
            pointer-events: none;
        }

        .project-detail-gallery .grid-item > a img {
            display: block;
            width: 100%;
            height: auto;
            vertical-align: middle;
            -webkit-filter: grayscale(100%);
            filter: grayscale(100%);
            transition:
                transform 0.55s cubic-bezier(0.25, 0.46, 0.45, 0.94),
                -webkit-filter 0.45s ease,
                filter 0.45s ease;
        }

        .project-detail-gallery .grid-item > a i {
            position: absolute;
            right: 14px;
            bottom: 14px;
            z-index: 2;
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            color: #b90808;
            background: rgba(255, 255, 255, 0.92);
            border-radius: 50%;
            border: 1px solid rgba(185, 8, 8, 0.12);
            box-shadow: 0 6px 20px rgba(58, 45, 40, 0.12);
            opacity: 0;
            transform: translateY(8px) scale(0.92);
            transition: opacity 0.35s ease, transform 0.35s ease, background 0.35s ease, color 0.35s ease;
            pointer-events: none;
        }

        .project-detail-gallery .grid-item > a:hover,
        .project-detail-gallery .grid-item > a:focus-visible {
            box-shadow: 0 14px 40px rgba(58, 45, 40, 0.12);
            transform: translateY(-3px);
            outline: none;
        }

        .project-detail-gallery .grid-item > a:hover::before,
        .project-detail-gallery .grid-item > a:focus-visible::before {
            opacity: 1;
        }

        .project-detail-gallery .grid-item > a:hover img,
        .project-detail-gallery .grid-item > a:focus-visible img {
            transform: scale(1.06);
            -webkit-filter: grayscale(0%);
            filter: grayscale(0%);
        }

        .project-detail-gallery .grid-item > a:active img {
            -webkit-filter: grayscale(0%);
            filter: grayscale(0%);
        }

        .project-detail-gallery .grid-item > a:hover i,
        .project-detail-gallery .grid-item > a:focus-visible i {
            opacity: 1;
            transform: translateY(0) scale(1);
            background: linear-gradient(135deg, #c41e1e 0%, #b90808 100%);
            color: #fff;
            border-color: transparent;
        }

        @media (max-width: 767px) {
            .project-detail-gallery .grid-3 .grid-item,
            .project-detail-gallery .grid-3 .grid-sizer {
                width: 50%;
            }

            .project-detail-gallery .grid-item > a {
                border-radius: 12px;
            }

            .project-detail-gallery .grid-item > a i {
                opacity: 1;
                transform: none;
                width: 36px;
                height: 36px;
                font-size: 15px;
                right: 10px;
                bottom: 10px;
            }

            .project-detail-gallery .grid-item > a::before {
                opacity: 0.35;
            }
        }

        @media (max-width: 500px) {
            .project-detail-gallery .grid-3 .grid-item,
            .project-detail-gallery .grid-3 .grid-sizer {
                width: 50%;
            }
        }
    </style>
@endsection
@section('content')
<main class="wrapper">
			
    <!-- Details Content -->
    <section class="blog-details blog-details-box">
        <div class="container-fluid">
            <div class="blog-details-inner">
                <div class="post-content">
                    <div class="row">
                        <div class="col-xl-7 col-md-7 pe-xl-5 pe-md-4">
                            <div class="style-masonry project-detail-gallery">
                                <div class="grid grid-3 gutter-10 clearfix">
                                    <div class="grid-sizer"></div>
                                    @foreach (json_decode($detail->images) ?: [] as $item)
                                    @php
                                        $imgUrl = is_string($item) ? $item : (is_array($item) ? ($item['after'] ?? $item['before'] ?? '') : '');
                                    @endphp
                                    @if($imgUrl)
                                    <div class="grid-item">
                                        <a href="{{ $imgUrl }}" data-fancybox="gallery" aria-label="Xem ảnh phóng to">
                                            <img src="{{ $imgUrl }}" alt="{{ $detail->name }}">
                                            <i class="bi bi-arrows-fullscreen" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                            
                        <!-- Service Side Info -->
                        <div class="col-xl-5 col-md-5 pt-4 ps-xl-5 ps-md-4">
                            <div class="sidebar">
                                <div class="post-header">
                                    <h1 class="post-title fw-normal">{{$detail->name}}</h1>
                                </div>
                                <div class="fulltext">
                                    {!!languageName($detail->content)!!}
                                </div>

                                <div class="divider-line-hr my-1"></div>

                                <div class="wptb-project-info1 border-0 bg-transparent">
                                    <div class="wptb--holder p-0">
                                        <div class="row">
                                            <div class="col-xxl-6">
                                                <div class="wptb--item border-0">
                                                    <div class="wptb--meta"><label>Danh mục:</label> <span>{{$detail->cateProject->name}}</span></div>
                                                </div>
                                                <div class="wptb--item border-0">
                                                    <div class="wptb--meta"><label>Tác giả:</label> <span>{{$detail->location}}</span></div>
                                                </div>
                                                <div class="wptb--item border-0">
                                                    <div class="wptb--meta"><label>Thời gian thực hiện:</label> <span>{{$detail->operate}}</span></div>
                                                </div>
                                                {{-- <div class="wptb--item border-0">
                                                    <div class="wptb--meta"><label>Khách hàng:</label> <span>{{$detail->location}}</span></div>
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                        
                            
                <div class="wptb-page-links">
                    <div class="wptb-pge-link--item previous">
                        @if($projectPrev)
                            <a href="{{ route('duanTieuBieuDetail', ['slug' => $projectPrev->slug]) }}" title="{{ $projectPrev->name }}">
                                <i class="bi bi-arrow-left"></i>
                                <span>{{ $projectPrev->name }}</span>
                            </a>
                        @elseif($detail->cateProject)
                            <a href="{{ route('projectCategory', ['slug' => $detail->cateProject->slug]) }}">
                                <i class="bi bi-arrow-left"></i>
                                <span>Quay lại danh mục</span>
                            </a>
                        @else
                            <a href="{{ route('duanTieuBieu') }}">
                                <i class="bi bi-arrow-left"></i>
                                <span>Quay lại</span>
                            </a>
                        @endif
                    </div>
                    <div class="wptb-pge-link--item next">
                        @if($projectNext)
                            <a href="{{ route('duanTieuBieuDetail', ['slug' => $projectNext->slug]) }}" title="{{ $projectNext->name }}">
                                <span>{{ $projectNext->name }}</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Gallery -->
            <div class="pd-top-50">
                <div class="wptb-heading">
                    <div class="wptb-item--inner">
                        <div class="row">
                            <div class="col-lg-12">
                                <h2 class="wptb-item--title mb-lg-0">
                                    <span>Tác phẩm khác</span></h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="style-masonry effect-grayscale">
                    <div class="grid grid-3 gutter-10 clearfix"> 
                          <div class="grid-sizer"></div>   
                          @foreach ($duanlq as $item)
                          <div class="grid-item">
                             @include('layouts.project.item',['item' => $item])
                          </div> 
                          @endforeach                       
                          
                    </div>
                 </div>
            </div>
        </div>
    </section>
    <!-- End Details Content -->
    
</main>

@endsection