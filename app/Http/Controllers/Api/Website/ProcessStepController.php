<?php

namespace App\Http\Controllers\Api\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\website\ProcessStep;

class ProcessStepController extends Controller
{
    public function createOrUpdate(Request $request)
    {
        $items = $request->items ?? [];
        ProcessStep::truncate();
        foreach ($items as $i => $item) {
            ProcessStep::create([
                'title'       => $item['title']       ?? '',
                'description' => $item['description'] ?? '',
                'image'       => $item['image']        ?? '',
                'sort'        => $item['sort']         ?? $i,
                'status'      => $item['status']       ?? 1,
            ]);
        }
        return response()->json(['message' => 'success'], 200);
    }

    public function list()
    {
        $data = ProcessStep::orderBy('sort')->orderBy('id')->get();
        return response()->json(['data' => $data, 'message' => 'success'], 200);
    }
}
