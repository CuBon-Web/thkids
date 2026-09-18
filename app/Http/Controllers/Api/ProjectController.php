<?php



namespace App\Http\Controllers\Api;



use App\Http\Controllers\Controller;

use App\models\Project;

use App\Services\CloudflareImageService;

use Illuminate\Http\Request;



class ProjectController extends Controller

{

    protected $cloudflareService;



    public function __construct(CloudflareImageService $cloudflareService)

    {

        $this->cloudflareService = $cloudflareService;

    }



    public function create(Request $request, Project $ser)

    {

        $data = $ser->saveProject($request);

        return response()->json(['message' => 'Save Success', 'data' => $data], 200);

    }



    public function list(Request $request)

    {

        $keyword = $request->keyword ?? '';

        $fields  = ['id', 'name', 'created_at', 'images', 'project_cate_id', 'status', 'show_home'];

        $data = $keyword

            ? Project::where('name', 'LIKE', "%{$keyword}%")->orderBy('id', 'DESC')->get($fields)

            : Project::orderBy('id', 'DESC')->get($fields);



        return response()->json(['data' => $data, 'message' => 'success']);

    }



    public function delete($id)

    {

        $query = Project::find($id);

        if ($query) {

            $this->deleteProjectImages($query->images);

            $query->delete();

        }



        return response()->json(['message' => 'Delete Success'], 200);

    }



    public function bulkDelete(Request $request)

    {

        $ids = $request->ids ?? [];

        foreach ($ids as $id) {

            $query = Project::find($id);

            if (!$query) {

                continue;

            }

            $this->deleteProjectImages($query->images);

            $query->delete();

        }



        return response()->json(['message' => 'Deleted'], 200);

    }

    public function bulkDuplicate(Request $request)
    {
        $ids = $request->ids ?? [];
        $created = [];

        foreach ($ids as $id) {
            $original = Project::find($id);
            if (!$original) {
                continue;
            }

            $name = $this->duplicateProjectName($original->name);
            $copy = $original->replicate();
            $copy->name = $name;
            $copy->slug = $this->uniqueProjectSlug($name);
            $copy->status = 0;
            $copy->show_home = 0;
            $copy->save();
            $created[] = $copy;
        }

        return response()->json([
            'message' => 'Duplicated',
            'count' => count($created),
            'data' => $created,
        ], 200);
    }

    public function toggleField(Request $request)

    {

        $project = Project::find($request->id);

        if (!$project) {

            return response()->json(['message' => 'Not found'], 404);

        }

        $field = $request->field;

        if (!in_array($field, ['status', 'show_home', 'project_cate_id'])) {

            return response()->json(['message' => 'Invalid field'], 422);

        }

        $project->$field = $request->value;

        $project->save();

        return response()->json(['message' => 'Updated', 'data' => $project], 200);

    }



    public function edit($id)

    {

        $data = Project::find($id);

        return response()->json(['data' => $data, 'message' => 'success']);

    }



    /**

     * @param string|array|null $images

     */

    protected function duplicateProjectName($name)
    {
        $base = trim($name) . ' (bản sao)';
        $candidate = $base;
        $n = 2;

        while (Project::where('name', $candidate)->exists()) {
            $candidate = $base . ' ' . $n;
            $n++;
        }

        return $candidate;
    }

    protected function uniqueProjectSlug($name)
    {
        $slug = to_slug($name);
        $candidate = $slug;
        $n = 2;

        while (Project::where('slug', $candidate)->exists()) {
            $candidate = $slug . '-' . $n;
            $n++;
        }

        return $candidate;
    }

    protected function deleteProjectImages($images)

    {

        if (empty($images)) {

            return;

        }



        $list = is_array($images) ? $images : json_decode($images, true);

        if (!is_array($list)) {

            return;

        }



        foreach ($list as $item) {
            $this->cloudflareService->deleteImageByUrl($item);
        }

    }

}


