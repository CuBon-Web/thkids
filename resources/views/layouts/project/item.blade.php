@php
    $projectImages = json_decode($item->images ?? '[]', true) ?: [];
    $projectThumb = $projectImages[0] ?? '';
@endphp
<a href="{{ route('duanTieuBieuDetail', $item->slug) }}" class="wptb-item--inner project-item-link">
    <div class="wptb-item--image project-thumb-image">
        @if($projectThumb)
        {!! lazy_img($projectThumb, $item->name) !!}
        @endif
    </div>
    <div class="wptb-item--holder">
        <div class="wptb-item--meta">
            <h4 class="text-white">{{ $item->name }}</h4>
            <p>{{ $item->cateProject->name ?? '' }}</p>
        </div>
    </div>
</a>
