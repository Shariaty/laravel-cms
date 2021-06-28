<?php

namespace Modules\Skill\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Skill\Skill;
use Modules\Skill\SkillCategory;
use Illuminate\Support\Facades\File;


class SkillController extends Controller
{
    protected  $redirectPath = 'administrator/skills/list';
    protected  $destinationPath = PATH_ROOT.('/uploads/admins/skill-pictures');

    // -------------------------------------------------------------------------------
    public function index()
    {
        $skills = Skill::orderBy('sort' , 'ASC')->real()->paginate(20);
        return view('skill::list' , compact('skills'))->with('title' , 'Skills List');
    }
    // -------------------------------------------------------------------------------
    public function add(Skill $skill)
    {
        $this->removeFakeOnes();
        $lastOrder = Skill::max('sort');
        $skill = Skill::create(array('sort' => $lastOrder ? $lastOrder+1 : 1 , 'fake' => 'Y'));
        return redirect(route('admin.skills.edit' , $skill));
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

        $request->merge(array('cat_id' => $request->input('category') , 'fake' => "N" ));
        $data = $request->except(['_token' , 'category' , 'image']);
        $skill->update($data);

        $request->session()->flash('Success', trans('notify.UPDATE_SUCCESS_NOTIFICATION'));
        return redirect($this->redirectPath);
    }
    // -------------------------------------------------------------------------------
    protected function delete(Skill $skill , Request $request){
        if(!empty($skill->file)){
            $this->removeFile($skill);
        }
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
    public function removeFakeOnes(){
        $items = Skill::select('id' , 'file')->where('fake' , 'Y')
            ->where('created_at' , '<=' , Carbon::now()->subDay())->get();

        foreach ($items as $item){
            if(!empty($item->file)){
                $this->removeFile($item);
            }

            $item->forceDelete();
        }

        return true;
    }

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


    // AttachmentFile ----------------------------------------------------------------
    public function ajaxFileUpload(Request $request)
    {
        if($request->ajax()) {
            if($request->hasFile('file'))  {
                $rules = array('file' => 'required|mimes:jpeg,jpg,png|max:1024');
                $validator = Validator::make($request->all(), $rules);

                if($validator->fails()){
                    $errors = $validator->messages()->all();
                    $result = array('success' => false , 'message' => $errors);
                } else {
                    $id = intval($request->input('id'));
                    $type = $request->input('type');
                    $item = Skill::whereId($id)->first();
                    if($item){
                        $file = $request->file('file');
                        $name = $this->uploadFile($file);

                        if ($type === "sheet") {
                            if($item->sheet)
                                $this->removeFile($item , $type);

                            $item->update(array('sheet' => $name));
                        } else {
                            if($item->file)
                                $this->removeFile($item , $type);

                            $item->update(array('file' => $name));
                        }

                    }
                    $result = array('success' => true , 'message' => $name , 'slug' => $item->id);
                }
                return response()->json($result);
            } else {
                return response()->json(array('error' => true , 'message' => [ 'File is not available or it is damaged , in this case you can use another file' ] ));
            }
        }

        return 'forbidden';
    }
    // -------------------------------------------------------------------------------
    public function magazineFileRemove($item , $type)
    {
        if($this->removeFile($item , $type)){
            return back()->with('info' , trans('notify.DELETE_SUCCESS_NOTIFICATION'));
        }
        return back()->with('error' , trans('notify.DATA_NOT_PROVIDED_ERROR_NOTIFICATION'));
    }
    // -------------------------------------------------------------------------------
    protected function uploadFile ($file){

        $extension = $file->getClientOriginalExtension();
        $fileName = Carbon::now()->toDateString().'--'.rand(11111,99999).'.'.$extension; // Make File name

        $file->move($this->destinationPath,$fileName);
        return $fileName;
    }
    // -------------------------------------------------------------------------------
    protected function removeFile ($item , $type = 'file'){
        if($item->file){
            $fullFileName = $this->destinationPath.'/'.$item->file ;
            if(File::exists($fullFileName)){
                @File::delete($fullFileName);
                $item->update(array('file' => null));
                return true;
            }
        }
        return false;
    }
    // -------------------------------------------------------------------------------
    protected function magazineFileView(Skill $slide , $type){
        $fullFileName = $this->destinationPath.'/'.$slide->file;
        return response()->file($fullFileName);
    }
    // AttachmentFile ----------------------------------------------------------------

}
