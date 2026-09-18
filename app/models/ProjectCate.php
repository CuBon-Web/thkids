<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ProjectCate extends Model
{
    protected $table = 'project_categories';
    protected $fillable = ['name', 'slug', 'description', 'content', 'image', 'status'];

    public function projects()
    {
        return $this->hasMany(Project::class, 'project_cate_id', 'id');
    }

    public function saveProjectCate($request)
    {
        $id = $request->id ?? '';

        if ($id) {
            $query = self::find($id) ?? new self();
        } else {
            $query = new self();
        }

        $query->name        = $request->name;
        $query->slug        = to_slug($request->name);
        $query->description = $request->description ?? '';
        $query->content     = $request->content ?? '';
        $query->image       = json_encode($request->image ?? []);
        $query->status      = $request->status ?? 1;
        $query->save();

        return $query;
    }
}
