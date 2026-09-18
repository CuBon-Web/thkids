<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\ProjectCate;

class ProjectCateController extends Controller
{
    public function create(Request $request)
    {
        $data = (new ProjectCate())->saveProjectCate($request);
        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function list(Request $request)
    {
        $keyword = $request->keyword ?? '';
        $data = $keyword
            ? ProjectCate::where('name', 'LIKE', "%{$keyword}%")->orderBy('id', 'DESC')->get()
            : ProjectCate::orderBy('id', 'DESC')->get();

        return response()->json(['data' => $data, 'message' => 'success'], 200);
    }

    public function edit($id)
    {
        $data = ProjectCate::find($id);
        return response()->json(['data' => $data, 'message' => 'success'], 200);
    }

    public function delete($id)
    {
        ProjectCate::destroy($id);
        return response()->json(['message' => 'success'], 200);
    }
}
