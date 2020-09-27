<?php

namespace Modules\Stores\Http\Controllers;

use App\City;
use App\Country;
use App\Http\Controllers\Controller;
use App\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Stores\Images;
use Modules\Stores\Store;
use Modules\Stores\StoreType;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use ZanySoft\Zip\Zip;


class StoresController extends Controller
{
    protected  $redirectPath = 'administrator/stores/list';
    protected  $destinationPath = PATH_ROOT.'/uploads/admins/stores/images';

    protected  $destinationPathofVtour = PATH_ROOT.'/uploads/admins/stores/vtours';
    protected  $destinationPathofVtourFiles = PATH_ROOT.('/uploads/admins/stores/vtours');


    protected $storeRules = array(
        'title'     => 'required',
        'type'      => 'required|array' ,
        'city_id'   => 'required',
        'phone'     => 'required',
        'desc'      => '',
        'address'   => 'max:100',
        'lat'       => 'required',
        'lng'       => 'required',
        'rate'      => 'required'
    );

    public function __construct()
    {
        parent::__construct();
    }
    // -------------------------------------------------------------------------------
    public function index()
    {
        return view('stores::list')->with('title' , 'Stores  List');
    }
    // -------------------------------------------------------------------------------
    public function anyData()
    {
        return Datatables::of(Store::select(['id' , 'st_number' , 'title' , 'desc' , 'status', 'created_at'])->with('storeTypes')->orderBy('created_at')->get())
            ->editColumn('desc', function ($store) {
                return str_limit($store->desc , 50);
            })
            ->editColumn('type', function ($store) {
                $final = null;
                foreach ($store->storeTypes as $type) {
                    $final .= '<span class="badge badge-success" style="font-family: Tahoma, Helvetica, Arial; margin: 2px; ">'.$type->title.'</span>';
                }
                return $final;
            })
            ->editColumn('status', function ($store) {
                return ($store->status == 'N') ?
                    '<button class="btn btn-xs btn-default status-change" data-status="'.$store->status.'" data-new="'.$store->id.'"><i class="fa fa-ban fa-1x text-danger"></i></button>' :
                    '<button class="btn btn-xs btn-default status-change" data-status="'.$store->status.'" data-new="'.$store->id.'"><i class="fa fa-check fa-1x text-success"></i></button>' ;
            })
            ->addColumn('action' , function ($store) {
                return $this->render($store);
            })
			->rawColumns(['status' , 'action' , 'type'])
            ->make(true);
    }
    // -------------------------------------------------------------------------------
    public function render( $store ) {
        $final = null;
        $final .= '<a href="'. route('admin.stores.edit' , $store->st_number).'" class="btn btn-xs btn-warning"><i class="fa fa-edit"></i></a>';
        $final .= '<a data-id="'.$store->st_number.'" class="btn btn-xs red delete_btn"><i class="fa fa-trash"></i></a>';

        return $final;
    }
    // -------------------------------------------------------------------------------
    public function add()
    {
        $store = null;
        $types = StoreType::pluck('title' , 'id');
        $districts = Store::where('city_id' ,1)->pluck('district' , 'district');
        $cities =  City::pluck('name' , 'id');
        $states  = State::pluck('name' , 'id');
        $tehranCities = City::where('state_id' , 1)->pluck('name' , 'id');

        return view('stores::add' , compact('store' ,'states', 'cities' , 'districts' , 'tehranCities' , 'types'))->with('title' , 'Add/Update Store Profile');
    }
    // -------------------------------------------------------------------------------
    public function save(Request $request)
    {
        if($request->has('type'))
            $request['type'] = stringArrayConvertToIntArray($request['type']);

        if(!$request->has('district'))
            $request['district'] = null;

        $this->validate($request, $this->storeRules);

        $location = new Point($request->input('lat'), $request->input('lng'));
        $request->merge(['st_number' => rand(11111 , 99999) , 'location' => $location ]);

        DB::beginTransaction();
        try {

            $data = $request->except(['_token', 'type']);
            $store = Store::create($data);

            if($request->input('type')) {
                $store->storeTypes()->sync($request->input('type'));
            }

            DB::commit();
            $success = true;
        } catch (\Exception $e) {
            $success = false;
            DB::rollback();
        }
        if($success) {
            $request->session()->flash('Success', trans('notify.CREATE_SUCCESS_NOTIFICATION'));
        } else {
            $request->session()->flash('Error', trans('notify.CREATE_FAILED_NOTIFICATION'));
        }

        return redirect(route('admin.stores.list'));
    }
    // -------------------------------------------------------------------------------
    public function edit(Store $store)
    {
        $types = StoreType::pluck('title' , 'id');
        $cities =  City::pluck('name' , 'id');
        $districts = Store::where('city_id' , $store->city_id)->pluck('district' , 'district');
        $images = $store->images;

        if( count($store->storeTypes)) {
            $selectedCategory = $store->storeTypes;
        } else {
            $selectedCategory = '';
        }

        return view('stores::edit' , compact('store' ,'states' , 'cities' , 'districts' , 'images' , 'types' , 'selectedCategory'))->with('title' , 'Edit Store Profile of '.$store->title);
    }
    // -------------------------------------------------------------------------------
    public function update(Request $request , Store $store)
    {

        if($request->has('type'))
            $request['type'] = stringArrayConvertToIntArray($request['type']);

        if(!$request->has('district'))
            $request['district'] = null;

        $this->validate($request, $this->storeRules);

        $location = new Point($request->input('lat'), $request->input('lng'));
        $request->merge(['location' => $location ]);

        $data = $request->except(['_token', 'type']);
        $store->update($data);

        if($request->input('type')) {
            $store->storeTypes()->sync($request->input('type'));
        }

        $request->session()->flash('Success', trans('notify.CREATE_SUCCESS_NOTIFICATION'));
        return redirect(route('admin.stores.list'))->with('success' , LBL_COMMON_UPDATE_SUCCESSFUL);
    }
    // -------------------------------------------------------------------------------
    public function delete(Store $store)
    {
        if($store->delete()){
            return response()->json(['status' => 'success', 'message' => LBL_COMMON_DELETE_SUCCESSFUL]);
        }
        return response()->json(['status' => 'error', 'message' => LBL_COMMON_DELETE_ERROR]);
    }
    // -------------------------------------------------------------------------------
    protected function statusUpdate(Request $request){
        if($request->has('user_id') && $request->has('status')){
            $item = Store::where('id' , $request->input('user_id'))->first();
            $item->status = $request->input('status');
            $item->update();
            return response(['status' => 'success' , 'message' => LBL_COMMON_UPDATE_SUCCESSFUL , 'newStatus' => $request->input('status')]);
        }
        return response(['status' => 'error' , 'message' => LBL_COMMON_ERROR] , 404);
    }
    // -------------------------------------------------------------------------------
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
        $store = Store::where('st_number' , '=' ,$id)->first();

        $name = time().mt_rand(11111 , 99999).'.'.$file->getClientOriginalExtension();
        $img = Image::make($file->getRealPath());
        $upload_success = $img->resize(750, null , function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->save($this->destinationPath.'/'.$name);



        if( $upload_success ) {
            $store->images()->create(['img' => $name , 'size' => $file->getClientSize()]);
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
    public function getCities(Request $request)
    {
        $finalCities = [];

        $stateId = $request->input('stateId');
        $state = State::where('id' , $stateId)->with('cities')->first();
        if ($state) {
            foreach ($state->cities as $city) {
                $finalCities[] = (object)[ 'text' =>  $city->name , 'id' => $city->id ];
            }
        }
        return response()->json(['cities' => $finalCities , $stateId , $state]);
    }

    // AttachmentFile ----------------------------------------------------------------
    public function ajaxFileUpload(Request $request)
    {
        if($request->ajax()) {
            if($request->hasFile('file')) {
                $rules = array('file' => 'required|mimes:zip|max:10000');
                $validator = Validator::make($request->all(), $rules);

                if($validator->fails()){
                    $errors = $validator->messages()->all();
                    $result = array('success' => false , 'message' => $errors);
                } else {
                    $id = intval($request->input('id'));
                    $item = Store::where('st_number' , $id)->first();
                    if( $item ){
                        if($item->vtour) {
                            $this->removeFile($item->st_number);
                        }
                        $file = $request->file('file');
                        $this->uploadFile($file , $item);
                        $item->update(array('vtour' => 'v-'.$item->st_number));
                    }
                    $result = array('success' => true , 'message' => 'v-'.$item->st_number , 'fullVtour' => $item->fullVtour);
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
        $fileName = $item->st_number.'.'.$extension; // Make File name
        $folderName = $this->destinationPathofVtourFiles.'/v-'.$item->st_number;
        @File::deleteDirectory($this->destinationPathofVtourFiles.'/v-'.$item->st_number);
        $result = File::makeDirectory($this->destinationPathofVtourFiles.'/v-'.$item->st_number , 0775, true);
        if($result) {
            $move = $file->move( $folderName , $fileName);
            if($move) {
                $zip = Zip::create($folderName.'/'.$fileName);
                $result = $zip->extract($folderName);
                if ($result) {
                    @File::delete($folderName.'/'.$fileName);
                }
            }
            return $fileName;
        }
        return false;
    }
    // -------------------------------------------------------------------------------
    protected function removeFile ($item){
        $store = Store::where('st_number' , $item)->first();
        if($store && $store->vtour){
            $result = File::deleteDirectory($this->destinationPathofVtourFiles.'/v-'.$store->st_number);
            if($result){
                $store->update(array('vtour' => null));
                return true;
            }
        }
        return false;
    }
    // -------------------------------------------------------------------------------
    protected function magazineFileView(Store $store){

        $headers = array(
//            'Content-Disposition' => 'inline',
            'Content-Type' => 'application/pdf'
        );
        $fullFileName = $this->destinationPathOfProductFiles.'/'.$product->file;

//            return response()->download($fullFileName);
        return response()->file($fullFileName, $headers);
    }
    // AttachmentFile ----------------------------------------------------------------

}
