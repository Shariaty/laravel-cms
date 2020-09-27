<?php

namespace Modules\Skill\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Skill\Skill;
use Modules\Skill\SkillCategory;


class SkillController extends Controller
{
    protected  $redirectPath = 'administrator/skills/list';
    protected  $destinationPathOfNews = PATH_ROOT.('/uploads/admins/skill-pictures');

    // -------------------------------------------------------------------------------
    public function index()
    {
        $skills = Skill::orderBy('sort' , 'ASC')->paginate(20);
        return view('skill::list' , compact('skills'))->with('title' , 'Skills List');
    }
    // -------------------------------------------------------------------------------
    public function add()
    {
        $categories = SkillCategory::orderBy('title' , 'ASC')->pluck('title', 'id');
        return view('skill::add' , compact('categories'))->with('title' , 'Category Create');
    }
    // -------------------------------------------------------------------------------
    public function create(Request $request)
    {
        $this->SkillValidator($request->all())->validate();

        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
        } else {
            $request->merge(array('is_published' => 'N'));
        }

        $lastOrder = Skill::max('sort');
        $request->merge(array('sort' => $lastOrder++));

        $request->merge(array('cat_id' => $request->input('category')));
        $data = $request->except(['_token' , 'image' , 'category']);

        Skill::create($data);

        $request->session()->flash('Success', trans('notify.CREATE_SUCCESS_NOTIFICATION'));
        return redirect($this->redirectPath);
    }
    // -------------------------------------------------------------------------------
    public function edit(Skill $skill)
    {
        $categories = SkillCategory::orderBy('title' , 'ASC')->pluck('title','id');
        return view('skill::edit' , compact('skill' , 'categories'))->with('title' , 'Edit: '.$skill->title);
    }
    // -------------------------------------------------------------------------------
    public function update(Request $request , Skill $skill)
    {
        $this->SkillValidator($request->all() , $skill)->validate();

        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
        } else {
            $request->merge(array('is_published' => 'N'));
        }

        $request->merge(array('cat_id' => $request->input('category')));
        $data = $request->except(['_token' , 'category' , 'image']);
        $skill->update($data);

        $request->session()->flash('Success', trans('notify.UPDATE_SUCCESS_NOTIFICATION'));
        return redirect($this->redirectPath);
    }
    // -------------------------------------------------------------------------------
    protected function delete(Skill $skill , Request $request){
        $skill->delete();
        $request->session()->flash('success', trans('notify.DELETE_SUCCESS_NOTIFICATION'));
        return redirect($this->redirectPath);
    }
    // -------------------------------------------------------------------------------
    protected function SkillValidator(array $data)
    {
        return Validator::make($data, [
            'title' => 'required|max:100',
            'category' => 'required'
        ]);
    }
    // -------------------------------------------------------------------------------
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'title' => 'required|max:100',
            'slug'  => 'required|max:100|unique:skills,slug',
            'category' => 'required',
            'body'  => 'required|min:3',
            'image'   => 'image|mimes:jpeg,png,jpg|max:1024'
        ]);
    }
    // -------------------------------------------------------------------------------
    protected function validatorUpdate(array $data , $skill)
    {
        return Validator::make($data, [
            'title' => 'required|max:100',
            'slug'  => 'required|max:100|unique:skills,slug,'.$skill->id,
            'body'  => 'required|min:3',
            'image'   => 'image|mimes:jpeg,png,jpg|max:1024'

        ]);
    }
    // -------------------------------------------------------------------------------
    protected function statusUpdate(Request $request){
        if($request->has('user_id') && $request->has('status')){
            $skill = Skill::where('id' , $request->input('user_id'))->first();
            $skill->is_published = $request->input('status');
            $skill->update();
            return response(['status' => 'success' , 'message' => 'successfully updated' , 'newStatus' => $request->input('status')]);
        }
        return response(['status' => 'error' , 'message' => 'Something went wrong! contact the administrator'] , 404);

    }
    // -------------------------------------------------------------------------------
    protected function AjaxSort(Request $request) {

        $data = $request->all();

        foreach ($data as $key => $value ){
            Skill::where('id' , $value)->update(array('sort' => $key));
        }

        return response(['status' => 'Success' , 'message' => 'Ordering updated']);
    }
    // -------------------------------------------------------------------------------

    // -------------------------------------------------------------------------------
    public function categoryList()
    {
        $categories = SkillCategory::orderBy('created_at')->paginate(20);
        return view('skill::category.list' , compact('categories'))->with('title' , 'Categories List');
    }
    // -------------------------------------------------------------------------------
    public function categoryAdd()
    {
        return view('skill::category.add')->with('title' , 'Category Create');
    }
    // -------------------------------------------------------------------------------
    public function categoryCreate(Request $request)
    {
        if($request->has('slug')){
            $request->merge(array('slug' => slug_utf8($request->input('slug'))));
        }

        $this->validate($request, [
            'title' => 'required|max:100',
            'slug'  => 'required|max:100|unique:skills_categories,slug',
            'desc'  => 'max:1000'
        ]);

        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
        } else {
            $request->merge(array('is_published' => 'N'));
        }

        $data = $request->except(['_token' , 'skills_categories']);
        SkillCategory::create($data);

        $request->session()->flash('Success', trans('notify.CREATE_SUCCESS_NOTIFICATION'));
        return redirect(route('admin.skills.categories'));
    }
    // -------------------------------------------------------------------------------
    public function categoryEdit(SkillCategory $cat)
    {
        return view('skill::category.edit' , compact('cat'))->with('title' , 'Edit: '.$cat->title);
    }
    // -------------------------------------------------------------------------------
    public function categoryUpdate(Request $request , SkillCategory $cat)
    {
        if($request->has('slug')){
            $request->merge(array('slug' => slug_utf8($request->input('slug'))));
        }

        $this->validate($request, [
            'title' => 'required|max:100',
            'slug'  => 'required|max:100|unique:skills_categories,slug,'.$cat->id,
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
        return redirect(route('admin.skills.categories'));
    }
    // -------------------------------------------------------------------------------
    protected function categoryDelete(SkillCategory $cat , Request $request){
        $cat->delete();
        $request->session()->flash('success', trans('notify.DELETE_SUCCESS_NOTIFICATION'));
        return redirect(route('admin.skills.categories'));
    }
    // -------------------------------------------------------------------------------
    protected function categoryStatusUpdate (Request $request)
    {
        if($request->has('user_id') && $request->has('status')){
            $cat = SkillCategory::where('id' , $request->input('user_id'))->first();
            $cat->is_published = $request->input('status');
            $cat->update();
            return response(['status' => 'success' , 'message' => 'successfully updated' , 'newStatus' => $request->input('status')]);
        }
        return response(['status' => 'error' , 'message' => 'Something went wrong! contact the administrator'] , 404);
    }
}
