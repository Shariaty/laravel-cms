<?php

namespace Modules\Products\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Products\Attribute;
use Modules\Products\Manufacture;
use Modules\Products\Option;
use Modules\Products\ProductCategory;


class ProductCategoryController extends Controller
{
    // -------------------------------------------------------------------------------
    public function categoryList()
    {
        $categories = ProductCategory::orderBy('created_at')->withCount('products')->with('attributes')
            ->with('options')->where('parent' , 0)->paginate(20);

        return view('products::category.list' , compact('categories'))->with('title' , 'Products Categories List');
    }
    // -------------------------------------------------------------------------------
    public function categoryAdd()
    {
        $attributes = Attribute::pluck('title' , 'id');
        $options = Option::pluck('title' , 'id');
        $manufactures = Manufacture::pluck('title' , 'id');

        $parents = $this->_generateCategoryList();

        return view('products::category.add' , compact('attributes' , 'options' , 'parents' , 'manufactures'))->with('title' , 'Category Create');
    }
    // -------------------------------------------------------------------------------
    public function categoryCreate(Request $request)
    {
        if($request->has('variants') && $request->input('variants') != null) {
            $data =  explode( ',' , $request->input('variants') );
            $request['variants'] = stringArrayConvertToIntArray($data);
        }

        if($request->has('options')) {
            $request['options'] = stringArrayConvertToIntArray($request['options']);
        }

        if($request->has('manufactures')) {
            $request['manufactures'] = stringArrayConvertToIntArray($request['manufactures']);
        }

        Validator::make($request->all(), [
            'title' => 'required|max:100',
            'variants' => 'max:'.CONFIG_LIMIT_COMBINATION_PRODUCTS_MODULE ,
            'options' => 'array|max:10',
            'manufactures' => 'array|max:10'

        ])->validate();

        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
        } else {
            $request->merge(array('is_published' => 'N'));
        }

        DB::beginTransaction();
        try {
            $data = $request->except(['_token' , 'variants']);

            $item = ProductCategory::create($data);

            if($request->input('variants') && $request->input('variants') != null) {
                $res = [];
                foreach ($request->input('variants') as $key => $value) {
                    $res[$value] = [ 'order' => $key];
                }
                $item->attributes()->sync($res);
            } else {
                $item->attributes()->sync([]);
            }

            if($request->input('options')) {
                $item->options()->sync($request->input('options'));
            }

            if($request->input('manufactures')) {
                $item->manufactures()->sync($request->input('manufactures'));
            }

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
        return redirect(route('admin.products.categories'));
    }
    // -------------------------------------------------------------------------------
    public function categoryEdit(ProductCategory $cat)
    {
        if( count($cat->attributes)) {
            $selectedAttributes = $cat->attributes;
        } else {
            $selectedAttributes = '';
        }
        $attributes = Attribute::pluck('title' , 'id');

        if( count($cat->options)) {
            $selectedOptions = $cat->options;
        } else {
            $selectedOptions = '';
        }

        if( count($cat->manufactures)) {
            $selectedManufactures = $cat->manufactures;
        } else {
            $selectedManufactures = '';
        }

        $options = Option::pluck('title' , 'id');
        $manufactures = Manufacture::pluck('title' , 'id');
        $parents = $this->_generateCategoryList($cat->id);

        return view('products::category.edit' , compact('cat' ,
            'selectedAttributes' , 'parents' ,'attributes' ,
            'options' , 'selectedOptions' , 'manufactures' ,'selectedManufactures'))->with('title' , 'Edit: '.$cat->title);
    }
    // -------------------------------------------------------------------------------
    public function categoryUpdate(Request $request , ProductCategory $cat)
    {
        if($request->has('variants') && $request->input('variants') != null) {
            $data =  explode( ',' , $request->input('variants') );
            $request['variants'] = stringArrayConvertToIntArray($data);
        }

        if($request->has('options')) {
            $request['options'] = stringArrayConvertToIntArray($request['options']);
        }

        if($request->has('manufactures')) {
            $request['manufactures'] = stringArrayConvertToIntArray($request['manufactures']);
        }


        Validator::make($request->all(), [
            'title' => 'required|max:100',
            'variants' => 'max:'.CONFIG_LIMIT_COMBINATION_PRODUCTS_MODULE ,
            'options' => 'array|max:10',
            'manufactures' => 'array|max:10'
        ])->validate();


        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
        } else {
            $request->merge(array('is_published' => 'N'));
        }


        DB::beginTransaction();
        try {
            $data = $request->except(['_token' , 'variants' , 'options']);
            $cat->update($data);

            if($request->input('variants') && count($request->input('variants'))) {
                $res = [];
                foreach ($request->input('variants') as $key => $value) {
                    $res[$value] = [ 'order' => $key];
                }
                $cat->attributes()->sync($res);
            } else {
                $cat->attributes()->sync([]);
            }

            if($request->input('options')) {
                $cat->options()->sync($request->input('options'));
            } else {
                $cat->options()->sync([]);
            }

            if($request->input('manufactures')) {
                $cat->manufactures()->sync($request->input('manufactures'));
            } else {
                $cat->manufactures()->sync([]);
            }

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

        return redirect(route('admin.products.categories'));
    }
    // -------------------------------------------------------------------------------
    protected function categoryDelete(ProductCategory $cat , Request $request){
        $count = $cat->products->count();

        if($count > 0) {
            return redirect(route('admin.products.categories'))->with('warning' , trans('notify.CATEGORY_CONTAIN_ITEMS_WARNING'));
        }

        $cat->delete();
        $request->session()->flash('success', trans('notify.DELETE_SUCCESS_NOTIFICATION'));
        return redirect(route('admin.products.categories'));
    }
    // -------------------------------------------------------------------------------
    protected function categoryStatusUpdate (Request $request)
    {
        if($request->has('user_id') && $request->has('status')){
            $cat = ProductCategory::where('id' , $request->input('user_id'))->first();
            $cat->is_published = $request->input('status');
            $cat->update();
            return response(['status' => 'success' , 'message' => 'successfully updated' , 'newStatus' => $request->input('status')]);
        }
        return response(['status' => 'error' , 'message' => 'Something went wrong! contact the administrator'] , 404);
    }
    // -------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------
    public function _generateCategoryList( $remove = null ){
        $parentsCategory = ProductCategory::parents()->get();

//        $final = [0 => 'None'];
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
