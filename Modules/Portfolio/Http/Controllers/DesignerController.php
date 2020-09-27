<?php

namespace Modules\Portfolio\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Portfolio\Designer;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;


class DesignerController extends Controller
{
    protected  $redirectPath = 'administrator/portfolio/designers';
    protected  $destinationPathOfMagazines = PATH_ROOT.('/uploads/admins/designer-pictures');
    // -------------------------------------------------------------------------------
    protected function validatorUpdate(array $data , $news)
    {
        return Validator::make($data, [
            'title' => 'required|max:100',
            'slug'  => 'unique:magazines,slug,'.$news->id.',id,deleted_at,NULL',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:1024'
        ]);
    }
    // -------------------------------------------------------------------------------
    public function designerList()
    {
        $designers = Designer::orderBy('created_at')->notFake()->withCount('portfolio')->get();
        return view('portfolio::designers.list' , compact('designers'))->with('title' , 'Designers List');
    }
    // -------------------------------------------------------------------------------
    public function designerAdd()
    {
        $this->removeFakeOnes();
        $rand = mt_rand(00000 , 99999);
        $designer = Designer::create(array('slug' => 'Undifined'.$rand , 'fake' => 'Y'));

        return redirect(route('admin.portfolio.designers.edit' , $designer->id));
    }
    // -------------------------------------------------------------------------------
    public function designerEdit(Designer $designer)
    {
        return view('portfolio::designers.edit' , compact('designer'))->with('title' , 'Edit: '.$designer->title);
    }
    // -------------------------------------------------------------------------------
    public function designerUpdate(Request $request , Designer $designer)
    {
        $this->validatorUpdate($request->all() , $designer)->validate();

        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
        } else {
            $request->merge(array('is_published' => 'N'));
        }

        DB::beginTransaction();
        try {

            if(!empty($request->input('finalFile'))) {
                if(!empty($designer->img)){
                    $this->imageDelete($designer->img , $this->destinationPathOfMagazines);
                }

                $name = $this->Base64imageUpload($request->input('finalFile') , $this->destinationPathOfMagazines , 465 , 390);
                if(!empty($name)){
                    $request->merge(array('img' => $name));
                }
            }
            if(!empty($request->input('title'))){
                $slug = slug_utf8($request->input('title'));
                $request->merge(array('slug' => $slug));
            }

            $request->merge(array('fake' => 'N'));
            $data = $request->except(['_token' , 'news_categories' , 'image' , 'finalFile']);

            $designer->update($data);
            DB::commit();
            $success = true;
        } catch (\Exception $e) {
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
    protected function designerDelete(Designer $cat , Request $request){
        $count = $cat->portfolio->count();

        if($count > 0) {
            return redirect(route('admin.portfolio.designers'))->with('warning' , trans('notify.CATEGORY_CONTAIN_ITEMS_WARNING'));
        }

        $cat->delete();
        $request->session()->flash('success', trans('notify.DELETE_SUCCESS_NOTIFICATION'));
        return redirect(route('admin.portfolio.designers'));
    }
    // -------------------------------------------------------------------------------
    protected function designerStatusUpdate (Request $request)
    {
        if($request->has('user_id') && $request->has('status')){
            $cat = Designer::where('id' , $request->input('user_id'))->first();
            $cat->is_published = $request->input('status');
            $cat->update();
            return response(['status' => 'success' , 'message' => 'successfully updated' , 'newStatus' => $request->input('status')]);
        }
        return response(['status' => 'error' , 'message' => 'Something went wrong! contact the administrator'] , 404);
    }
    // -------------------------------------------------------------------------------
    protected function removeFakeOnes(){
        $items = Designer::select('id' , 'img')->where('fake' , 'Y')->where('created_at' , '<=' , Carbon::now()->subDay())->get();

        foreach ($items as $item){
            if(!empty($item->img)){
                $this->imageDelete($item->img , $this->destinationPathOfMagazines);
            }
            $item->forceDelete();
        }

        return true;
    }
    // -------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------
    /** -------------------------------------------------------------------------- */
    protected function itemImageDelete ($slug){
        $news = Designer::where('slug' , '=' , $slug)->first();
        $this->imageDelete($news->img , $this->destinationPathOfMagazines);
        $news->update(array('img' => null));
        return redirect()->back();
    }
    /** -------------------------------------------------------------------------- */
    public function ajaxImageRemove(Request $request)
    {
        if($request->ajax()){
            $id = intval($request->input('id'));
            if($id){
                $item = Designer::whereId($id)->first();
                if($item->img){
                    @$this->imageDelete($item->img , $this->destinationPathOfMagazines);
                    $item->update(array('img' => null));
                    return response()->json([ 'status' => 'success' ,  'message' => trans('notify.DELETE_SUCCESS_NOTIFICATION')]);
                } else {
                    return response()->json([ 'status' => 'error' ,  'message' => trans('notify.DATA_NOT_PROVIDED_ERROR_NOTIFICATION')]);
                }
            }
            return response()->json([ 'status' => 'error' ,  'message' => trans('notify.DATA_NOT_PROVIDED_ERROR_NOTIFICATION')]);
        }
        return 'Forbidden';
    }
    /** -------------------------------------------------------------------------- */
    protected function Base64imageUpload($image , $destinationPath , $height = 465 , $width = 390){;

        $extension = getBase64extension($image);
        $convertedImage = Image::make(file_get_contents($image));

        $name = time().$extension;
        $convertedImage->resize($height, $width)->save($destinationPath.'/'.$name);
        return $name;
    }
    /** -------------------------------------------------------------------------- */
    protected function imageDelete ($fileName , $destinationPath)
    {
        $fullFileName = $destinationPath . '/' . $fileName;

        if (File::exists($fullFileName)) {
            @File::delete($fullFileName);
            return true;
        }
        return false;
    }
    /** -------------------------------------------------------------------------- */
}
