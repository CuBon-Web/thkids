<?php

namespace App\Http\Controllers\Api\Website;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\website\WhyChoose;

class WhyChooseController extends Controller
{
    public function createOrUpdate(Request $request)
    {
        if ($request->data) {
            WhyChoose::truncate();
            foreach ($request->data as $index => $value) {
                WhyChoose::create([
                    'title'       => $value['title'] ?? '',
                    'description' => $value['description'] ?? '',
                    'image'       => $value['image'] ?? '',
                    'link'        => $value['link'] ?? '',
                    'sort'        => $index + 1,
                    'status'      => $value['status'] ?? 1,
                ]);
            }
        }

        return response()->json(['message' => 'success'], 200);
    }

    public function list()
    {
        $data = WhyChoose::orderBy('sort')->get();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }
}
