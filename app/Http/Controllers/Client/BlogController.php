<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\blog\Blog;
use App\models\product\Category;
use App\models\blog\BlogCategory;
use App\models\blog\BlogTypeCate;
use App\models\construction\Construction;
use App\models\product\Product;
use Session;

class BlogController extends Controller
{
    private function parseTags($rawTags): array
    {
        if (empty($rawTags)) {
            return [];
        }

        if (is_array($rawTags)) {
            return array_values(array_filter(array_map('trim', $rawTags)));
        }

        $decoded = json_decode((string) $rawTags, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter(array_map('trim', $decoded)));
        }

        return array_values(array_filter(array_map('trim', explode(',', (string) $rawTags))));
    }

    private function extractUniqueTags($blogs, int $limit = 20): array
    {
        return collect($blogs)
            ->flatMap(function ($blog) {
                return $this->parseTags($blog->tags ?? null);
            })
            ->map(function ($tag) {
                return trim((string) $tag);
            })
            ->filter()
            ->unique(function ($tag) {
                return mb_strtolower($tag, 'UTF-8');
            })
            ->take($limit)
            ->values()
            ->all();
    }

    public function list()
    {
        $data['blog'] = Blog::where(['status'=>1])
        ->orderBy('id','DESC')
        ->select(['id','title','image','description','created_at','slug','tags'])
        ->paginate(12);
        $data['popularBlogTags'] = $this->extractUniqueTags(Blog::where('status', 1)->latest('id')->take(200)->get(['tags']));
        $data['title_page'] = 'Tin tức cập nhật';
        return view('blog.list',$data);
    }
    public function listCateBlog($slug)
    {
        $data['blog'] = Blog::where(['status'=>1,'category'=>$slug])
        ->orderBy('id','DESC')
        ->select(['id','title','image','description','created_at','slug','tags'])
        ->paginate(9);
        $cate = BlogCategory::where('slug', $slug)->where('status', 1)->first(['name']);
        if (!$cate) {
            abort(404);
        }
        $data['blognew'] = Blog::where(['status'=>1])->orderBy('id','DESC')->limit(10)->get();
        $data['cate_name'] = languageName($cate->name);
        $data['cate_slug'] = $slug;
        $data['type_name'] = '';
        $data['title_page'] = languageName($cate->name);
        $data['popularBlogTags'] = $this->extractUniqueTags($data['blog']->items());
        return view('blog.listBlogCate',$data);
    }
    public function listTypeBlog($slug)
    {
        $data['blog'] = Blog::where(['status'=>1,'type_cate'=>$slug])
        ->orderBy('id','DESC')
        ->select(['id','title','image','description','created_at','slug','tags'])
        ->paginate(9);
        
        $cate = BlogTypeCate::where('slug', $slug)->first(['name','category_slug']);
        if (!$cate) {
            abort(404);
        }
        $cateBlog = BlogCategory::where('slug', $cate->category_slug)->first(['name']);
        $data['cate_name'] = $cateBlog ? languageName($cateBlog->name) : '';
        $data['cate_slug'] = $cate->category_slug;
        $data['type_name'] = languageName($cate->name);

        $data['title_page'] = languageName($cate->name);
        $data['popularBlogTags'] = $this->extractUniqueTags($data['blog']->items());
        return view('blog.listBlogCate',$data);
    }
    public function listByTag($tag)
    {
        $tag = trim(urldecode((string) $tag));
        if ($tag === '') {
            abort(404);
        }

        $data['blog'] = Blog::where('status', 1)
            ->where('tags', 'LIKE', '%' . $tag . '%')
            ->orderBy('id', 'DESC')
            ->select(['id', 'title', 'image', 'description', 'created_at', 'slug', 'tags'])
            ->paginate(12);

        $data['title_page'] = 'Tag: ' . $tag;
        $data['activeTag'] = $tag;
        $data['popularBlogTags'] = $this->extractUniqueTags(
            Blog::where('status', 1)->latest('id')->take(200)->get(['tags'])
        );
        return view('blog.listBlogTag', $data);
    }
    public function detailBlog($slug)
    {
        $blog = Blog::with('cate')->where(['slug' => $slug, 'status' => 1])->first();
        if (!$blog) {
            abort(404);
        }

        $data['blog_detail'] = $blog;
        $data['bloglq'] = Blog::where(['status' => 1, 'category' => $blog->category])->limit(10)->get();
        $data['blogTags'] = $this->parseTags($blog->tags ?? null);
        $data['blognew'] = Blog::where(['status' => 1])->orderBy('id', 'DESC')->limit(5)->get();
        // $data['prevBlog'] = Blog::where('status', 1)
        //     ->where('id', '<', $blog->id)
        //     ->orderBy('id', 'DESC')
        //     ->first(['id', 'slug', 'title']);
        // $data['nextBlog'] = Blog::where('status', 1)
        //     ->where('id', '>', $blog->id)
        //     ->orderBy('id', 'ASC')
        //     ->first(['id', 'slug', 'title']);
        return view('blog.detail', $data);
    }
    public function search_blog(Request $request)
    {
        $keyword = $request->keyword;
        $code = Session::get('locale');
        $arr = [];
        $arrb = [];
        $arrOpt = [];
        //search option
        $productOpt =  Blog::where('status',1)
        ->get(['title','image','description','created_at','slug','tags'])
        ->toArray();
        foreach($productOpt as $key => $item){
            $fielName = json_decode($item['title']);
            foreach($fielName as $i){
                $matchTitle = strpos(strtolower(stripVN($i->content)), strtolower(stripVN($keyword))) !== false && $i->lang_code == $code;
                $matchTag = false;
                foreach ($this->parseTags($item['tags'] ?? null) as $tag) {
                    if (strpos(strtolower(stripVN($tag)), strtolower(stripVN($keyword))) !== false) {
                        $matchTag = true;
                        break;
                    }
                }
                if($matchTitle || $matchTag){
                    array_push($arr,$productOpt[$key]);
                }
            }
        }
        $data['keyword'] = $request->keyword;
        $data['countproduct'] = count($arr);
        $data['resultPro'] = $arr;
        return view('search_result',$data);
    }
}
