@php
    $r2Base = rtrim(config('filesystems.disks.r2.url') ?? '', '/');
@endphp
@if($r2Base)
<link rel="preconnect" href="{{ $r2Base }}" crossorigin>
<link rel="dns-prefetch" href="{{ $r2Base }}">
@endif
{{-- <link rel="stylesheet" href="{{ r2_asset('frontend/css/bootstrap.min.css') }}"> --}}

<link rel="stylesheet" href="frontend/css/main.css">
