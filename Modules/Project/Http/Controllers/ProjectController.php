<?php

namespace Modules\Project\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use Modules\Project\Project;
use Modules\Project\ProjectCategory;

class ProjectController extends Controller
{
    protected  $redirectPath = 'administrator/projects/list';
    protected  $destinationPathOfNews = PATH_ROOT.('/uploads/admins/project-pictures');

    // -------------------------------------------------------------------------------
    public function index()
    {
        $projects = Project::orderBy('sort' , 'ASC')->paginate(30);
        return view('project::list' , compact('projects'))->with('title' , 'Projects List');
    }
    // -------------------------------------------------------------------------------
    public function add()
    {
        $categories = ProjectCategory::orderBy('title' , 'ASC')->pluck('title', 'id');
        return view('project::add' , compact('categories'))->with('title' , 'Project Create');
    }
    // -------------------------------------------------------------------------------
    public function create(Request $request)
    {
        if($request->has('categories')){
            $request['categories'] = stringArrayConvertToIntArray($request['categories']);
        }

        $this->validator($request->all())->validate();

        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
        } else {
            $request->merge(array('is_published' => 'N'));
        }

        if($request->has('is_expired')){
            $request->merge(array('is_expired' => 'Y'));
        } else {
            $request->merge(array('is_expired' => 'N'));
        }

        $lastOrder = Project::max('sort');
        $request->merge(array('sort' => $lastOrder+1));


        if($request->file('image')) {
            $name = $this->imageUpload($request->file('image') , $this->destinationPathOfNews , null , 500);

            if(!empty($name)){
                $request->merge(array('img' => $name));
            }
        }

        $data = $request->except(['_token' , 'categories' , 'image']);
        $project = Project::create($data);

        if((count($request->input('categories')) >= 1)) {
            $project->categories()->sync($request->input('categories'));
        }

        $request->session()->flash('Success', trans('notify.CREATE_SUCCESS_NOTIFICATION'));

        return redirect($this->redirectPath);
    }
    // -------------------------------------------------------------------------------
    public function edit(Project $project)
    {
        $categories = ProjectCategory::orderBy('title' , 'ASC')->pluck('title','id');

        if( count($project->categories)) {
            $selectedCategory = $project->categories;
        } else {
            $selectedCategory = '';
        }

        return view('project::edit' , compact('project' , 'categories' , 'selectedCategory'))->with('title' , 'Edit: '.$project->title);
    }
    // -------------------------------------------------------------------------------
    public function update(Request $request , Project $project)
    {
        $this->validatorUpdate($request->all() , $project)->validate();


        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
        } else {
            $request->merge(array('is_published' => 'N'));
        }

        if($request->has('is_expired')){
            $request->merge(array('is_expired' => 'Y'));
        } else {
            $request->merge(array('is_expired' => 'N'));
        }

        DB::beginTransaction();
        try {

            if(!empty($request->file('image'))) {

                if(!empty($project->img)){
                    $this->imageDelete($project->img , $this->destinationPathOfNews);
                }

                $name = $this->imageUpload($request->file('image') , $this->destinationPathOfNews , null , 500);
                if(!empty($name)){
                    $request->merge(array('img' => $name));
                }
            }

            $data = $request->except(['_token' , 'categories' , 'image']);

            $project->update($data);
            if((count($request->input('categories')) >= 1)) {

                $project->categories()->sync($request->input('categories'));
            }
            DB::commit();
            $success = true;
        } catch (\Exception $e) {
            dd($e);

            $success = false;
            DB::rollback();
        }

        if($success) {
            $request->session()->flash('Success', trans('notify.UPDATE_SUCCESS_NOTIFICATION'));
        } else {
            $request->session()->flash('Error', trans('notify.UPDATE_FAILED_NOTIFICATION'));
        }

        return redirect($this->redirectPath);
    }
    // -------------------------------------------------------------------------------
    protected function delete(Project $project , Request $request){
        $this->imageDelete($project->img , $this->destinationPathOfNews);
        $project->delete();
        $request->session()->flash('success', trans('notify.DELETE_SUCCESS_NOTIFICATION'));
        return redirect($this->redirectPath);
    }
    // -------------------------------------------------------------------------------
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'title' => 'required|max:100',
            'slug'  => 'required|max:100|unique:projects,slug',
            'categories' => 'required|array',
            'image'   => 'image|mimes:jpeg,png,jpg|max:1024'
        ]);
    }
    // -------------------------------------------------------------------------------
    protected function validatorUpdate(array $data , $project)
    {
        return Validator::make($data, [
            'title' => 'required|max:100',
            'slug'  => 'required|max:100|unique:projects,slug,'.$project->id,
            'image'   => 'image|mimes:jpeg,png,jpg|max:1024'
        ]);
    }
    // -------------------------------------------------------------------------------
    protected function statusUpdate(Request $request){
        if($request->has('user_id') && $request->has('status')){
            $project = Project::where('id' , $request->input('user_id'))->first();
            $project->is_published = $request->input('status');
            $project->update();
            return response(['status' => 'success' , 'message' => 'successfully updated' , 'newStatus' => $request->input('status')]);
        }
        return response(['status' => 'error' , 'message' => 'Something went wrong! contact the administrator'] , 404);

    }
    // -------------------------------------------------------------------------------
    protected function ProjectImageDelete(Project $project){
        $this->imageDelete($project->img , $this->destinationPathOfNews);
        $project->update(array('img' => null));
        return redirect()->back();
    }
    // -------------------------------------------------------------------------------
    protected function AjaxSort(Request $request) {

        $data = $request->all();
        foreach ($data as $key => $value ){
            Project::where('id' , $value)->update(array('sort' => $key));
        }
        return response(['status' => 'Success' , 'message' => 'Ordering updated']);
    }
    // -------------------------------------------------------------------------------

    // -------------------------------------------------------------------------------
    public function categoryList()
    {
        $categories = ProjectCategory::orderBy('created_at')->paginate(20);
        return view('project::category.list' , compact('categories'))->with('title' , 'Categories List');
    }
    // -------------------------------------------------------------------------------
    public function categoryAdd()
    {
        return view('project::category.add')->with('title' , 'Category Create');
    }
    // -------------------------------------------------------------------------------
    public function categoryCreate(Request $request)
    {
        if($request->has('slug')){
            $request->merge(array('slug' => slug_utf8($request->input('slug'))));
        }

        $this->validate($request, [
            'title' => 'required|max:100',
            'slug'  => 'required|max:100|unique:projects_categories,slug',
            'desc'  => 'max:1000'
        ]);

        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
        } else {
            $request->merge(array('is_published' => 'N'));
        }

        $data = $request->except(['_token' , 'projects_categories']);
        ProjectCategory::create($data);

        $request->session()->flash('Success', trans('notify.CREATE_SUCCESS_NOTIFICATION'));
        return redirect(route('admin.projects.categories'));
    }
    // -------------------------------------------------------------------------------
    public function categoryEdit(ProjectCategory $cat)
    {
        return view('project::category.edit' , compact('cat'))->with('title' , 'Edit: '.$cat->title);
    }
    // -------------------------------------------------------------------------------
    public function categoryUpdate(Request $request , ProjectCategory $cat)
    {
        if($request->has('slug')){
            $request->merge(array('slug' => slug_utf8($request->input('slug'))));
        }

        $this->validate($request, [
            'title' => 'required|max:100',
            'slug'  => 'required|max:100|unique:projects_categories,slug,'.$cat->id,
            'desc'  => 'max:1000'
        ]);

        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
            $data = $request->except(['_token']);
        } else {
            $data = $request->except(['_token' , 'is_published']);
        }

        $cat->update($data);

        $request->session()->flash('Success', trans('notify.UPDATE_SUCCESS_NOTIFICATION'));
        return redirect(route('admin.projects.categories'));
    }
    // -------------------------------------------------------------------------------
    protected function categoryDelete(ProjectCategory $cat , Request $request){
        $cat->delete();
        $request->session()->flash('success', trans('notify.DELETE_SUCCESS_NOTIFICATION'));
        return redirect(route('admin.projects.categories'));
    }
    // -------------------------------------------------------------------------------
    protected function categoryStatusUpdate (Request $request)
    {
        if($request->has('user_id') && $request->has('status')){
            $cat = ProjectCategory::where('id' , $request->input('user_id'))->first();
            $cat->is_published = $request->input('status');
            $cat->update();
            return response(['status' => 'success' , 'message' => 'successfully updated' , 'newStatus' => $request->input('status')]);
        }
        return response(['status' => 'error' , 'message' => 'Something went wrong! contact the administrator'] , 404);
    }
    // -------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------
    protected function imageUpload ($image , $destinationPath , $height = null , $width = 100){
        $name = time().'.'.$image->getClientOriginalExtension();
        $img = Image::make($image->getRealPath());

        $img->resize($height, $width, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$name);

        return $name;
    }
    // -------------------------------------------------------------------------------
    protected function imageDelete ($fileName , $destinationPath)
    {
        $fullFileName = $destinationPath . '/' . $fileName;

        if (File::exists($fullFileName)) {
            File::delete($fullFileName);
            return true;
        }
        return false;
    }
    // -------------------------------------------------------------------------------

}
