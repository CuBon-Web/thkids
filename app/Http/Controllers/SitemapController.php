<?php

namespace App\Http\Controllers;

use App\models\blog\Blog;
use App\models\blog\BlogCategory;
use App\models\blog\BlogTypeCate;
use App\models\product\Category;
use App\models\product\Product;
use App\models\product\TypeProduct;
use App\models\Project;
use App\models\ProjectCate;
use App\models\Services;
use App\models\ServiceCate;
use App\models\PageContent;

class SitemapController extends Controller
{
    public function index()
    {
        $blogLastmod = Blog::max('updated_at');
        $productLastmod = Product::max('updated_at');
        $productCateLastmod = Category::max('updated_at');
        $productTypeLastmod = TypeProduct::max('updated_at');
        $blogTypeLastmod = BlogTypeCate::max('updated_at');
        $serviceCateLastmod = ServiceCate::max('updated_at');
        $serviceLastmod = Services::max('updated_at');
        $projectCateLastmod = ProjectCate::max('updated_at');
        $projectLastmod = Project::max('updated_at');

        $items = [
            [
                'loc' => url('/sitemaps/static.xml'),
                'lastmod' => now()->toAtomString(),
            ],
            [
                'loc' => url('/sitemaps/blog.xml'),
                'lastmod' => $this->toAtomOrNow($blogLastmod),
            ],
            [
                'loc' => url('/sitemaps/blog-type.xml'),
                'lastmod' => $this->toAtomOrNow($blogTypeLastmod),
            ],
            [
                'loc' => url('/sitemaps/product-cate.xml'),
                'lastmod' => $this->toAtomOrNow($productCateLastmod),
            ],
            [
                'loc' => url('/sitemaps/product-type.xml'),
                'lastmod' => $this->toAtomOrNow($productTypeLastmod),
            ],
            [
                'loc' => url('/sitemaps/product.xml'),
                'lastmod' => $this->toAtomOrNow($productLastmod),
            ],
            // [
            //     'loc' => url('/sitemaps/service-cate.xml'),
            //     'lastmod' => $this->toAtomOrNow($serviceCateLastmod),
            // ],
            // [
            //     'loc' => url('/sitemaps/service.xml'),
            //     'lastmod' => $this->toAtomOrNow($serviceLastmod),
            // ],
            // [
            //     'loc' => url('/sitemaps/project-cate.xml'),
            //     'lastmod' => $this->toAtomOrNow($projectCateLastmod),
            // ],
            // [
            //     'loc' => url('/sitemaps/project.xml'),
            //     'lastmod' => $this->toAtomOrNow($projectLastmod),
            // ],
        ];

        $xml = view('sitemaps.index', compact('items'))->render();
        return $this->xmlResponse($xml);
    }

    public function staticPages()
    {
        $urls = [
            [
                'loc' => url('/'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'loc' => route('allProduct'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'loc' => route('allListBlog'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.8',
            ],
            [
                'loc' => route('flashSale'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.8',
            ],
            [
                'loc' => route('aboutUs'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ],
            [
                'loc' => route('priceList'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ],
            [
                'loc' => route('lienHe'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ],
            [
                'loc' => route('duanTieuBieu'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'loc' => route('fag'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ],
        ];

        $blogCateUrls = BlogCategory::where('status', 1)
            ->whereNotNull('slug')
            ->where('slug', '<>', '')
            ->orderBy('id', 'desc')
            ->get(['slug'])
            ->map(function ($cate) {
                return [
                    'loc' => route('listCateBlog', ['slug' => $this->normalizeSlug($cate->slug)]),
                    'lastmod' => now()->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ];
            })
            ->all();

        $pageContentUrls = PageContent::where('status', 1)
            ->where('language', app()->getLocale())
            ->whereNotNull('slug')
            ->where('slug', '<>', '')
            ->orderBy('id', 'desc')
            ->get(['slug'])
            ->map(function ($page) {
                return [
                    'loc' => route('pagecontent', ['slug' => $this->normalizeSlug($page->slug)]),
                    'lastmod' => now()->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.5',
                ];
            })
            ->all();

        $urls = array_merge($urls, $blogCateUrls, $pageContentUrls);

        $xml = view('sitemaps.urlset', compact('urls'))->render();
        return $this->xmlResponse($xml);
    }

    public function blog()
    {
        $blogs = Blog::where('status', 1)
            ->whereNotNull('slug')
            ->where('slug', '<>', '')
            ->orderBy('updated_at', 'desc')
            ->get(['slug', 'updated_at']);

        $urls = $blogs->map(function ($blog) {
            return [
                'loc' => route('detailBlog', ['slug' => $this->normalizeSlug($blog->slug)]),
                'lastmod' => optional($blog->updated_at)->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        })->values()->all();

        $xml = view('sitemaps.urlset', compact('urls'))->render();
        return $this->xmlResponse($xml);
    }

    public function productCate()
    {
        $categories = Category::where('status', 1)
            ->whereNotNull('slug')
            ->where('slug', '<>', '')
            ->orderBy('updated_at', 'desc')
            ->get(['slug', 'updated_at']);

        $urls = $categories->map(function ($cate) {
            return [
                'loc' => route('allListProCate', ['danhmuc' => $this->normalizeSlug($cate->slug)]),
                'lastmod' => optional($cate->updated_at)->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        })->values()->all();

        $xml = view('sitemaps.urlset', compact('urls'))->render();
        return $this->xmlResponse($xml);
    }

    public function productType()
    {
        $types = TypeProduct::where('status', 1)
            ->whereNotNull('slug')
            ->where('slug', '<>', '')
            ->whereNotNull('cate_slug')
            ->where('cate_slug', '<>', '')
            ->orderBy('updated_at', 'desc')
            ->get(['slug', 'cate_slug', 'updated_at']);

        $urls = $types->map(function ($type) {
            return [
                'loc' => route('allListType', [
                    'danhmuc' => $this->normalizeSlug($type->cate_slug),
                    'loaidanhmuc' => $this->normalizeSlug($type->slug),
                ]),
                'lastmod' => optional($type->updated_at)->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.75',
            ];
        })->values()->all();

        $xml = view('sitemaps.urlset', compact('urls'))->render();
        return $this->xmlResponse($xml);
    }

    public function blogType()
    {
        $types = BlogTypeCate::where('status', 1)
            ->whereNotNull('slug')
            ->where('slug', '<>', '')
            ->orderBy('updated_at', 'desc')
            ->get(['slug', 'updated_at']);

        $urls = $types->map(function ($type) {
            return [
                'loc' => route('listTypeBlog', ['slug' => $this->normalizeSlug($type->slug)]),
                'lastmod' => optional($type->updated_at)->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.65',
            ];
        })->values()->all();

        $xml = view('sitemaps.urlset', compact('urls'))->render();
        return $this->xmlResponse($xml);
    }

    public function product()
    {
        $products = Product::where('status', 1)
            ->whereNotNull('slug')
            ->where('slug', '<>', '')
            ->orderBy('updated_at', 'desc')
            ->get(['slug', 'cate_slug', 'type_slug', 'updated_at']);

        $urls = $products->map(function ($product) {
            return [
                'loc' => route('detailProduct', [
                    'cate' => $product->cate_slug ?: 'san-pham',
                    'type' => $product->type_slug ?: 'loai',
                    'id' => $product->slug,
                ]),
                'lastmod' => optional($product->updated_at)->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        })->values()->all();

        $xml = view('sitemaps.urlset', compact('urls'))->render();
        return $this->xmlResponse($xml);
    }

    public function serviceCate()
    {
        $categories = ServiceCate::where('status', 1)
            ->whereNotNull('slug')
            ->where('slug', '<>', '')
            ->orderBy('updated_at', 'desc')
            ->get(['slug', 'updated_at']);

        $urls = $categories->map(function ($cate) {
            return [
                'loc' => route('serviceList', ['slug' => $this->normalizeSlug($cate->slug)]),
                'lastmod' => optional($cate->updated_at)->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        })->values()->all();

        $xml = view('sitemaps.urlset', compact('urls'))->render();
        return $this->xmlResponse($xml);
    }

    public function service()
    {
        $services = Services::where('status', 1)
            ->whereNotNull('slug')
            ->where('slug', '<>', '')
            ->whereNotNull('cate_slug')
            ->where('cate_slug', '<>', '')
            ->orderBy('updated_at', 'desc')
            ->get(['slug', 'cate_slug', 'updated_at']);

        $urls = $services->map(function ($service) {
            return [
                'loc' => route('serviceDetail', [
                    'danhmuc' => $this->normalizeSlug($service->cate_slug),
                    'slug' => $this->normalizeSlug($service->slug),
                ]),
                'lastmod' => optional($service->updated_at)->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        })->values()->all();

        $xml = view('sitemaps.urlset', compact('urls'))->render();
        return $this->xmlResponse($xml);
    }

    public function projectCate()
    {
        $categories = ProjectCate::where('status', 1)
            ->whereNotNull('slug')
            ->where('slug', '<>', '')
            ->orderBy('updated_at', 'desc')
            ->get(['slug', 'updated_at']);

        $urls = $categories->map(function ($cate) {
            return [
                'loc' => route('projectCategory', ['slug' => $this->normalizeSlug($cate->slug)]),
                'lastmod' => optional($cate->updated_at)->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        })->values()->all();

        $xml = view('sitemaps.urlset', compact('urls'))->render();
        return $this->xmlResponse($xml);
    }

    public function project()
    {
        $projects = Project::where('status', 1)
            ->whereNotNull('slug')
            ->where('slug', '<>', '')
            ->orderBy('updated_at', 'desc')
            ->get(['slug', 'updated_at']);

        $urls = $projects->map(function ($project) {
            return [
                'loc' => route('duanTieuBieuDetail', ['slug' => $this->normalizeSlug($project->slug)]),
                'lastmod' => optional($project->updated_at)->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        })->values()->all();

        $xml = view('sitemaps.urlset', compact('urls'))->render();
        return $this->xmlResponse($xml);
    }

    private function xmlResponse($xml)
    {
        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function toAtomOrNow($value)
    {
        if (!$value) {
            return now()->toAtomString();
        }
        return date('c', strtotime($value));
    }

    private function normalizeSlug($slug)
    {
        return trim((string) $slug, " \t\n\r\0\x0B/");
    }
}
