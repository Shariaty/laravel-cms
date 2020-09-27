<?php

namespace Modules\Validity\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Validity\Validity;


class ValidityApiController extends Controller
{
    protected function confirm(Request $request){

        $identification = $request->input('identification');
        if($identification){
            $validity = Validity::where('identification' , $identification)->first();
            if ($validity){
                return response(['status' => 'success' , 'message' => 'کالای وارد شده اصیل است.' , 'title' => $validity->title , 'date' => $validity->date ]);
            }
            return response(['status' => 'error' , 'message' => 'متاسفانه کالای مورد نظر اصالت ندارد!'] , 403);
        }

        return response(['status' => 'error' , 'message' => 'اطلاعات کافی مهیا نیست'] , 404);
    }
    // -------------------------------------------------------------------------------

}
