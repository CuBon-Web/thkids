<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Librarys;
use App\models\MessContact;
use App\Services\CloudflareImageService;

class AllController extends Controller
{
    protected $apiToken;
    protected $accountId;
    protected $cloudflareService;

    public function __construct(CloudflareImageService $cloudflareService)
    {
        $this->cloudflareService = $cloudflareService;
    }
    public function uploadImage(Request $request)
    {
        if (!$request->hasFile('img')) {
            return response()->json(['data' => 'fail'], 500);
        }

        $image = $request->file('img');
        $response = $this->cloudflareService->uploadImage($image);

        if (empty($response['success']) || empty($response['result']['variants'][0])) {
            return response()->json([
                'data' => 'fail',
                'errors' => $response['errors'] ?? null,
            ], 500);
        }

        return response()->json([
            'messenge' => 'success',
            'path' => $response['result']['variants'][0],
        ], 200);
    }
    public function uploadImageMulti(Request $request)
    {
        $uploadId = [];
        if($files = $request->file('file')){
            foreach($request->file('file') as $key => $file){
                $name = rand().$file->getClientOriginalName();
                $fielname = $file->move('uploads/imagesMuli/', $name);
                $uploadId[] = '/uploads/images/'.$name;
            }
        }
        return response()->json([
            'messenge' => 'success',
            'path' => $uploadId
        ],200);
    }
    public function fileStore(Request $request)
    {
        $upload_path = public_path('upload');
        $file_name = $request->file->getClientOriginalName();
        $generated_new_name = time() . '.' . $request->file->getClientOriginalExtension();
        $request->file->move($upload_path, $generated_new_name);
         
        $insert['title'] = $generated_new_name;
        return response()->json([
            'messenge' => 'success',
            'path' => $insert
        ],200);
    }
    public function listMesscontact(Request $request)
    {
        $keyword = $request->keyword;
        if($keyword == ""){
            $data = MessContact::orderBy('id','DESC')->get();
        }else{
            $data = MessContact::where('title', 'LIKE', '%'.$keyword.'%')->orderBy('id','DESC')->get()->toArray();
        }
        return response()->json([
            'data' => $data,
            'message' => 'success'
        ]);
    }
}
