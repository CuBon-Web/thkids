@extends('layouts.main.master')
@section('title')
    {{ $title }}
@endsection
@section('description')
    {{ $description }}
@endsection
@section('image')
    {{ url(firstBeforeAfterImage($image) ?: 'frontend/img/page-header-bg.png') }}
@endsection
@section('css')
@endsection
@section('js')
@endsection
@section('content')
    <main class="wrapper">
       
        <section class="wptb-project pd-top-140">
            <div class="container">
                <div class="wptb-heading">
                    <div class="wptb-item--inner text-center">
                        {{-- <h6 class="wptb-item--subtitle"><span>01//</span> {{ $Cate->name }}</h6> --}}
                        <h1 class="wptb-item--title"> <span>{{ $title }}</span></h1>
                        <p>{{ $description }}</p>
                    </div>
                </div>

                <div class="style-masonry effect-grayscale">
                    <div class="grid grid-3 gutter-10 clearfix">
                        <div class="grid-sizer"></div>
                        @foreach ($list as $item)
                        <div class="grid-item">
                            @include('layouts.project.item',['item'=>$item])
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="wptb-item--button text-center mt-5">
                   {{ $list->links() }}
                </div>
            </div>
        </section>
    </main>
@endsection
