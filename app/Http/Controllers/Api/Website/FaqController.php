<?php

namespace App\Http\Controllers\Api\Website;

use App\Http\Controllers\Controller;
use App\models\website\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function createOrUpdate(Request $request)
    {
        $items = $request->items ?? [];
        Faq::truncate();

        foreach ($items as $i => $item) {
            $question = trim($item['question'] ?? '');
            if ($question === '') {
                continue;
            }

            Faq::create([
                'question' => $question,
                'answer'   => $item['answer'] ?? '',
                'sort'     => $item['sort'] ?? $i,
                'status'   => $item['status'] ?? 1,
            ]);
        }

        return response()->json(['message' => 'success'], 200);
    }

    public function list()
    {
        $data = Faq::orderBy('sort')->orderBy('id')->get();

        return response()->json(['data' => $data, 'message' => 'success'], 200);
    }
}
