<?php

namespace App\Http\Controllers\Api\Product;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use  App\models\product\Category;
use File,Validator;

class CategoryController extends Controller
{
    public function add(Request $request, Category $category)
    {
        $data = $category->saveCate($request);
        return response()->json([
    		'message' => 'Save Success',
    		'data'=> $data
    	],200);
    }
    public function list(Request $request)
    {
        $keyword = $request->keyword;
        $query = Category::query();
        if ($keyword != "") {
            $query->where('name', 'LIKE', '%'.$keyword.'%');
        }
        $data = $query->orderBy('sort', 'ASC')->orderBy('id', 'ASC')->get();
        return response()->json([
            'data' => $data,
            'message' => 'success'
        ]);
    }

    public function sort(Request $request)
    {
        $ids = $request->ids ?? [];
        if (!is_array($ids) || count($ids) === 0) {
            return response()->json(['message' => 'Danh sách sắp xếp không hợp lệ'], 422);
        }
        foreach ($ids as $index => $id) {
            Category::where('id', (int) $id)->update(['sort' => $index + 1]);
        }
        return response()->json(['message' => 'Sort success'], 200);
    }
    public function edit($id)
    {
        $data = Category::where(['id'=>$id])->first();
        return response()->json([
            'message' => 'success',
            'data' => $data
        ], 200);
    }
    public function delete( $id)
    {
        $query = Category::find($id);
        $file= str_replace('http://localhost:8080','',$query->avatar);
        $filename = public_path().$file;
        if(file_exists( public_path().$file ) ){
            \File::delete($filename);
        }
        $query->delete();
        return response()->json(['message'=>'Delete Success']);
    }
}
