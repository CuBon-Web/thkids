<?php

namespace App\Http\Controllers\Api\Blog;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\blog\Blog;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    private function blogListRelations()
    {
        return [
            'cate' => function ($query) {
                $query->select('id', 'name', 'slug');
            },
            'typeCate' => function ($query) {
                $query->select('id', 'name', 'slug');
            },
        ];
    }

    private function blogListColumns()
    {
        return ['id', 'title', 'created_at', 'category', 'type_cate', 'type_news', 'status', 'slug', 'tags'];
    }
    public function create(Request $request, Blog $blog)
    {
    	$data = $blog->saveBlog($request);
        return response()->json([
    		'message' => 'Save Success',
    		'data'=> $data
    	],200);
    }
    public function list(Request $request)
    {
        $keyword = $request->keyword ?? '';
        $query = Blog::with($this->blogListRelations())->orderBy('id', 'DESC');

        if ($keyword !== '') {
            $query->where('title', 'LIKE', '%' . $keyword . '%');
        }

        $data = $query->get($this->blogListColumns());

        return response()->json([
            'data' => $data,
            'message' => 'success',
        ]);
    }

    public function delete($id)
    {
        $query = Blog::find($id);
        if (!$query) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $this->removeBlogImage($query);
        $query->delete();

        return response()->json(['message' => 'Delete Success'], 200);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids ?? [];
        if (!is_array($ids) || count($ids) === 0) {
            return response()->json(['message' => 'Danh sách bài viết trống'], 422);
        }

        foreach ($ids as $id) {
            $blog = Blog::find($id);
            if (!$blog) {
                continue;
            }
            $this->removeBlogImage($blog);
            $blog->delete();
        }

        return response()->json(['message' => 'Bulk delete success'], 200);
    }

    public function bulkStatus(Request $request)
    {
        $ids = $request->ids ?? [];
        $status = $request->status;

        if (!is_array($ids) || count($ids) === 0) {
            return response()->json(['message' => 'Danh sách bài viết trống'], 422);
        }
        if (!in_array((int) $status, [0, 1], true)) {
            return response()->json(['message' => 'Trạng thái không hợp lệ'], 422);
        }

        Blog::whereIn('id', $ids)->update(['status' => (int) $status]);

        return response()->json(['message' => 'Bulk status success'], 200);
    }

    public function bulkDuplicate(Request $request)
    {
        $ids = $request->ids ?? [];
        if (!is_array($ids) || count($ids) === 0) {
            return response()->json(['message' => 'Danh sách bài viết trống'], 422);
        }

        $created = [];
        DB::beginTransaction();
        try {
            foreach ($ids as $id) {
                $original = Blog::find($id);
                if (!$original) {
                    continue;
                }
                $created[] = $this->cloneBlog($original);
            }
            DB::commit();

            return response()->json([
                'message' => 'Bulk duplicate success',
                'count' => count($created),
                'data' => $created,
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json(['message' => 'Bulk duplicate failed'], 500);
        }
    }

    private function removeBlogImage(Blog $blog): void
    {
        $paths = array_filter([$blog->image ?? null, $blog->avatar ?? null]);
        foreach ($paths as $file) {
            $file = str_replace('http://localhost:8080', '', $file);
            $filename = public_path() . $file;
            if ($file && file_exists($filename)) {
                @\File::delete($filename);
            }
        }
    }

    private function cloneBlog(Blog $original): Blog
    {
        $copy = $original->replicate();
        $title = json_decode($original->title, true);
        $baseName = 'ban-sao';

        if (is_array($title) && count($title) > 0) {
            foreach ($title as &$row) {
                if (isset($row['content'])) {
                    $row['content'] = rtrim($row['content']) . ' (Bản sao)';
                }
            }
            unset($row);
            $copy->title = json_encode($title, JSON_UNESCAPED_UNICODE);
            $baseName = to_slug($title[0]['content'] ?? 'ban-sao');
        }

        $copy->slug = $this->uniqueBlogSlug($baseName);
        $copy->status = 0;
        $copy->home_status = 0;
        $copy->save();

        return $copy;
    }

    private function uniqueBlogSlug(string $slug): string
    {
        $slug = $slug ?: 'bai-viet';
        $base = $slug;
        $i = 1;
        while (Blog::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
    public function edit($id)
    {
        $data = Blog::where([
            'id'=> $id
        ])
        ->first();
        return response()->json([
            'data' => $data,
            'message' => 'success'
        ]);
    }

}
