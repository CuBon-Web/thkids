<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\models\product\Category;
use App\models\product\Product;
use App\models\blog\Blog;
use Session;
use App\models\website\Partner;
use App\models\website\WhyChoose;
use App\models\blog\BlogCategory;
use App\models\BannerAds;
use App\models\website\Video;
use App\models\website\Founder;
use App\models\website\Prize;
use App\models\website\AlbumAffter;
use App\models\ReviewCus;
use App\models\PageContent;
use App\models\Services;
use App\models\Project;
use App\models\website\ProcessStep;
use App\models\website\Faq;

class HomeController extends Controller
{
    public function home()
    {
        // $data['blogCateForBlog'] = BlogCategory::where('status', 1)
        //     ->orderBy('id', 'DESC')
        //     ->get()
        //     ->each(function ($cate) {
        //         $cate->setRelation(
        //             'listBlog',
        //             $cate->listBlog()->where('status', 1)->limit(8)->get()
        //         );
        //     });
        $data['hotnews'] = Blog::where([
            ['status','=',1]
        ])->orderBy('id','DESC')->limit(8)->get(['id','title','slug','created_at','image','description','author']);
        $data['gioithieu'] = PageContent::where('slug', 'gioi-thieu')
            ->whereIn('language', [app()->getLocale(), 'en', 'vi'])
            ->orderByRaw("FIELD(language, ?, 'en', 'vi')", [app()->getLocale()])
            ->first(['id', 'title', 'description', 'image']);
        $data['whyChoose'] = WhyChoose::where('status', 1)->orderBy('sort')->get();
        $data['service'] = Services::where(['status'=>1,'status_home'=>1])->get();
        $data['bannerads'] = BannerAds::where(['status'=>1,'status_show'=>'banner_ads'])->get();
        $data['ReviewCus'] = ReviewCus::where(['status'=>1])->get();
        $data['Partner'] = Partner::where(['status'=>1])->get();
        $data['duan'] = Project::where('status',1)->where('show_home',1)->with('cateProject')->limit(10)->get();
        $data['processSteps'] = ProcessStep::where('status',1)->orderBy('sort')->orderBy('id')->get();
        $data['homeFaqs'] = Faq::where('status', 1)->orderBy('sort')->orderBy('id')->get();
        $data['gallery'] = AlbumAffter::where('status',1)->orderBy('sort')->get();
        $data['homePro'] = Product::where(['status'=>1,'discountStatus'=>1])
            ->select('id','category','name','discount','price','images','slug','cate_slug','type_slug','description','status_variant','discountStatus','home_status','qty')
            ->with(['cate:id,slug,name'])
            ->orderBy('id','DESC')
            ->limit(18)->get();
        $this->attachVariantPriceRange($data['homePro']);
        return view('home',$data);
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

        $ranges = \App\models\VariantSkuValue::query()
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
