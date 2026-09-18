<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\product\Product;
use App\models\product\Category;
use App\models\blog\Blog;
use App\models\product\TypeProduct;
use App\models\construction\Construction;
use  App\models\product\TypeProductTwo;
use Session;
use App\models\tag\Tags;
use App\models\tag\TagCate;
use App\models\VariantSkuValue;

class ProductController extends Controller
{
    public function allProduct()
    {
        $data['list'] = Product::where('status',1)->orderBy('id','DESC')->select('id','category','name','discount','price','images','slug','cate_slug','type_slug','status_variant','tags')
        ->paginate(12);
        $data['filter'] = TagCate::with(['tags'])->where('status', 1)->get();
        $data['title'] = "Tất cả sản phẩm";
        $data['content'] = 'none';
        return view('product.list',$data);
    }
    public function flashSale()  {
        $data['list'] = Product::where(['status'=>1,'discountStatus'=>1])
        ->orderBy('id','DESC')
        ->select('id','category','name','discount','price','images','slug','cate_slug','type_slug','description','status_variant','discountStatus','home_status')
        ->paginate(20);
        return view('product.flash_sale',$data);
    }
    public function allListCate($danhmuc)
    {
        $data['list'] = Product::where(['status'=>1,'cate_slug'=>$danhmuc])
        ->orderBy('id','DESC')
        ->select('id','category','name','discount','price','images','slug','cate_slug','type_slug','description','status_variant','tags')
        ->paginate(20);
        $data['cateno'] = Category::where('slug',$danhmuc)->first(['id','name','avatar','content','slug']);
        $data['filter'] = TagCate::with(['tags'])->where('cate_product_id',$data['cateno']->id)->get();

        $data['cate_slug'] = $data['cateno']->slug;
        $data['type_slug'] = "";
        $data['type_two_slug'] = "";

        $data['cate_name'] = languageName($data['cateno']->name);
        $data['type_name'] = "";
        $data['type_two_name'] = "";
        $data['image'] = $data['cateno']->imagehome;
        $data['title'] = languageName($data['cateno']->name);
        $data['content'] = $data['cateno']->content;
        return view('product.list',$data);
    }
    public function allListType(Request $request, $danhmuc, $loaidanhmuc){
        if ($request->query('view') === 'ajaxload4') {
            return $this->homeTabAjax($danhmuc, $loaidanhmuc);
        }

        $data['list'] = Product::where(['status'=>1,'cate_slug'=>$danhmuc,'type_slug'=>$loaidanhmuc])
            ->orderBy('id','DESC')
            ->select('id','category','name','discount','price','images','slug','cate_slug','type_slug','description','status_variant','tags')
            ->paginate(20);
        $data['type'] = TypeProduct::where('slug',$loaidanhmuc)->first(['id','name','cate_id','content','slug']);
        $cate_id = $data['type']->cate_id;
        $data['cateno'] = Category::where('slug',$danhmuc)->first(['id','name','avatar','content','slug']);
        $data['filter'] = TagCate::with(['tags'])->where('cate_product_id',$data['cateno']->id)->get();
        
        $data['cate_slug'] = $data['cateno']->slug;
        $data['type_slug'] = $data['type']->slug;
        $data['type_two_slug'] = "";

        $data['cate_name'] = languageName($data['cateno']->name);
        $data['type_name'] = languageName($data['type']->name);
        $data['type_two_name'] = "";

        $data['title'] = languageName($data['type']->name);
        $data['content'] = $data['type']->content;
        return view('product.list',$data);
    }
    public function allListTypeTwo($danhmuc,$loaidanhmuc,$thuonghieu){
        $data['list'] = Product::where(['status'=>1,'cate_slug'=>$danhmuc,'type_slug'=>$loaidanhmuc,'type_two_slug'=>$thuonghieu])
            ->orderBy('id','DESC')
            ->select('id','category','name','discount','price','images','slug','cate_slug','type_slug','description','status_variant','tags')
            ->paginate(20);
        $data['typetwo'] = TypeProductTwo::where('slug',$thuonghieu)->first(['id','name','cate_id','content','slug']);
        $data['cateno'] = Category::where('slug',$danhmuc)->first(['id','name','avatar','content','slug']);
        $data['type'] = TypeProduct::where('slug',$loaidanhmuc)->first(['id','name','cate_id','content','slug']);

        $data['cate_slug'] = $data['cateno']->slug;
        $data['type_slug'] = $data['type']->slug;
        $data['type_two_slug'] = $data['typetwo']->slug;

        $data['cate_name'] = languageName($data['cateno']->name);
        $data['type_name'] = languageName($data['type']->name);
        $data['type_two_name'] = languageName($data['typetwo']->name);

        $data['filter'] = TagCate::with(['tags'])->where('cate_product_id',$data['cateno']->id)->get();
        $data['title'] = languageName($data['typetwo']->name);
        $data['content'] = $data['typetwo']->content;
        return view('product.list',$data);
    }
    public function tagCateList($tagCateSlug){

        $tagCate = TagCate::where('slug',$tagCateSlug)->first();

        $data['list'] = Product::where('status',1)
            ->where('tags', 'LIKE', '%'.$tagCateSlug.'%')
            ->paginate(12);
            $data['cateno'] = Category::where('id',$tagCate->cate_product_id)->first(['id','name','avatar','content','slug']);
            $data['filter'] = TagCate::with(['tags'])->where('cate_product_id',$data['cateno']->id)->get();
            $cate_id = $data['cateno']->id;
            $data['cate_slug'] = $data['cateno']->slug;
            $data['type_slug'] = "";
            $data['type_two_slug'] = "";
            
            $data['tag_cate'] = $tagCate->name;
            $data['tag_cate_slug'] = $tagCateSlug;
            $data['tag_name'] = '';

            $data['cate_name'] = '';
            $data['type_name'] = '';
            $data['type_two_name'] = '';

            $data['cateid'] = $cate_id;
            $data['title'] = $tagCate->name;
            $data['content'] = "";
        return view('product.list',$data);

    }
    public function tag($tag)
    {
            $tag = Tags::where('slug',$tag)->first();
            $tagCate = TagCate::where('id',$tag->cate_tag_id)->first();
            $keysearch = $tag->slug.'-'.$tagCate->slug;
            $data['list'] = Product::where('status',1)
            ->whereJsonContains('tags', $keysearch)
            ->paginate(12);
            $data['cateno'] = Category::where('id',$tag->cate_product_id)->first(['id','name','avatar','content','slug']);
            $data['filter'] = TagCate::with(['tags'])->where('cate_product_id',$data['cateno']->id)->get();
            $cate_id = $data['cateno']->id;
            $data['cate_slug'] = $data['cateno']->slug;
            $data['type_slug'] = "";
            $data['type_two_slug'] = "";

            $data['tag_cate'] = $tagCate->name;
            $data['tag_cate_slug'] = $tagCate->slug;
            $data['tag_name'] = $tag->name;
            $data['tag_slug'] = $tag->slug;
            $data['cate_name'] = '';
            $data['type_name'] = '';
            $data['type_two_name'] = '';

            $data['cateid'] = $cate_id;
            $data['title'] = $tag->name;
            $data['content'] = "";
        return view('product.list',$data);
    }
    public function CateProList($cate)
    {
        $data['list'] = Product::with('customer','cate')
        ->where('category',$cate)
        ->orderBy('id','ASC')
        ->paginate(12);
        $data['cate'] = Category::where('id',$cate)->first();
        return view('product.list',$data);
    }
    public function TypeProList($type)
    {
        $data['list'] = Product::with('customer','cate')
        ->where('type_cate',$type)
        ->orderBy('id','ASC')
        ->paginate(12);
        $cate = TypeProduct::where('id',$type)->first();
        $data['title_page'] = languageName($cate->name);
        return view('product.list',$data);
    }
    public function getproajax(Request $request)
    {
        if($request->cate == "all"){
            $product = Product::where([
                ['status', '=', 1]
            ])->limit(9)->orderBy('id','ASC')->get(['id','name','discount','price','images','status_variant']);
        }else{
            $product =  Product::where(['status'=>1,'category'=>$request->cate])
            ->orderBy('id','DESC')
            ->select('id','category','name','discount','price','images')
            ->get();
        }
        $view = view("layouts.product.getproajax",compact('product'))->render();
        return response()->json(['html'=>$view]);
    }
    public function filterProduct(Request $request)
    {
        $product = Product::query();

        if( isset($request->fillter) ){
            foreach($request->fillter as $item){
                $product = $product->orWhereJsonContains('tags', $item);
            }
        }
        if($request->keyword){
            $product = $product->where('name', 'LIKE', '%'.$request->keyword.'%');
        }

        // if(isset($request->pricemin) && isset($request->pricemax)){
        //     $product = $product->whereBetween('price', [$request->pricemin, $request->pricemax]);
        // }

        

        
        if(isset($request->sortby)){
            if($request->sortby == "price-asc"){
                $product = $product->orderBy('price','ASC');
            }elseif($request->sortby == "price-desc"){
                $product = $product->orderBy('price','DESC');
            }elseif($request->sortby =="created-oldest"){
                $product = $product->orderBy('id','ASC');
            }elseif($request->sortby =="created-desc"){
                $product = $product->orderBy('id','DESC');
            }else{
                $product = $product; 
            }
        }

        if($request->cate ){
            // dd(2);
            $product = $product->where('cate_slug',$request->cate);
        }
        if( $request->type != null ){
            // dd(1);
            $product = $product->where('type_slug',$request->type);
        }
        if($request->typetwo != null ){
            // dd(3);
            $product = $product->where('type_two_slug',$request->typetwo);
        }
        if(isset($request->pricemin) && $request->pricemin !== null && $request->pricemin !== ''){
            $product = $product->where('discount', '>=', (float) $request->pricemin);
        }
        if(isset($request->pricemax) && $request->pricemax !== null && $request->pricemax !== ''){
            $product = $product->where('discount', '<=', (float) $request->pricemax);
        }

        $product = $product
            ->where('status',1)
            ->with(['cate:id,name,slug'])
            ->select('id','category','name','discount','price','images','slug','cate_slug','type_slug','description','status_variant','tags')
            ->paginate(20);

        $itemsHtml = view("layouts.product.filter_grid_items",compact('product'))->render();
        $paginationHtml = $product->links()->render();
        return response()->json([
            'items_html' => $itemsHtml,
            'pagination_html' => $paginationHtml,
            'total' => $product->total(),
            'count' => $product->count()
        ]);
    }

    private function prepareProductVariants(Product $product)
    {
        $variants = [];
        $variantOptions = [];
        $originalPrice = (float) $product->price;
        $salePrice = (float) $product->discount;
        $variantMinPrice = null;
        $variantMaxPrice = null;

        if ((int) $product->status_variant === 1) {
            $variantRows = VariantSkuValue::where('product_id', $product->id)
                ->orderBy('id', 'ASC')
                ->get(['id', 'version', 'price']);

            $variants = $variantRows->map(function ($row) {
                return [
                    'id' => $row->id,
                    'version' => $row->version,
                    'price' => (float) $row->price,
                ];
            })->values()->all();

            $variantMinPrice = $variantRows->min('price');
            $variantMaxPrice = $variantRows->max('price');
            if (!is_null($variantMinPrice)) {
                $salePrice = (float) $variantMinPrice;
            }

            $rawVariant = json_decode((string) $product->variant, true);
            if (is_array($rawVariant)) {
                $variantOptions = collect($rawVariant)->map(function ($option) {
                    $labels = collect($option['option_values'] ?? [])->pluck('label')->filter()->values()->all();
                    return [
                        'name' => $option['display_name'] ?? '',
                        'values' => $labels,
                    ];
                })->filter(function ($option) {
                    return !empty($option['name']) && !empty($option['values']);
                })->values()->all();
            }

            if (empty($variantOptions) && !empty($variants)) {
                $variantOptions = [[
                    'name' => 'Phân loại',
                    'values' => collect($variants)->pluck('version')->filter()->unique()->values()->all(),
                ]];
            }
        }

        return compact('variants', 'variantOptions', 'originalPrice', 'salePrice', 'variantMinPrice', 'variantMaxPrice');
    }

    public function detail_product($cate,$type,$id)
    {
        $data['product'] = Product::with([
            'typecatetwo' => function ($query) {
                $query->select('id', 'name','avatar','slug'); 
            },
            'typeCate' => function ($query) {
                $query->select('id', 'name','avatar','slug'); 
            },
            'cate' => function ($query) {
                $query->where('status',1)->limit(5)->select('id','name','avatar','slug'); 
            },
        ])->where('slug', $id)->where('status', 1)->first(['id','name','images','type_cate','type_two','category','sku','discount','price','content','size','description','slug','preserve','status_variant','created_at','species','variant','cate_slug','type_slug','qty','preserve','seo_title','meta_description','focus_keyword']);

        if (!$data['product']) {
            abort(404);
        }

        // Canonicalize product URL to avoid duplicate indexable paths.
        $correctCate = trim((string) ($data['product']->cate_slug ?: 'san-pham'), '/');
        $correctType = trim((string) ($data['product']->type_slug ?: 'loai'), '/');
        if ($cate !== $correctCate || $type !== $correctType) {
            return redirect()->route('detailProduct', [
                'cate' => $correctCate,
                'type' => $correctType,
                'id' => $data['product']->slug,
            ], 301);
        }

        $variantData = $this->prepareProductVariants($data['product']);
        $data = array_merge($data, $variantData);
        $data['productlq'] = Product::where('category',$data['product']->category)
            ->where('id', '!=', $data['product']->id)
            ->limit(8)
            ->with(['cate:id,name,slug'])
            ->get(['id','category','name','status_variant','discount','price','images','slug','cate_slug','type_slug','description']);

        return view('product.detail',$data);
    }
    public function autosearchcomplete(Request $request)
    {
        $keyword = trim((string) ($request->keyword ?? $request->keywordsearch ?? $request->input('query') ?? $request->q ?? ''));
        if ($keyword === '') {
            return response()->json(['html' => '', 'count' => 0]);
        }

        $baseQuery = Product::where('status', 1)
            ->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('slug', 'LIKE', '%' . $keyword . '%');
            });

        $data = (clone $baseQuery)
            ->select('id', 'category', 'name', 'discount', 'price', 'images', 'slug', 'cate_slug', 'type_slug', 'description', 'status_variant')
            ->with(['cate:id,slug,name'])
            ->orderBy('id', 'DESC')
            ->limit(8)
            ->get();

        $this->attachVariantPriceRange($data);

        $view = view('layouts.product.search_render', compact('data', 'keyword'))->render();

        return response()->json([
            'html' => $view,
            'count' => (clone $baseQuery)->count(),
            'keyword' => $keyword,
        ]);
    }
    private function compareSidebarPayload($message = 'success')
    {
        $list = session()->get('compareProduct', []);
        return [
            'html' => view('layouts.product.compare-sidebar-items', compact('list'))->render(),
            'qty' => count($list),
            'slugs' => collect($list)->pluck('pro_slug')->values()->all(),
            'message' => $message,
        ];
    }

    private function buildCompareItem(Product $product, $id)
    {
        $images = json_decode($product->images, true) ?? [];

        return [
            'idpro' => $id,
            'name' => $product->name,
            'image' => $images[0] ?? '',
            'status_variant' => $product->status_variant,
            'price' => $product->price,
            'size' => json_decode($product->size),
            'discount' => $product->discount,
            'cate_slug' => $product->cate_slug,
            'type_slug' => $product->type_slug,
            'pro_slug' => $product->slug,
            'cate' => $product->category,
        ];
    }

    public function compare(Request $request)
    {
        $id = $request->id;
        $product = Product::where('id', $id)->first(['id', 'name', 'images', 'category', 'discount', 'price', 'size', 'cate_slug', 'status_variant', 'type_slug', 'slug']);

        if (!$product) {
            return response()->json([
                'message' => 'error',
                'html' => '',
                'qty' => 0,
                'slugs' => [],
            ], 404);
        }

        $compare = session()->get('compareProduct', []);

        if (isset($compare[$id])) {
            return response()->json($this->compareSidebarPayload('exist'));
        }

        if (!empty($compare)) {
            foreach ($compare as $val) {
                if ($product->category != $val['cate']) {
                    return response()->json($this->compareSidebarPayload('error'));
                }
            }
            if (count($compare) >= 3) {
                return response()->json($this->compareSidebarPayload('limit3'));
            }
        }

        $compare[$id] = $this->buildCompareItem($product, $id);
        session()->put('compareProduct', $compare);

        return response()->json($this->compareSidebarPayload('success'));
    }

    public function removeCompare(Request $request)
    {
        if ($request->id) {
            $compare = session()->get('compareProduct', []);
            if (isset($compare[$request->id])) {
                unset($compare[$request->id]);
                session()->put('compareProduct', $compare);
            }
            return response()->json($this->compareSidebarPayload('removed'));
        }

        return response()->json($this->compareSidebarPayload('error'));
    }
    public function compareList()
    {
        $list = collect(session()->get('compareProduct', []))->values();
        $categories = Category::whereIn('id', $list->pluck('cate')->filter()->unique()->all())
            ->get(['id', 'name'])
            ->keyBy('id');

        $items = $list->map(function ($item) use ($categories) {
            $item = (array) $item;
            $category = $categories->get($item['cate'] ?? null);
            $originalPrice = (float) ($item['price'] ?? 0);
            $salePrice = (float) ($item['discount'] ?? 0);

            if ((int) ($item['status_variant'] ?? 0) === 1 && !empty($item['idpro'])) {
                $variantMin = VariantSkuValue::where('product_id', $item['idpro'])->min('price');
                if (!is_null($variantMin)) {
                    $salePrice = (float) $variantMin;
                }
            }

            $specs = [];
            if (!empty($item['size'])) {
                $rawSpecs = is_array($item['size']) ? $item['size'] : json_decode(json_encode($item['size']), true);
                if (is_array($rawSpecs)) {
                    foreach ($rawSpecs as $spec) {
                        $spec = (array) $spec;
                        $title = trim((string) ($spec['title'] ?? ''));
                        $detail = trim((string) ($spec['detail'] ?? ''));
                        if ($title !== '' || $detail !== '') {
                            $specs[] = ['title' => $title, 'detail' => $detail];
                        }
                    }
                }
            }

            $item['category_name'] = $category ? languageName($category->name) : 'Đang cập nhật';
            $item['product_name'] = languageName($item['name'] ?? '');
            $item['product_url'] = route('detailProduct', [
                'cate' => $item['cate_slug'] ?? '',
                'type' => !empty($item['type_slug']) ? $item['type_slug'] : 'loai',
                'id' => $item['pro_slug'] ?? '',
            ]);
            $item['sale_price'] = $salePrice;
            $item['original_price'] = $originalPrice;
            $item['specs'] = $specs;

            return $item;
        })->all();

        return view('compare.product', [
            'list' => $items,
            'maxSlots' => 3,
        ]);
    }
    public function searchResult(Request $request)
    {
        $keyword = trim((string) ($request->keyword ?? $request->keywordsearch ?? $request->input('query') ?? $request->q ?? ''));
        if ($keyword === '') {
            return redirect()->route('home');
        }

        $data['product'] = Product::where('status', 1)
            ->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('slug', 'LIKE', '%' . $keyword . '%');
            })
            ->with(['cate:id,slug,name'])
            ->orderBy('id', 'DESC')
            ->paginate(18)
            ->appends(['keyword' => $keyword]);

        $this->attachVariantPriceRange($data['product']);
        $data['keyword'] = $keyword;

        return view('product.search_result', $data);
    }
    public function getSku(Request $request)
    {
        $data = VariantSkuValue::where(['product_id'=>$request->id,'version'=>$request->value])->first();
        return response()->json([
            'data'=> $data,
            'message' => 'success'
        ]);
    }

    private function homeTabAjax($danhmuc, $loaidanhmuc)
    {
        $list = Product::query()
            ->select('id', 'category', 'name', 'discount', 'price', 'images', 'slug', 'cate_slug', 'type_slug', 'status_variant', 'discountStatus', 'home_status')
            ->where('status', 1)
            ->where('home_status', 1)
            ->where('cate_slug', $danhmuc)
            ->where('type_slug', $loaidanhmuc)
            ->with(['cate:id,slug,name'])
            ->orderBy('id', 'DESC')
            ->take(8)
            ->get();

        $this->attachVariantPriceRange($list);

        return view('layouts.product.home_tab_ajax', [
            'list' => $list,
            'cate_slug' => $danhmuc,
            'type_slug' => $loaidanhmuc,
        ]);
    }

    private function attachVariantPriceRange($products): void
    {
        $products = collect($products);
        if ($products->isEmpty()) {
            return;
        }

        $variantProductIds = $products
            ->where('status_variant', 1)
            ->pluck('id')
            ->filter()
            ->values()
            ->all();

        if (empty($variantProductIds)) {
            return;
        }

        $ranges = VariantSkuValue::query()
            ->selectRaw('product_id, MIN(price) as min_price, MAX(price) as max_price')
            ->whereIn('product_id', $variantProductIds)
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        foreach ($products as $product) {
            $range = $ranges->get($product->id);
            $product->variant_min_price = $range ? (float) $range->min_price : null;
            $product->variant_max_price = $range ? (float) $range->max_price : null;
        }
    }
}
