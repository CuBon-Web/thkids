@extends('layouts.main.master')
@section('title')
    {{ $blog_detail->seo_title ? $blog_detail->seo_title : languageName($blog_detail->title) }}
@endsection
@section('description')
    {{ $blog_detail->meta_description ? $blog_detail->meta_description : languageName($blog_detail->description) }}
@endsection
@section('og_type')
article
@endsection
@section('og_extra')
<meta property="article:published_time" content="{{ optional($blog_detail->created_at)->toIso8601String() }}">
<meta property="article:modified_time" content="{{ optional($blog_detail->updated_at)->toIso8601String() }}">
@if (!empty($blog_detail->author))
<meta property="article:author" content="{{ $blog_detail->author }}">
@endif
@foreach (($blogTags ?? []) as $tag)
<meta property="article:tag" content="{{ $tag }}">
@endforeach
@endsection
@section('keywords')
{{ trim(implode(', ', array_filter(array_merge(
    [$blog_detail->focus_keyword ?? null],
    $blogTags ?? []
)))) }}
@endsection
@section('canonical')
{{ route('detailBlog', ['slug' => $blog_detail->slug]) }}
@endsection
@section('image')
    {{ url('' . $blog_detail->image) }}
@endsection
@section('schema')
    @php
        $cleanText = function ($value) {
            return seo_plain_text($value, 0);
        };
        $jsonFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        $postTitle = $cleanText(languageName($blog_detail->title));
        $postDescription = seo_plain_text(
            $blog_detail->meta_description ?: languageName($blog_detail->description),
            5000
        );
        $postContentText = trim(seo_plain_text(languageName($blog_detail->content), 0));
        preg_match_all('/[\p{L}\p{N}]+/u', $postContentText, $wordMatches);
        $postWordCount = count($wordMatches[0]);
        $postUrl = route('detailBlog', ['slug' => $blog_detail->slug]);
        $homeUrl = route('home');
        $categorySlug = $blog_detail->category;
        $categoryName = $cleanText(
            optional($blog_detail->cate)->name
                ? languageName($blog_detail->cate->name)
                : $categorySlug
        );
        $categoryUrl = route('listCateBlog', ['slug' => $categorySlug]);
        $siteName = $setting->webname ?? ($setting->company ?? 'Website');
        $publisherName = $setting->company ?? $siteName;
        $publisherLogo = !empty($setting->logo) ? url($setting->logo) : url('' . $blog_detail->image);
    @endphp
    <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebSite",
      "@id": {!! json_encode(url('/') . '#website', $jsonFlags) !!},
      "url": {!! json_encode(url('/'), $jsonFlags) !!},
      "name": {!! json_encode($siteName, $jsonFlags) !!},
      "inLanguage": "vi-VN"
    },
    {
      "@type": "Organization",
      "@id": {!! json_encode(url('/') . '#organization', $jsonFlags) !!},
      "name": {!! json_encode($publisherName, $jsonFlags) !!},
      "url": {!! json_encode(url('/'), $jsonFlags) !!},
      "logo": {
        "@type": "ImageObject",
        "url": {!! json_encode($publisherLogo, $jsonFlags) !!}
      }
    },
    {
      "@type": "BreadcrumbList",
      "@id": {!! json_encode($postUrl . '#breadcrumb', $jsonFlags) !!},
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "name": "Trang chủ",
          "item": {!! json_encode($homeUrl, $jsonFlags) !!}
        },
        {
          "@type": "ListItem",
          "position": 2,
          "name": {!! json_encode($categoryName, $jsonFlags) !!},
          "item": {!! json_encode($categoryUrl, $jsonFlags) !!}
        },
        {
          "@type": "ListItem",
          "position": 3,
          "name": {!! json_encode($postTitle, $jsonFlags) !!},
          "item": {!! json_encode($postUrl, $jsonFlags) !!}
        }
      ]
    },
    {
      "@type": "BlogPosting",
      "@id": {!! json_encode($postUrl . '#article', $jsonFlags) !!},
      "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": {!! json_encode($postUrl, $jsonFlags) !!}
      },
      "headline": {!! json_encode($postTitle, $jsonFlags) !!},
      "description": {!! json_encode($postDescription, $jsonFlags) !!},
      "articleSection": {!! json_encode($categoryName, $jsonFlags) !!},
      "keywords": {!! json_encode(implode(', ', $blogTags ?? []), $jsonFlags) !!},
      "inLanguage": "vi-VN",
      "wordCount": {{ $postWordCount }},
      "datePublished": {!! json_encode(optional($blog_detail->created_at)->toIso8601String(), $jsonFlags) !!},
      "dateModified": {!! json_encode(optional($blog_detail->updated_at)->toIso8601String(), $jsonFlags) !!},
      "image": [
        {
          "@type": "ImageObject",
          "url": {!! json_encode(url(''.$blog_detail->image), $jsonFlags) !!}
        }
      ],
      "author": {
        "@type": "Person",
        "name": {!! json_encode($cleanText($blog_detail->author ?: 'Admin'), $jsonFlags) !!}
      },
      "publisher": {
        "@type": "Organization",
        "@id": {!! json_encode(url('/') . '#organization', $jsonFlags) !!}
      }
    }
  ]
}
</script>
@endsection
@section('css')
<link rel="stylesheet" href="{{url('frontend/css/breadcrumb_style.scss.css')}}">
<link rel="stylesheet" href="{{url('frontend/css/paginate.scss.css')}}">
<link rel="stylesheet" href="{{url('frontend/css/blog_article_style.scss.css')}}">
@endsection
@section('js')

@endsection
@section('content')
<div class="bodywrap">

  <section class="bread-crumb">
    <div class="container">
      <ul class="breadcrumb">					
        <li class="home">
          <a href="/"><span>Trang chủ</span></a>						
          <span class="mr_lr">&nbsp;<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-inline--fa fa-chevron-right fa-w-10"><path fill="currentColor" d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z" class=""></path></svg>&nbsp;</span>
        </li>
        @if (!empty($blog_detail->category))
        <li>
          <a href="{{ route('listCateBlog', ['slug' => $blog_detail->category]) }}">
            <span>{{ optional($blog_detail->cate)->name ? languageName($blog_detail->cate->name) : $blog_detail->category }}</span>
          </a>
          <span class="mr_lr">&nbsp;<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-inline--fa fa-chevron-right fa-w-10"><path fill="currentColor" d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z" class=""></path></svg>&nbsp;</span>
        </li>
        @endif
        <li><strong><span>{{languageName($blog_detail->title)}}</span></strong></li>
      </ul>
    </div>
  </section>
<div class="blog_wrapper layout-blog" itemscope itemtype="https://schema.org/BlogPosting">
  <meta itemprop="headline" content="{{ languageName($blog_detail->title) }}">
  <meta itemprop="description" content="{{ seo_plain_text($blog_detail->meta_description ?: languageName($blog_detail->description), 160) }}">
  <div class="container">
    <div class="row article-main">
        <div class="right-content col-lg-9 col-12">
          <div class="article-details clearfix">
            <h1 class="article-title" itemprop="name">{{languageName($blog_detail->title)}}</h1>
            <div class="posts">
              <div class="time-post f">
                
                <svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="clock" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-clock fa-w-16"><path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm216 248c0 118.7-96.1 216-216 216-118.7 0-216-96.1-216-216 0-118.7 96.1-216 216-216 118.7 0 216 96.1 216 216zm-148.9 88.3l-81.2-59c-3.1-2.3-4.9-5.9-4.9-9.7V116c0-6.6 5.4-12 12-12h14c6.6 0 12 5.4 12 12v146.3l70.5 51.3c5.4 3.9 6.5 11.4 2.6 16.8l-8.2 11.3c-3.9 5.3-11.4 6.5-16.8 2.6z" class=""></path></svg>
                {{date_format($blog_detail->created_at,'d/m/Y')}}
              </div>
              <div class="time-post">
                <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="user" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-user fa-w-14"><path fill="currentColor" d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z" class=""></path></svg>
                <span>{{languageName($blog_detail->author ?: 'Admin')}}</span>
              </div>
            </div>
            
            <div class="article-content rte">
                {!!languageName($blog_detail->content)!!}
              </div>
                          <div class="share-group d-flex align-items-center mt-3">
<strong class="share-group__heading mr-3">Chia sẻ</strong>
<div class="share-group__list">
      <a class="share-group__item facebook" title="Chia sẽ lên Facebook" target="_blank" href="http://www.facebook.com/sharer.php?u={{url()->current()}}">
    <img width="100" height="100" src="{{url('frontend/images/icon_face.png')}}" alt="Facebook">
  </a>
          <a class="share-group__item pinterest" title="Chia sẽ lên Pinterest" target="_blank" href="http://pinterest.com/pin/create/button/?url={{url()->current()}}">
    <img width="100" height="100" src="{{url('frontend/images/icon_print.png')}}" alt="Pinterest">
  </a>
          <a class="share-group__item twitter" target="_blank" title="Chia sẽ lên Twitter" href="http://twitter.com/share?text={{url()->current()}}">
    <img width="100" height="100" src="{{url('frontend/images/icon_tw.png')}}" alt="Twitter">
  </a>
    </div>
</div>													</div>
        </div>
        <div class="blog_left_base col-lg-3 col-12">
          <div class="aside-blog-right">
              <div class="blog_noibat">
                <h2 class="h2_sidebar_blog">
                    <a href="" title="Bài viết mới">Bài viết mới</a>
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
