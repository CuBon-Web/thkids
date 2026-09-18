@extends('layouts.main.master')
@php
    $blogPager = seo_paginator_links($blog ?? null);
@endphp
@section('title')
{{$title_page}} 
@endsection
@section('description')
{{ 'Tổng hợp bài viết thuộc danh mục ' . trim($title_page) . ' tại ' . ($setting->webname ?? $setting->company ?? 'website') . '.' }}
@endsection
@section('seo_prev')
{{ $blogPager['prev'] ?? '' }}
@endsection
@section('seo_next')
{{ $blogPager['next'] ?? '' }}
@endsection
@section('image')
{{ !empty($banner[0]->image) ? url($banner[0]->image) : url($setting->logo ?? '') }}
@endsection
@section('schema')
@php
    $cleanText = function ($value) {
        return seo_plain_text($value, 0);
    };
    $jsonFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
    $currentUrl = url()->current();
    $homeUrl = route('home');
    $siteUrl = url('/');
    $categoryUrl = !empty($cate_slug)
        ? route('listCateBlog', ['slug' => $cate_slug])
        : $currentUrl;
    $pageTitle = $cleanText($title_page);
    $siteName = $cleanText($setting->webname ?? $setting->company ?? $title_page);
    $publisherName = $cleanText($setting->company ?? $siteName);
    $publisherLogo = !empty($setting->logo)
        ? url($setting->logo)
        : (!empty($banner[0]->image) ? url($banner[0]->image) : null);

    $itemListElements = [];
    foreach ($blog as $index => $item) {
        $postUrl = route('detailBlog', ['slug' => $item->slug]);
        $postImage = !empty($item->image) ? url($item->image) : null;
        $itemListElements[] = [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'url' => $postUrl,
            'item' => array_filter([
                '@type' => 'BlogPosting',
                'headline' => $cleanText(languageName($item->title)),
                'description' => $cleanText(strip_tags(languageName($item->description))),
                'datePublished' => optional($item->created_at)->toIso8601String(),
                'image' => $postImage,
                'mainEntityOfPage' => $postUrl,
            ], function ($value) {
                return !is_null($value) && $value !== '';
            }),
        ];
    }

    $schemaGraph = [
        [
            '@type' => 'WebSite',
            '@id' => $siteUrl . '#website',
            'url' => $siteUrl,
            'name' => $siteName,
            'inLanguage' => 'vi-VN',
        ],
        array_filter([
            '@type' => 'Organization',
            '@id' => $siteUrl . '#organization',
            'name' => $publisherName,
            'url' => $siteUrl,
            'logo' => $publisherLogo ? [
                '@type' => 'ImageObject',
                'url' => $publisherLogo,
            ] : null,
        ], function ($value) {
            return !is_null($value) && $value !== '';
        }),
        [
            '@type' => 'BreadcrumbList',
            '@id' => $currentUrl . '#breadcrumb',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Trang chủ',
                    'item' => $homeUrl,
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $pageTitle,
                    'item' => $categoryUrl,
                ],
            ],
        ],
        [
            '@type' => 'CollectionPage',
            '@id' => $currentUrl . '#collection',
            'url' => $currentUrl,
            'name' => $pageTitle,
            'description' => $pageTitle,
            'inLanguage' => 'vi-VN',
            'isPartOf' => [
                '@type' => 'WebSite',
                '@id' => $siteUrl . '#website',
            ],
        ],
        [
            '@type' => 'ItemList',
            '@id' => $currentUrl . '#itemlist',
            'name' => $pageTitle,
            'numberOfItems' => count($itemListElements),
            'itemListElement' => $itemListElements,
        ],
    ];
@endphp
<script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@graph' => $schemaGraph], $jsonFlags) !!}</script>
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('frontend/css/breadcrumb_style.scss.css') }}">
<link rel="stylesheet" href="{{ asset('frontend/css/blog_article_style.scss.css') }}">
@endsection
@section('js')

@endsection
@section('content')
<div class="bodywrap">

  <section class="bread-crumb">
    <div class="container">
      <ul class="breadcrumb">					
        <li class="home">
          <a href="{{ route('home') }}"><span>Trang chủ</span></a>
          <span class="mr_lr">&nbsp;<svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="8" height="8"><path fill="currentColor" d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"></path></svg>&nbsp;</span>
        </li>
        <li><strong><span>{{ $title_page }}</span></strong></li>
      </ul>
    </div>
  </section>
<div class="blog_wrapper layout-blog" itemscope itemtype="https://schema.org/Blog">
  <meta itemprop="name" content="{{$title_page}}">
  <meta itemprop="description" content="{{$title_page}}">
  <div class="container">
    <h1 class="title-page">
        <span>{{$title_page}}</span>
    </h1>
    <div class="row">
        <div class="right-content col-lg-9 col-12">
          <div class="list-blogs">
              <div class="row row-fix">
                @forelse ($blog as $item)
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 col-fix">
                  <div class="item-blog">
                    <div class="block-thumb">
                        <a class="thumb" href="{{route('detailBlog',['slug'=>$item->slug])}}" title="{{languageName($item->title)}}">
                        <img width="600" height="380" class="lazyload" src="{{url('frontend/images/lazy.png')}}" data-src="{{url($item->image)}}"  alt="{{languageName($item->title)}}">
                        </a>
                    </div>
                    <div class="day_time">
                        <span class="day_item">{{date_format($item->created_at,'d')}}</span>
                        <span class="myear_item">{{date_format($item->created_at,'m/Y')}}</span>
                    </div>
                    <div class="block-content">
                        <h3><a href="{{route('detailBlog',['slug'=>$item->slug])}}" title="{{languageName($item->title)}}">{{languageName($item->title)}}</a></h3>
                        <p class="justify">{{ strip_tags(languageName($item->description)) }}</p>
                    </div>
                  </div>
              </div>
                @empty
                <div class="col-12">
                  <p class="mb-0 text-center text-muted">Chưa có bài viết trong danh mục này.</p>
                </div>
                @endforelse
              </div>
              <div class="text-center">
                <nav class="clearfix relative nav_pagi w_100 ">
                    {{$blog->links()}}
                </nav>
              </div>
          </div>
        </div>
        <div class="blog_left_base col-lg-3 col-12">
          <div class="aside-blog-right">
              <div class="blog_noibat">
                <h2 class="h2_sidebar_blog">
                    <a href="{{ route('allListBlog') }}" title="Bài viết mới">Bài viết mới</a>
                </h2>
                <div class="blog_content">
                    @foreach ($blognew as $item)
                    <div class="item clearfix">

                      <div class="post-thumb">
                          <a class="image-blog scale_hover" href="{{route('detailBlog',['slug'=>$item->slug])}}" title="{{languageName($item->title)}}">
                          <img width="600" height="380" class="img_blog lazyload" src="{{url('frontend/images/lazy.png')}}" data-src="{{url($item->image)}}"  alt="{{languageName($item->title)}}">
                          </a>
                      </div>
                      <div class="contentright">
                          <h3><a title="{{languageName($item->title)}}" href="{{route('detailBlog',['slug'=>$item->slug])}}">{{languageName($item->title)}}</a></h3>
                      </div>
                    </div>
                    @endforeach
                </div>
              </div>
              <div class="clearfix clear-fix"></div>
          </div>
        </div>
    </div>
  </div>
</div>
</div>
@endsection