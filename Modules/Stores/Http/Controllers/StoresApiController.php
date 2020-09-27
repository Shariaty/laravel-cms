<?php

namespace Modules\Stores\Http\Controllers;
use App\City;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CountryCities\CountryCitiesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Ratings\Http\Controllers\RatingsApiController;
use Modules\Stores\Images;
use Modules\Stores\Store;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use Grimzy\LaravelMysqlSpatial\Types\Point;



class StoresApiController extends Controller
{
    protected  $destinationPath = PATH_ROOT.'/uploads/admins/stores/images';

    protected $storeRules = array(
        'title'     => 'required',
        'type'      => 'array' ,
        'city_id'   => 'required',
        'state_id'  => 'required',
        'phone'     => 'required',
        'desc'      => 'required',
        'address'   => 'required|max:100',
        'lat'       => 'required',
        'lng'       => 'required'
    );

    public function getAllStores()
    {
        $stores = Store::select('st_number' , 'lat' , 'lng')->where('city_id' , 1)->with('storeTypes')->published()->get();
        return response()->json($stores);
    }

    public function getSearchedStores(Request $request)
    {
        $district = $request->input('district');
        $city = $request->input('city');
        $categories = $request->input('categories');

        $catIds = [];
        if ($categories) {
            foreach ($categories as $category) {
                $catIds[] = [$category['value']];
            }
        }

        $builder = Store::query();

        if ($city) {
            $builder->where('city_id' , $city);
        }

        if ($district) {
            $builder->where('district', $district);
        }

        if ($catIds) {
            $builder->whereHas('storeTypes', function ($query) use ($catIds) {
                $query->whereIn('store_types.id', $catIds);
            });
        }

        $stores = $builder->published()->get();

        return response()->json($stores);

    }

    public function getSpecificStore($st_number)
    {
        $result = Store::where('st_number' , $st_number)->with('images')->withCount('images')->with('storeTypes')->first();

        $selectedCategories = [];
        if($result->storeTypes) {
            foreach ($result->storeTypes as $storeType) {
                $selectedCategories[] = (object)[ 'label' =>  $storeType->p_title , 'value' => $storeType->id ];
            }
        }

        $rate = 0;
        $comments = [];

        if( hasModule('Ratings') && $result ){
            $raters = new RatingsApiController();
            $rate =  $raters->calculateRate($result->id);
        }

        if( hasModule('Comments') && $result ){
            $comments = $result->comments()->approved()->get();
        }

        $result->setAttribute('rate', $rate);
        $result->setAttribute('comments', $comments);

        $cities = City::where('state_id' , $result->state_id )->get();
        $finalCities = [];

        foreach ($cities as $city) {
            $finalCities[] = (object)[ 'label' =>  $city->p_name , 'value' => $city->id , 'position' => $city->position ];
        }
        $countryCountroller = new CountryCitiesController();
        $result->setAttribute('states' , $countryCountroller->getStates());
        $result->setAttribute('selectedCategories' , $selectedCategories);
        $result->setAttribute('cities' , $finalCities);

        return response()->json($result);
    }

    public function createStoreByUser(Request $request)
    {
        if($request->has('selectedCategories') && count($request->input('selectedCategories'))) {
            $types = [];
            foreach ($request->input('selectedCategories') as $cat) {
                $types[] = $cat['value'];
            }
            $request['type'] = stringArrayConvertToIntArray($types);
        }

        $this->validate($request, $this->storeRules);
        $user = auth()->guard('api')->user();
        $location = new Point($request->input('lat'), $request->input('lng'));
        $request->merge([
            'st_number' => rand(11111 , 99999) ,
            'location' => $location ,
            'user_id' => $user->id ,
            'status' => 'N'
        ]);
        $request['madeBy'] = STORE_TYPE_MADE_BY_PUBLIC;


        DB::beginTransaction();
        try {

            $data = $request->except(['_token', 'type' , 'selectedCategories']);
            $store = Store::create($data);
            if(count($request['type'] > 1)) {
                $store->storeTypes()->sync($request['type']);
            }

            DB::commit();
            $success = true;
        } catch (\Exception $e) {
            dd($e->getMessage());
            $success = false;
            DB::rollback();
        }
        if($success) {
            return response()->json([
                'status' => 'success' ,
                'message' => trans('notify.CREATE_SUCCESS_NOTIFICATION'),
                'store_id' => $store->st_number
            ]);
        } else {
            return response()->json(['status' => 'error' , 'message' => LBL_COMMON_ERROR ]);
        }

    }

    public function getStoresListOfUser()
    {
        $user = auth()->guard('api')->user();

        $stores = [];
        if($user) {
            $stores = $user->stores;
        }

        return response()->json(['status' => 'success', 'stores' => $stores ]);
    }

    public function updateStore(Request $request)
    {

        $store = Store::where('st_number' , $request->input('st_number'))->first();

        if($store) {

            if($request->has('selectedCategories') && count($request->input('selectedCategories'))) {
                $types = [];
                foreach ($request->input('selectedCategories') as $cat) {
                    $types[] = $cat['value'];
                }
                $request['type'] = stringArrayConvertToIntArray($types);
            }

            $this->validate($request, $this->storeRules);
            $user = auth()->guard('api')->user();
            $location = new Point($request->input('lat'), $request->input('lng'));
            $request->merge([
                'location' => $location ,
                'user_id' => $user->id ,
                'status' => 'N'
            ]);

            DB::beginTransaction();
            try {
                $data = $request->except(['_token', 'type' , 'selectedCategories']);
                $store->update($data);
                if(count($request['type'] > 1)) {
                    $store->storeTypes()->sync($request['type']);
                }
                DB::commit();
                $success = true;
            } catch (\Exception $e) {
                dd($e->getMessage());
                $success = false;
                DB::rollback();
            }


            if($success) {
                return response()->json([
                    'status' => 'success' ,
                    'message' => 'اطلاعات با موفقیت بروز شد',
                    'store_id' => $store->st_number
                ]);
            } else {
                return response()->json(['status' => 'error' , 'message' => LBL_COMMON_ERROR ]);
            }
        }
        return response()->json(['status' => 'error' , 'message' => LBL_COMMON_ERROR ]);
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    /** ------------------------------------------------------------------------------------------------------------- */
    public function getAllImages(Request $request)
    {
        $st_number = $request->input('st_number');
        $store = Store::where('st_number' , $st_number)->with('images')->first();
        if ($store) {
            return response()->json([ 'status' => 'success' ,  'images' => $store->images ]);
        }
        return response()->json([ 'status' => 'error' ,  'message' => LBL_COMMON_ERROR]);
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function ajaxImageUpload(Request $request)
    {

//         return response()->json([ 'status' => 'success' ,  'data' => $request->all()]);
        $image = $request->input('image');
        $user = auth()->guard('api')->user();
        $store = Store::where('st_number' , $request->input('st_number'))->first();

        if($store && $image){
            $extension = getBase64extension($image);
            $convertedImage = Image::make(file_get_contents($image));
            $name = $this->imageUpload($convertedImage , $this->destinationPath , null , 300 , $extension);
            if($name) {
//                $store->images()->create(['img' => $name , 'size' => $file->getClientSize()]);
                $store->images()->create(['img' => $name ]);
                return response()->json([ 'status' => 'success' ,  'message' => LBL_COMMON_UPDATE_SUCCESSFUL]);
            } else {
                return response()->json([ 'status' => 'error' ,  'message' => LBL_COMMON_ERROR]);
            }
        }
//        return response()->json([ 'status' => 'error' ,  'data' => $request->all()]);
        return response()->json([ 'status' => 'error' ,  'message' => LBL_COMMON_ERROR]);
    }
    /** -------------------------------------------------------------------------- */
    /** -------------------------------------------------------------------------- */
    protected function imageUpload ($image , $destinationPath , $height = null , $width = 100 , $extension = '.jpg' ){
        $name = time().$extension;

        $image->resize($height, $width, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$name);

        return $name;
    }
    /** -------------------------------------------------------------------------- */
    protected function imageDelete ($fileName , $destinationPath)
    {
        $fullFileName = $destinationPath . '/' . $fileName;

        if (File::exists($fullFileName)) {
            File::delete($fullFileName);
            return true;
        }
        return false;
    }
    /** -------------------------------------------------------------------------- */
    public function ajaxImageRemove(Request $request)
    {

//        return response()->json([ 'status' => $request->input('imageId') ]);


        $image = Images::whereId($request->input('imageId'))->first();
        if($image){
            @$this->imageDelete($image->img , $this->destinationPath);
            $image->forceDelete();
            return response()->json([ 'status' => 'success' ,  'message' => LBL_COMMON_UPDATE_SUCCESSFUL]);
        }
        return response()->json([ 'status' => 'error' ,  'message' => LBL_COMMON_ERROR]);
    }
}
