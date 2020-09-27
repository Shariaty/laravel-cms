<?php

namespace Modules\Portfolio\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Portfolio\Designer;
use Modules\Portfolio\Images;
use Modules\Portfolio\Portfolio;
use Modules\Portfolio\PortfolioCategory;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;


class PortfolioController extends Controller
{
    protected  $redirectPath = 'administrator/portfolio/list';
    protected  $destinationPath = PATH_ROOT.'/uploads/admins/portfolio/images';
    protected  $destinationPathOfProductFiles = PATH_ROOT.('/uploads/admins/portfolio/attachments');

    protected $rules = array(
        'title'      => 'required|max:100',
        'year'      => 'required|int',
        'category'   => 'required' ,
        'options'    => 'array|max:10'
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
        return view('portfolio::list')->with('title' , 'Portfolio  List');
    }
    // -------------------------------------------------------------------------------
    public function anyData()
    {
        return Datatables::of(Portfolio::select('*')->notFake()->with('category')->get())
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
            ->editColumn('is_published', function ($item) {
                return ($item->is_published == 'N') ?
                    '<button class="btn btn-xs btn-default status-change" data-status="'.$item->is_published.'" data-new="'.$item->id.'"><i class="fa fa-ban fa-1x text-danger"></i></button>' :
                    '<button class="btn btn-xs btn-default status-change" data-status="'.$item->is_published.'" data-new="'.$item->id.'"><i class="fa fa-check fa-1x text-success"></i></button>' ;
            })
            ->addColumn('action' , function ($item) {
                return $this->render($item);
            })
            ->rawColumns(['is_published', 'created_at' , 'action' , 'category'])
            ->make(true);
    }
    // -------------------------------------------------------------------------------
    public function render( $item ) {
        $final = null;
        $final .= '<a href="'. route('admin.portfolio.edit' , $item->id).'" class="btn btn-xs btn-warning"><i class="fa fa-edit"></i></a>';
        $final .= '<a data-id="'.$item->id.'" class="btn btn-xs red delete_btn"><i class="fa fa-trash"></i></a>';

        return $final;
    }
    // -------------------------------------------------------------------------------
    public function add()
    {
        $this->removeFakeOnes();
        $product = Portfolio::create(array('fake' => 'Y'));

        return redirect(route('admin.portfolio.edit' , $product->id));
    }
    // -------------------------------------------------------------------------------
    public function edit(Portfolio $product)
    {
        $locales = Config::get('translatable.localeList');
        $images = $product->images;
        $object = new PortfolioCategoryController();
        $categories = $object->_generateCategoryList();
        $selectedValue = null;

        return view('portfolio::edit' , compact('product' ,'images' , 'categories', 'selectedValue' , 'locales'))->with('title' , 'Edit Product : '.$product->visible_sku);
    }
    // -------------------------------------------------------------------------------
    public function update(Request $request , Portfolio $product)
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

        if($request->has('category')){
            if(intval($request->input('category') != 0))
                $request->merge(array('category_id' => intval($request->input('category')) ));
        }

        $request->merge(array('fake' => 'N'));

//        DB::beginTransaction();
        $data = $request->except(['_token', 'category' , 'value']);

        $product->update($data);

        $success = true;

//        try {


            // Sync Keywords
//            $keywords = $request->input('keywords');
//
//            $finalKeywords = [];
//            if ($keywords){
//                foreach ($keywords as $key => $value) {
//                    $tag = Tag::whereId($value)->first();
//                    if ($tag) {
//                        array_push($finalKeywords , $tag->id);
//                    } else {
//                        $newTag = Tag::create(['tag_name' => $value]);
//                        if ($newTag) {
//                            array_push($finalKeywords , $newTag->id);
//                        }
//                    }
//                }
//            }
//
//            $product->tags()->sync($finalKeywords);
            // Sync Keywords

//            DB::commit();
//            $success = true;
//        } catch (\Exception $e) {
//            $success = false;
//            DB::rollback();
//        }

        if($success) {
            $request->session()->flash('success' , LBL_COMMON_UPDATE_SUCCESSFUL);
        } else {
            $request->session()->flash('error', LBL_COMMON_ERROR);
        }

        return redirect(route('admin.portfolio.list'));

    }
    // -------------------------------------------------------------------------------
    public function delete(Portfolio $product)
    {
        if($product->delete()){
            return response()->json(['status' => 'success', 'message' => LBL_COMMON_DELETE_SUCCESSFUL]);
        }
        return response()->json(['status' => 'error', 'message' => LBL_COMMON_DELETE_ERROR]);
    }
    // -------------------------------------------------------------------------------
    protected function statusUpdate(Request $request){
        if($request->has('user_id') && $request->has('status')){
            $item = Portfolio::where('id' , $request->input('user_id'))->first();
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
            return response($validation->errors()->first() , 400);
        }

        $file = $request->file('file');
        $id = $request->input('store_id');
        $store = Portfolio::whereId($id)->first();

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
        $items = Portfolio::select('id' , 'file')->where('fake' , 'Y')
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
            if($request->file('file'))  {
                $rules = array('file' => 'required|mimes:pdf,dox,mp4,ogx,oga,ogv,ogg,webm');
                $validator = Validator::make($request->all(), $rules);

                if($validator->fails()){
                    $errors = $validator->messages()->all();
                    $result = array('success' => false , 'message' => $errors);
                } else {
                    $id = intval($request->input('id'));
                    $type = $request->input('type');
                    $item = Portfolio::whereId($id)->first();
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
    protected function removeFile ($item , $type = null){
        $product = Portfolio::whereId($item)->first();

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
    protected function magazineFileView($id , $type){

        $headers = array(
//            'Content-Disposition' => 'inline',
            'Content-Type' => 'application/pdf'
        );

        $portfolio = Portfolio::where('id' , $id)->first();
        $file = $type === "sheet" ? $portfolio->sheet : $portfolio->file;

        $fullFileName = $this->destinationPathOfProductFiles.'/'.$file;

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
        $category = PortfolioCategory::whereId($categoryId)->first();

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
