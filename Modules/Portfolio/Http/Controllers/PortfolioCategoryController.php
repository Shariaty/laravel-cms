<?php

namespace Modules\Portfolio\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Portfolio\PortfolioCategory;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class PortfolioCategoryController extends Controller
{
    protected  $destinationPathOfImages = PATH_ROOT.('/uploads/admins/portfolio/categories');

    // -------------------------------------------------------------------------------
    public function categoryList()
    {
        $categories = PortfolioCategory::orderBy('created_at')->withCount('portfolio')
            ->where('parent' , 0)->get();
        return view('portfolio::category.list' , compact('categories'))->with('title' , 'Categories List');
    }
    // -------------------------------------------------------------------------------
    public function categoryAdd()
    {
        $parents = $this->_generateCategoryList();
        return view('portfolio::category.add' , compact('parents'))->with('title' , 'Category Create');
    }
    // -------------------------------------------------------------------------------
    public function categoryCreate(Request $request)
    {
        Validator::make($request->all(), [
            'title' => 'required|max:100'
        ])->validate();

        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
        } else {
            $request->merge(array('is_published' => 'N'));
        }

        DB::beginTransaction();
        try {
            if(!empty($request->file('image'))) {
                $name = $this->imageUpload($request->file('image') , $this->destinationPathOfImages , 700 );
                if(!empty($name)){
                    $request->merge(array('img' => $name));
                }
            }

            $data = $request->except(['_token' , 'image' , 'variants']);
            $item = PortfolioCategory::create($data);

            DB::commit();
            $success = true;
        } catch (\Exception $e) {
            dd($e);
            $success = false;
            DB::rollback();
        }
        if($success) {
            $request->session()->flash('Success', trans('notify.CREATE_SUCCESS_NOTIFICATION'));
        } else {
            $request->session()->flash('Error', trans('notify.CREATE_FAILED_NOTIFICATION'));
        }
        return redirect(route('admin.portfolio.categories'));
    }
    // -------------------------------------------------------------------------------
    public function categoryEdit(PortfolioCategory $cat)
    {
        $parents = $this->_generateCategoryList($cat->id);
        return view('portfolio::category.edit' , compact('cat' , 'parents'))->with('title' , 'Edit: '.$cat->title);
    }
    // -------------------------------------------------------------------------------
    public function categoryUpdate(Request $request , PortfolioCategory $cat)
    {
        Validator::make($request->all(), [
            'title' => 'required|max:100'
        ])->validate();

        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
        } else {
            $request->merge(array('is_published' => 'N'));
        }


        DB::beginTransaction();
        try {
            if(!empty($request->file('image'))) {
                if(!empty($cat->img)){
                    $this->imageDelete($cat->img , $this->destinationPathOfImages);
                }

                $name = $this->imageUpload($request->file('image') , $this->destinationPathOfImages , 700);
                if(!empty($name)){
                    $request->merge(array('img' => $name));
                }
            }

            $data = $request->except(['_token' , 'image']);

            $cat->update($data);
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

        return redirect(route('admin.portfolio.categories'));
    }
    // -------------------------------------------------------------------------------
    protected function categoryDelete(PortfolioCategory $cat , Request $request){
        $count = $cat->portfolio->count();

        if($count > 0) {
            return redirect(route('admin.portfolio.categories'))->with('warning' , trans('notify.CATEGORY_CONTAIN_ITEMS_WARNING'));
        }

        $cat->delete();
        $request->session()->flash('success', trans('notify.DELETE_SUCCESS_NOTIFICATION'));
        return redirect(route('admin.portfolio.categories'));
    }
    // -------------------------------------------------------------------------------
    protected function categoryStatusUpdate (Request $request)
    {
        if($request->has('user_id') && $request->has('status')){
            $cat = PortfolioCategory::where('id' , $request->input('user_id'))->first();
            $cat->is_published = $request->input('status');
            $cat->update();
            return response(['status' => 'success' , 'message' => 'successfully updated' , 'newStatus' => $request->input('status')]);
        }
        return response(['status' => 'error' , 'message' => 'Something went wrong! contact the administrator'] , 404);
    }
    // -------------------------------------------------------------------------------
    public function removePicture(PortfolioCategory $cat)
    {
        if(!empty($cat->img)){
            $this->imageDelete($cat->img , $this->destinationPathOfImages);
        }
        $cat->update(['img' => null]);
        return redirect(route('admin.portfolio.categories.edit' , ['portfolioCat' => $cat , 'status' => 'picture'] ));
    }
    // -------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------
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
    // -------------------------------------------------------------------------------
    public function _generateCategoryList( $remove = null ){
        $parentsCategory = PortfolioCategory::parents()->get();

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
