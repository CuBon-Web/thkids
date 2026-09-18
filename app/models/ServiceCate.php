<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\Services;
class ServiceCate extends Model
{
    protected $table = "service_category";
    public function services()
    {
        return $this->hasMany(Services::class,'cate_id','id');
    }
    protected function normalizeImage($image)
    {
        if (is_array($image)) {
            return json_encode(array_values(array_filter($image)));
        }
        if (is_string($image) && $image !== '') {
            return $image;
        }
        return '[]';
    }

    public function saveCate($request)
    {
        $id = $request->id;
        $image = $this->normalizeImage($request->image);
        if($id != "" ){
            $query = ServiceCate::where([
                'id' => $id
             ])->first();
            if ($query) {
                $query->name = $request->name;
                $query->slug = to_slug($request->name);
                $query->content = $request->content;
                $query->description = $request->description;
                $query->status = $request->status;
                $query->image = $image;
                $query->save();
            }else{
                $query = new ServiceCate();
                $query->name = $request->name;
                $query->slug = to_slug($request->name);
                $query->content = $request->content;
                $query->status = $request->status;
                $query->description = $request->description;
                $query->image = $image;
                $query->save();
            }
            
        }else{
            $query = new ServiceCate();
            $query->name = $request->name;
            $query->slug = to_slug($request->name);
            $query->content = $request->content;
            $query->status = $request->status;
            $query->description = $request->description;
            $query->image = $image;
            $query->save();
            
        }
        return $query;
    }
}
