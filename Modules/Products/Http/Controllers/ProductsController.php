<?php

namespace Modules\Products\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Products\Age;
use Modules\Products\Images;
use Modules\Products\Product;
use Modules\Products\ProductCategory;
use Modules\Products\ProductOptionValue;
use Modules\Products\Unit;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;


class ProductsController extends Controller
{
    protected  $redirectPath = 'administrator/products/list';
    protected  $destinationPath = PATH_ROOT.'/uploads/admins/products/images';
    protected  $destinationPathOfProductFiles = PATH_ROOT.('/uploads/admins/products/attachments');

    protected $rules = array(
        'title'      => 'required|max:100',
        'categories' => 'max:5000' ,
        'options'    => 'array|max:10'
    );

    public function __construct()
    {
        parent::__construct();
    }
    // -------------------------------------------------------------------------------
    public function index()
    {
        return view('products::list')->with('title' , 'Products  List');
    }
    // -------------------------------------------------------------------------------
    public function anyData()
    {
        return Datatables::of(Product::select('*')->notFake()->parents()->with('category.attributes')->get())
            ->editColumn('is_published', function ($item) {
                return ($item->is_published == 'N') ?
                    '<button class="btn btn-xs btn-default status-change" data-status="'.$item->is_published.'" data-new="'.$item->id.'"><i class="fa fa-ban fa-1x text-danger"></i></button>' :
                    '<button class="btn btn-xs btn-default status-change" data-status="'.$item->is_published.'" data-new="'.$item->id.'"><i class="fa fa-check fa-1x text-success"></i></button>' ;
            })
            ->editColumn('visible_sku', function ($item) {
                return $item->visible_sku;
            })
            ->editColumn('desc', function ($item) {
                return str_limit($item->desc , 50);
            })
            ->editColumn('category', function ($product) {
                $final = '<span class="badge badge-default">بدون دسته بندی</span>';
                if ( $product->category && $product->category->title ) {
                    $final = '<span class="badge badge-success" style="font-family: Tahoma, Helvetica, Arial; margin: 2px; ">'.$product->category->title.'</span>';
                }
                return $final;
            })
            ->addColumn('subProducts' , function ($item) {
                if ($item->category && count($item->category->attributes) == 0) {
                    return '<a class="btn btn-xs btn-default" disabled data-toggle="tooltip" data-placement="top" title="'.LBL_RESTRICT_ADDING_SUB_PRODUCT.'"> Sub Products </a>';
                } else {
                    return '<a href="'.route('admin.subProducts.list', $item->sku).'" class="btn btn-xs btn-default" > Sub Products ( '. $item->sub_product_count .' ) </a>';
                }
            })
            ->addColumn('type' , function ($item) {
                switch ($item->type) {
                    case PRODUCT_TYPE_NORMAL        : return '<span class="badge badge-warning">محصول</span>';
                    case PRODUCT_TYPE_COMPLEX       : return '<span class="badge badge-info">محصول ترکیبی</span>';
                    case PRODUCT_TYPE_RAW_MATERIAL  : return '<span class="badge badge-danger">مواد اولیه</span>';
                    default : return 'None';
                }
            })
            ->addColumn('action' , function ($item) {
                return $this->render($item);
            })
			->rawColumns(['is_published' , 'action' , 'category' , 'type' , 'subProducts'])
            ->make(true);
    }
    // -------------------------------------------------------------------------------
    public function render( $item ) {
        $final = null;
        $final .= '<a href="'. route('admin.products.edit' , $item->sku).'" class="btn btn-xs btn-warning"><i class="fa fa-edit"></i></a>';
        $final .= '<a data-id="'.$item->sku.'" class="btn btn-xs red delete_btn"><i class="fa fa-trash"></i></a>';

        return $final;
    }
    // -------------------------------------------------------------------------------
    public function add()
    {
        $this->removeFakeOnes();
        $rand = mt_rand(111111 , 999999);
        $product = Product::create(array('sku' => $rand , 'fake' => 'Y'));

        return redirect(route('admin.products.edit' , $product->sku));
    }
    // -------------------------------------------------------------------------------
    public function edit(Product $product)
    {
        $rawProductsList = Product::onlyRaw()->pluck('title' , 'id');
        $unitList = Unit::pluck('title' , 'id');
        $subUnitList = array_prepend($unitList->toArray() , 'بدون واحد' , 0);

        $optionsList = [];
        $manufactureList= [];
        $images = $product->images;
        $object = new ProductCategoryController();
        $categories = $object->_generateCategoryList();
        $ageEnable = false;
        $ageFinal = null ;
        $selectedRaw = null;
        $selectedValue = null;
        if($product->age) {
            $ageFinal = [$product->age->start , $product->age->end];
        }
        if($product->category){
            $selectedOptions = $product->op()->pluck('value_id')->toArray();
            $optionsList = $this->getCategoryOptions($product->category , $selectedOptions);
            $selectedManufacture = $product->manufacture_id ? $product->manufacture_id : null;
            $manufactureList = $this->getCategoryManufactures($product->category , [$selectedManufacture]);
            $ageEnable = $product->category->has_age == 'Y' ? true : false;
        } else {
            $selectedOptions = [];
            $selectedManufacture = null;
        }
        if($product->bom && count($product->bom)){
            $selectedRaw = $product->bom[0]->rawProduct_id;
            $selectedValue = $product->bom[0]->value;
        }

        $selectedOtherCategories = null;
        if($product->otherCategories){
            $selectedOtherCategories =  $product->otherCategories()->pluck('category_id');
        }

        return view('products::edit' , compact('product' ,'images' , 'categories' , 'selectedOtherCategories',
            'optionsList' , 'selectedOptions' , 'ageFinal' , 'ageEnable' ,
            'selectedRaw' , 'selectedValue' ,
            'selectedManufacture' , 'manufactureList' , 'rawProductsList' , 'unitList' , 'subUnitList'))->with('title' , 'Edit Product : '.$product->visible_sku);
    }
    // -------------------------------------------------------------------------------
    public function update(Request $request , Product $product)
    {

        $this->validate($request, $this->rules);

        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
        } else {
            $request->merge(array('is_published' => 'N'));
        }

        if($request->has('has_limit') && $request->input('has_limit') === "on"){
            $request->merge(array('has_limit' => 'Y'));
            $request->merge(array('limit_value' => $request->input('limitValue')));
            $request->merge(array('limit_time' => $request->input('limitTime')));

        } else {
            $request->merge(array('has_limit' => 'N'));
            $request->merge(array('limit_value' => null ));
            $request->merge(array('limit_time' =>  0));
        }

        if($request->has('category')){
            if(intval($request->input('category') != 0))
                $request->merge(array('category_id' => intval($request->input('category')) ));
        }

        if($request->has('manufacturer')){
            $request->merge(array('manufacture_id' => intval($request->input('manufacturer')) ));
        }

        $cat = ProductCategory::whereId($request->input('category_id'))->first();

        if($cat && $cat->has_age == 'Y') {
            if($request->has('ageRange')){
                $myArray = explode(',', $request->input('ageRange'));
                $IntArray = stringArrayConvertToIntArray($myArray);

                $result = [
                    'start' => isset($IntArray[0]) ? $IntArray[0] : 0 ,
                    'end'   => isset($IntArray[1]) ? $IntArray[1] : 0 ,
                ];

                $age = new Age($result);
                $product->age()->delete();
                $product->age()->save($age);
            }
        } else {
            $product->age()->delete();
        }

        $request->merge(array('fake' => 'N'));

        if( $request->has('subUnit') && $request->input('subUnit') !== "0"){
            $request->merge (array( 'subUnit_id' => $request->input('subUnit')) );
        } else {
            $request->merge (array( 'conversion_factor' => null , 'subUnit_id' => null ));
        }


        DB::beginTransaction();

        try {
            $product->op()->delete();
            if($request->has('options')) {
                $request['variants'] = stringArrayConvertToIntArray($request['variants']);
                foreach ($request->input('options') as $opt) {
                    $product->op()->create(['value_id' => intval($opt)]);
                }
            }

            if($request->input('o_cat')) {
                $product->otherCategories()->sync($request->input('o_cat'));
            } else {
                $product->otherCategories()->sync([]);
            }

            if ($request->input('type') == PRODUCT_TYPE_RAW_MATERIAL && $request->has('build_from') && $request->has('value')){
                $product->bom()->delete();
                $product->bom()->create([
                    'rawProduct_id' => $request->input('build_from'),
                    'value' => floatval($request->input('value'))
                ]);
            } else {
                $product->bom()->delete();
            }

            $data = $request->except(['_token', 'category' , 'subUnit' ,'manufacturer' , 'options' , 'rawProduct_id' , 'value']);
            $product->update($data);
            DB::commit();
            $success = true;
        } catch (\Exception $e) {
            $success = false;
            DB::rollback();
        }

        if($success) {
            $request->session()->flash('success' , LBL_COMMON_UPDATE_SUCCESSFUL);
        } else {
            $request->session()->flash('error', LBL_COMMON_ERROR);
        }

        return redirect(route('admin.products.list'));

    }
    // -------------------------------------------------------------------------------
    public function delete(Product $product)
    {
        if($product->delete()){
            return response()->json(['status' => 'success', 'message' => LBL_COMMON_DELETE_SUCCESSFUL]);
        }
        return response()->json(['status' => 'error', 'message' => LBL_COMMON_DELETE_ERROR]);
    }
    // -------------------------------------------------------------------------------
    protected function statusUpdate(Request $request){
        if($request->has('user_id') && $request->has('status')){
            $item = Product::where('id' , $request->input('user_id'))->first();
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
        $store = Product::where('sku' , '=' ,$id)->first();

        $name = time().mt_rand(11111 , 99999).'.'.$file->getClientOriginalExtension();
        $img = Image::make($file->getRealPath());
        $upload_success = $img->resize(750, null , function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->save($this->destinationPath.'/'.$name);



        if( $upload_success ) {
            $store->images()->create(['img' => $name , 'size' => $file->getSize()]);
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
        $items = Product::select('id' , 'file')->where('fake' , 'Y')
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
                    $item = Product::whereId($id)->first();
                    if($item){
                        $file = $request->file('file');
                        $name = $this->uploadFile($file , $item);

                        if($item->file)
                            $this->removeFile($item);

                        $item->update(array('file' => $name));
                    }
                    $result = array('success' => true , 'message' => $name , 'slug' => $item->sku);
                }
                return response()->json($result);
            } else {
                return response()->json(array('error' => true , 'message' => [ 'File is not available or it is damaged , in this case you can use another file' ] ));
            }
        }

        return 'forbidden';
    }
    // -------------------------------------------------------------------------------
    public function magazineFileRemove($item)
    {
        if($this->removeFile($item)){
            return back()->with('info' , trans('notify.DELETE_SUCCESS_NOTIFICATION'));
        }
        return back()->with('error' , trans('notify.DATA_NOT_PROVIDED_ERROR_NOTIFICATION'));
    }
    // -------------------------------------------------------------------------------
    protected function uploadFile ($file , $item){

        $extension = $file->getClientOriginalExtension();
        $fileName = Carbon::now()->toDateString().'--'.rand(11111,99999).'.'.$extension; // Make File name

        $file->move($this->destinationPathOfProductFiles,$fileName);
        return $fileName;
    }
    // -------------------------------------------------------------------------------
    protected function removeFile ($item){
        $product = Product::whereId($item)->first();
        if($product->file){
            $fullFileName = $this->destinationPathOfProductFiles.'/'.$product->file ;

            if(File::exists($fullFileName)){
                @File::delete($fullFileName);
                $product->update(array('file' => null));
                return true;
            }
        }
        return false;
    }
    // -------------------------------------------------------------------------------
    protected function magazineFileView(Product $product){

        $headers = array(
//            'Content-Disposition' => 'inline',
            'Content-Type' => 'application/pdf'
        );
        $fullFileName = $this->destinationPathOfProductFiles.'/'.$product->file;

//            return response()->download($fullFileName);
        return response()->file($fullFileName, $headers);
    }
    // AttachmentFile ----------------------------------------------------------------


    public function ajaxGetValues(Request $request)
    {
        $variantID = $request->input('variantName');

        $values = AttributeValue::select('id' , 'title')->where('attribute_id' , $variantID)->get();
        $final=[];
        if(count($values)){
            foreach ($values as $value) {
                $final[] = ['id' => $value->id , 'text' => $value->title];
            }
        }

        return response()->json($final);
    }

    public function AjaxGetOptions(Request $request)
    {
        $categoryId = $request->input('categoryId');
        $category = ProductCategory::whereId($categoryId)->first();

        $ageStatus = false;
        if($category) {
            $ageStatus = $category->has_age == 'Y' ? true : false ;
        }

        $final = [ 'options' => $this->getCategoryOptions($category) ,
                   'manufactures' => $this->getCategoryManufactures($category) ,
                   'ageStatus' => $ageStatus
        ] ;

        return response()->json($final);
    }

    protected function getCategoryManufactures($category , $selecteds = null ){
        $final = null;

        if($category) {
            if(count($category->manufactures)) {
                foreach ($category->manufactures as $manufacture) {

                    if($selecteds) {
                        $final[$manufacture->id][] = [ "id" => $manufacture->id , "text" => $manufacture->title , 'selected' => in_array($manufacture->id , $selecteds)];
                    } else {
                        $final[$manufacture->id][] = [ "id" => $manufacture->id , "text" => $manufacture->title];
                    }

                }
            }
        }

        return $final;
    }

    protected function getCategoryOptions($category , $selecteds = null ){
        $final = null;

        if($category) {
            if(count($category->options)) {
                foreach ($category->options as $option) {

                    $final[ $option->id ] = [ "text" => $option->title ];
                    if(count($option->values)) {
                        foreach ($option->values as $val) {
                            if($selecteds) {
                                $final[$option->id]["children"][] = [ "id" => $val->id , "text" => $val->title , 'selected' => in_array($val->id , $selecteds)];
                            } else {
                                $final[$option->id]["children"][] = [ "id" => $val->id , "text" => $val->title];
                            }
                        }
                    }
                }
            }
        }

        return $final;
    }

}
