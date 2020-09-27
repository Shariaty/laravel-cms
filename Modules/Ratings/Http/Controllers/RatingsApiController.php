<?php

namespace Modules\Ratings\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Ratings\Rate;
use Modules\Stores\Store;

class RatingsApiController extends Controller
{
    public function rate(){
        $rate = new Rate(['rater_id' =>  2 , 'value' => 4]);
        $item = Store::find(3);

        if($item) {
            $uniqness = Rate::where('rater_id' , $rate->rater_id)
                ->where('ratable_id' , $item->id )
                ->where('ratable_type' , get_class($item) )
                ->count();

            if($uniqness == 0){
                $result = $item->rates()->save($rate);
                return response()->json(['success' => 'Your rate saved successfully']);
            }
            return response()->json(['error' => 'شما در حال حاضر به این آیتم رتبه داده اید']);
        }
        return response()->json(['error' => LBL_COMMON_ERROR]);
    }

    public function calculateRate( $ratable_id )
    {
        $storeRate = null;
        $store =  Store::find($ratable_id);

        if($store && $store->rates) {
            $rate = $store->rates->avg('value');
            $storeRate = number_format($rate , 1);
        }

        return $storeRate;
    }
}
