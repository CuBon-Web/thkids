<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\ServiceCate;
use App\models\ProjectCate;

class Project extends Model
{
    protected $table = "projects";

    public function cateService()
    {
        return $this->hasOne(ServiceCate::class, 'id', 'cate_id');
    }

    public function cateProject()
    {
        return $this->hasOne(ProjectCate::class, 'id', 'project_cate_id');
    }
    public function saveProject($request)
    {
    	$id = $request->id;
        if($id != ""){
            $query = Project::where([
                'id' => $id
             ])->first();
            
            if ($query) {
                $query->name = $request->name;
                $query->slug = to_slug($request->name);
                $query->content = json_encode($request->content);
                $query->project_cate_id = $request->project_cate_id ?? 0;
                $query->description = json_encode($request->description);
                $query->location = $request->location;
                $query->scale = $request->scale;
                $query->operate = $request->operate;
                $query->images = json_encode($request->images);
                $query->status = $request->status;
                $query->show_home = $request->show_home ?? 0;
                $query->save();
            }else{
                $query = new Project();
                $query->name = $request->name;
                $query->slug = to_slug($request->name);
                $query->content = json_encode($request->content);
                $query->project_cate_id = $request->project_cate_id ?? 0;
                $query->description = json_encode($request->description);
                $query->location = $request->location;
                $query->scale = $request->scale;
                $query->operate = $request->operate;
                $query->images = json_encode($request->images);
                $query->status = $request->status;
                $query->show_home = $request->show_home ?? 0;
                $query->save();
            }
            
        }else{
                $query = new Project();
                $query->name = $request->name;
                $query->slug = to_slug($request->name);
                $query->content = json_encode($request->content);
                $query->project_cate_id = $request->project_cate_id ?? 0;
                $query->description = json_encode($request->description);
                $query->location = $request->location;
                $query->scale = $request->scale;
                $query->operate = $request->operate;
                $query->images = json_encode($request->images);
                $query->status = $request->status;
                $query->show_home = $request->show_home ?? 0;
                $query->save();
            
        }
        return $query;
    }
}
