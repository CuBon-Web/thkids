@extends('layouts.main.master')
@php
    $tagPager = seo_paginator_links($blog ?? null);
@endphp
@section('title')
{{$title_page}} 
@endsection
@section('description')
{{ 'Các bài viết gắn tag ' . trim(str_replace('Tag: ', '', $title_page)) . ' tại ' . ($setting->webname ?? $setting->company ?? 'website') . '.' }}
@endsection
@section('seo_prev')
{{ $tagPager['prev'] ?? '' }}
@endsection
@section('seo_next')
{{ $tagPager['next'] ?? '' }}
@endsection
@section('image')
{{ !empty($banner[0]->image) ? url($banner[0]->image) : url($setting->logo ?? '') }}
@endsection
@section('schema')

@endsection
@section('css')
@endsection
@section('js')

@endsection
@section('content')

<section class="page-title">
    <div class="auto-container">
      <div class="title-outer text-center">
        <h1 class="title">{{$title_page}}</h1>
        <ul class="page-breadcrumb">
          <li><a href="{{route('home')}}">Trang chủ</a></li>
          <li><a href="{{ route('allListBlog') }}">Tin tức</a></li>
          <li>{{$title_page}}</li>
        </ul>
      </div>
    </div>
  </section>
  <section class="blog-section">
    <div class="auto-container">
      @if (!empty($activeTag))
      <div class="mb-3">
        <span class="badge badge-primary">Tag đang xem: #{{ $activeTag }}</span>
      </div>
      @endif
      @if (!empty($popularBlogTags))
      <div class="mb-4">
        <div class="sidebar__tags-list">
          @foreach ($popularBlogTags as $tag)
          <a
            href="{{ route('listBlogTag', ['tag' => urlencode($tag)]) }}"
            class="{{ !empty($activeTag) && mb_strtolower($activeTag, 'UTF-8') === mb_strtolower($tag, 'UTF-8') ? 'active' : '' }}"
          >
            #{{ $tag }}
          </a>
          @endforeach
        </div>
      </div>
      @endif
      <div class="row">
        @forelse ($blog as $item)
        <div class="col-xl-4 col-md-6">
            <!-- News Block -->
            <div class="blog-block">
              <div class="inner-block">
                <div class="content">
                    <div class="h3 title"><a href="{{route('detailBlog',['slug'=>$item->slug])}}">{{languageName($item->title)}}</a></div>
                  <div class="text line_2">{{languageName($item->description)}}</div>
                  @php
                    $itemTags = json_decode($item->tags ?? '[]', true);
                    if (!is_array($itemTags)) {
                      $itemTags = array_filter(array_map('trim', explode(',', (string) ($item->tags ?? ''))));
                    }
                    $itemTags = array_slice($itemTags, 0, 3);
                  @endphp
                  @if (!empty($itemTags))
                  <div class="sidebar__tags-list mt-2">
                    @foreach ($itemTags as $tag)
                    <a href="{{ route('listBlogTag', ['tag' => urlencode($tag)]) }}">#{{ $tag }}</a>
                    @endforeach
                  </div>
                  @endif
                </div>
                <div class="image">
                  <a href="{{route('detailBlog',['slug'=>$item->slug])}}">
                    <img src="{{url(''.$item->image)}}" alt="{{languageName($item->title)}}">
                    <img src="{{url(''.$item->image)}}" alt="{{languageName($item->title)}}">
                  </a>
                  <div class="date">{{date_format($item->created_at,'m/y')}}</div>
                </div>
                <a class="btn-more" href="{{route('detailBlog',['slug'=>$item->slug])}}"><span class="btn-title">Read More</span> <span class="line"></span> <span class="arrow fal fa-arrow-right"></span></a>
              </div>
            </div>
          </div>
        @empty
        <div class="col-12">
          <p class="alert alert-warning mb-0">Chưa có bài viết cho tag này.</p>
        </div>
        @endforelse
        {{$blog->links()}}
      </div>
    </div>
  </section>
@endsection