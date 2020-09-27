<?php

namespace Modules\SlideShow\Http\Controllers;

use App\Admin\Skill;
use App\Admin\SkillCategory;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use Modules\SlideShow\SlideShow;

class SlideShowController extends Controller
{
    protected  $redirectPath = 'administrator/slide-show/list';
    protected  $destinationPath = PATH_ROOT.('/uploads/admins/slide-show-pictures');

    // -------------------------------------------------------------------------------
    public function index()
    {
        $slides = SlideShow::published()->orderBy('sort' , 'ASC')->paginate(50);
        return view('slideshow::list' , compact('slides'))->with('title' , 'List of Slides');
    }
    // -------------------------------------------------------------------------------
    public function add()
    {
        $this->removeFakeOnes();
        $lastOrder = SlideShow::max('sort');
        $slide = SlideShow::create(array('sort' => $lastOrder ? $lastOrder+1 : 1 , 'fake' => 'Y'));
        return redirect(route('admin.slide.edit' , $slide->id));
    }
    // -------------------------------------------------------------------------------
    public function edit(SlideShow $slide)
    {
        $locales = Config::get('translatable.localeList');
        return view('slideshow::edit' , compact('slide' , 'locales'))->with('title' , 'Edit: '.$slide->title);
    }
    // -------------------------------------------------------------------------------
    public function update(Request $request , SlideShow $slide)
    {
        $validationResult = $this->SlideValidator($request->all());

        if ($validationResult) {
            return redirect()->back()->withInput($request->input())->withErrors($validationResult);
        }

        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
        } else {
            $request->merge(array('is_published' => 'N'));
        }
        $request->merge(array('fake' => 'N'));
        $data = $request->except(['_token' , 'image']);
        $slide->update($data);

        $request->session()->flash('Success', trans('notify.UPDATE_SUCCESS_NOTIFICATION'));
        return redirect($this->redirectPath);
    }
    // -------------------------------------------------------------------------------
    protected function delete(SlideShow $slide , Request $request){
        if(!empty($slide->file)){
            @$this->removeFile($slide->id);
        }
        $slide->delete();
        $request->session()->flash('success', trans('notify.DELETE_SUCCESS_NOTIFICATION'));
        return redirect($this->redirectPath);
    }
    // -------------------------------------------------------------------------------
    protected function SlideValidator($data){
        $rules = [];
        $locales = Config::get('translatable.localeList');

        foreach ($locales as $key => $value) {
            $rules[$key] = ['required','array'];
            $rules[$key.'.title'] = ['required','max:100'];
        }

        $validator =  Validator::make($data, $rules);

        if ($validator->fails())
        {
            $error = $validator->errors()->first();
            return $validator->errors();
        }
        return false;
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
            $skill = SlideShow::where('id' , $request->input('user_id'))->first();
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
            SlideShow::where('id' , $value)->update(array('sort' => $key));
        }

        return response(['status' => 'Success' , 'message' => 'Ordering updated']);
    }
    // -------------------------------------------------------------------------------
    public function removeFakeOnes(){
        $items = SlideShow::select('id' , 'file')->where('fake' , 'Y')
            ->where('created_at' , '<=' , Carbon::now()->subDay())->get();

        foreach ($items as $item){
            if(!empty($item->file)){
                $this->removeFile($item->id);
            }

            $item->forceDelete();
        }

        return true;
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
                    $item = SlideShow::whereId($id)->first();
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
        $product = SlideShow::whereId($item)->first();

        if ($type === "sheet") {
            if($product->sheet){
                $fullFileName = $this->destinationPath.'/'.$product->sheet ;
                if(File::exists($fullFileName)){
                    @File::delete($fullFileName);
                    $product->update(array('sheet' => null));
                    return true;
                }
            }
        } else {
            if($product->file){
                $fullFileName = $this->destinationPath.'/'.$product->file ;
                if(File::exists($fullFileName)){
                    @File::delete($fullFileName);
                    $product->update(array('file' => null));
                    return true;
                }
            }
        }

        return false;
    }
    // -------------------------------------------------------------------------------
    protected function magazineFileView(SlideShow $slide , $type){

//        $headers = array(
//            'Content-Type' => 'application/pdf'
//        );

        $fullFileName = $this->destinationPath.'/'.$slide->file;

//        return response()->file($fullFileName, $headers);
        return response()->file($fullFileName);
    }
    // AttachmentFile ----------------------------------------------------------------

}
