<?php

namespace Modules\Project\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Project\Project;

class ProjectApiController extends Controller
{
    // -------------------------------------------------------------------------------
    public function getAllProjects()
    {
        $projects = Project::orderBy('sort', 'ASC')->with('categories')->get();

        foreach ($projects as $project){
            unset($project->id);
            unset($project->updated_at);
            unset($project->deleted_at);

            foreach ($project->categories as $category) {
                unset($category->id);
                unset($category->updated_at);
                unset($category->deleted_at);
                unset($category->pivot);
            }
        }

        return response()->json($projects);
    }

}
