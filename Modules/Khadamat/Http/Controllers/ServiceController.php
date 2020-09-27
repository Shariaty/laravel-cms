<?php

namespace Modules\Khadamat\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Khadamat\Images;
use Modules\Khadamat\Services;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;


class ServiceController extends Controller
{
    protected  $redirectPath = 'administrator/services/list';
    protected  $destinationPath = PATH_ROOT.'/uploads/admins/general-images';
    protected  $destinationPathOfProductFiles = PATH_ROOT.('/uploads/admins/general-attachments');

    protected $rules = array(
        'title'      => 'required|max:100'
    );


    public function __construct()
    {
        parent::__construct();
    }
    // -------------------------------------------------------------------------------
    protected function validateService($data){
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
    public function index()
    {
        $services = Services::orderBy('created_at')->where('parent' , 0)->get();
        return view('khadamat::list' , compact('services'))->with('title' , 'Services  List');
    }
    // -------------------------------------------------------------------------------
    public function add()
    {
        $this->removeFakeOnes();
        $service = Services::create(array('fake' => 'Y'));

        return redirect(route('admin.services.edit' , $service));
    }
    // -------------------------------------------------------------------------------
    public function edit(Services $service)
    {
        $locales = Config::get('translatable.localeList');
        $images = $service->images;
        $parents = $this->_generateCategoryList($service->id);
        $selectedValue = null;

        $tags = Tag::pluck('tag_name' , 'id');

        if ($service->tags) {
            $selectedTags = $service->tags->pluck('id');
        } else {
            $selectedTags = '';
        }

        return view('khadamat::edit' , compact('service' ,'images' , 'parents' , 'locales' , 'tags' , 'selectedTags'))->with('title' , 'Edit Service : '.$service->title);
    }
    // -------------------------------------------------------------------------------
    public function update(Request $request , Services $service)
    {
        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
        } else {
            $request->merge(array('is_published' => 'N'));
        }

        $validationResult = $this->validateService($request->all());

        if ($validationResult) {
            return redirect()->back()->withInput($request->input())->withErrors($validationResult);
        }

        $request->merge(array('fake' => 'N'));

        DB::beginTransaction();

        try {
            if(!empty($request->file('image'))) {
                if(!empty($service->img)){
                    $this->imageDelete($service->img , $this->destinationPath);
                }

                $name = $this->imageUpload($request->file('image') , $this->destinationPath , 700);
                if(!empty($name)){
                    $request->merge(array('img' => $name));
                }
            }

            $data = $request->except(['_token', 'category' , 'value' , 'image']);
            $service->update($data);

            // Sync Keywords
            $keywords = $request->input('keywords');

            $finalKeywords = [];
            if ($keywords){
                foreach ($keywords as $key => $value) {
                    $tag = Tag::whereId($value)->first();
                    if ($tag) {
                        array_push($finalKeywords , $tag->id);
                    } else {
                        $newTag = Tag::create(['tag_name' => $value]);
                        if ($newTag) {
                            array_push($finalKeywords , $newTag->id);
                        }
                    }
                }
            }

            $service->tags()->sync($finalKeywords);
            // Sync Keywords


            DB::commit();
            $success = true;
        } catch (\Exception $e) {
            dd($e);
            $success = false;
            DB::rollback();
        }

        if($success) {
            return redirect(route('admin.services.list'))->with('success' , LBL_COMMON_UPDATE_SUCCESSFUL);
        } else {
            return redirect(route('admin.services.list'))->with('error', LBL_COMMON_ERROR);
        }

    }
    // -------------------------------------------------------------------------------
    public function delete(Services $service)
    {
        if($service->delete()){
            return redirect(route('admin.services.list'))->with('success' , trans('notify.DELETE_SUCCESS_NOTIFICATION'));
        }
        return redirect(route('admin.services.list'))->with('error' , trans('notify.DELETE_FAILED_NOTIFICATION'));
    }
    // -------------------------------------------------------------------------------
    public function removePicture(Services $service)
    {
        if(!empty($service->img)){
            $this->imageDelete($service->img , $this->destinationPath);
        }

        $service->update(['img' => null]);
        return redirect(route('admin.services.edit' , ['service' => $service , 'status' => 'image'] ));
    }
    // -------------------------------------------------------------------------------
    protected function statusUpdate(Request $request){
        if($request->has('user_id') && $request->has('status')){
            $item = Services::where('id' , $request->input('user_id'))->first();
            $item->is_published = $request->input('status');
            $item->update();
            return response(['status' => 'success' , 'message' => LBL_COMMON_UPDATE_SUCCESSFUL , 'newStatus' => $request->input('status')]);
        }
        return response(['status' => 'error' , 'message' => LBL_COMMON_ERROR] , 404);
    }
    // -------------------------------------------------------------------------------
    public function dropZoneUpload(Request $request)
    {
        $rules = array(
            'file' => 'required|image|max:3000',
            'store_id' => 'required'
        );
        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails())
        {
            return response($validation->errors->first() , 400);
        }

        $file = $request->file('file');
        $id = $request->input('store_id');
        $service = Services::whereId($id)->first();

        $name = time().mt_rand(11111 , 99999).'.'.$file->getClientOriginalExtension();
        $img = Image::make($file->getRealPath());
        $upload_success = $img->resize(750, null , function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->save($this->destinationPath.'/'.$name);

        if( $upload_success ) {
            $service->images()->create(['img' => $name , 'size' => $file->getSize()]);
            return response(['success' => 'success'  , 'name' => $name] , 200);
        } else {
            return response('error' , 400);
        }
    }
    // -------------------------------------------------------------------------------
    public function dropZoneImageRemove(Request $request)
    {
        $imageName = $request->input('id');
        if($imageName){
            $fullFileName = $this->destinationPath .'/'. $imageName;

            @File::delete($fullFileName);

            $image = Images::where('img' , '=' , $imageName)->first();

            $result = $image->delete();

            if($result){
                return response(['status' => 'success' , 'message' => LBL_COMMON_DELETE_SUCCESSFUL] , 200);
            } else {
                return response(['status' => 'error' , 'message' => LBL_COMMON_DELETE_ERROR] , 400);
            }
        }
        return response(['status' => 'error' , 'message' => LBL_COMMON_DELETE_ERROR] , 400);
    }
    // -------------------------------------------------------------------------------
    public function removeFakeOnes(){
        $items = Services::select('id' , 'file')->where('fake' , 'Y')
                ->where('created_at' , '<=' , Carbon::now()->subDay())
                ->with('images')->get();

        foreach ($items as $item){

            if(count($item->images)){
                foreach ($item->images as $image) {
                    $this->imageDelete($image->img , $this->destinationPath);
                }
            }

            if(!empty($item->file)){
                $this->removeFile($item->id);
            }

            $item->forceDelete();
        }

        return true;
    }
    // -------------------------------------------------------------------------------
    protected function imageDelete ($fileName , $destinationPath)
    {
        $fullFileName = $destinationPath . '/' . $fileName;

        if (File::exists($fullFileName)) {
            @File::delete($fullFileName);
            return true;
        }
        return false;
    }
    // -------------------------------------------------------------------------------
    protected function imageUpload ($image , $destinationPath , $height = null , $width = null){
        $name = time().'.'.$image->getClientOriginalExtension();
        $img = Image::make($image->getRealPath());

        $img->resize($height, $width, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$name);

        return $name;
    }

    // AttachmentFile ----------------------------------------------------------------
    public function ajaxFileUpload(Request $request)
    {
        if($request->ajax()) {
            if($request->hasFile('file'))  {
                $rules = array('file' => 'required|mimes:pdf,docx|max:2048');
                $validator = Validator::make($request->all(), $rules);

                if($validator->fails()){
                    $errors = $validator->messages()->all();
                    $result = array('success' => false , 'message' => $errors);
                } else {
                    $id = intval($request->input('id'));
                    $type = $request->input('type');
                    $item = Services::whereId($id)->first();
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

        $file->move($this->destinationPathOfProductFiles,$fileName);
        return $fileName;
    }
    // -------------------------------------------------------------------------------
    protected function removeFile ($item , $type){
        $product = Services::whereId($item)->first();

        if ($type === "sheet") {
            if($product->sheet){
                $fullFileName = $this->destinationPathOfProductFiles.'/'.$product->sheet ;
                if(File::exists($fullFileName)){
                    @File::delete($fullFileName);
                    $product->update(array('sheet' => null));
                    return true;
                }
            }
        } else {
            if($product->file){
                $fullFileName = $this->destinationPathOfProductFiles.'/'.$product->file ;
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
    protected function magazineFileView(Services $service , $type){

        $headers = array(
            'Content-Type' => 'application/pdf'
        );

        $file = $type === "sheet" ? $service->sheet : $service->file;

        $fullFileName = $this->destinationPathOfProductFiles.'/'.$file;

        return response()->file($fullFileName, $headers);
    }
    // AttachmentFile ----------------------------------------------------------------

    protected function _generateCategoryList( $remove = null ){
        $parentsCategory = Services::parents()->get();

        $final = [0 => 'None'];
        foreach ($parentsCategory as $parent) {
            $final[$parent->id] = $parent->title;
            if(count($parent->children)) {
                foreach ($parent->children as $child) {
                    $final[$child->id] = '-'.$child->title;
                    if(count($child->children)) {
                        foreach ($child->children as $secondChild) {
                            $final[$secondChild->id] = '--'.$secondChild->title;
                        }
                    }
                }
            }
        }
        if($remove) {
            $final = array_except($final , $remove);
        }
        return $final;
    }

}
