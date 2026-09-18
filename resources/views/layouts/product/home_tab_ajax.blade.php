<div class="row row-fix">
    @forelse ($list as $pro)
    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-6 col-fix">
        @include('layouts.product.item', ['pro' => $pro])
    </div>
    @empty
    <div class="col-12">
        <p class="text-center mb-0">Chưa có sản phẩm.</p>
    </div>
    @endforelse
</div>
<div class="see-more">
    <a title="Xem tất cả" href="{{ route('allListType', ['danhmuc' => $cate_slug, 'loaidanhmuc' => $type_slug]) }}">Xem tất cả</a>
</div>
