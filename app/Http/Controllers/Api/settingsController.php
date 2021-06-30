<?php

namespace App\Http\Controllers\Api;

use App\Facade\Facades\OilSettings;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Events\Events;
use Modules\Portfolio\Portfolio;
use Modules\Products\Manufacture;

class settingsController extends Controller
{
    public function getAllSettings()
    {
        $settings = OilSettings::getApiStyle(['site' , 'social']);
//        $brands = Manufacture::all();glo

//        $newEvent = null;
//        if (hasModule('Events')){
//            $event = Events::published()->whereDate('event_date', '>=', Carbon::now())->orderBy('event_date' , 'ASC')->first();
//            if ($event) {
//                $newEvent = $event;
//            }
//        }

        $pageObject = new pagesController();
        $aboutData = $pageObject->getSpecificPageInAllLanguages('about-us');
        $about = (isset($aboutData->original) && $aboutData->original) ? $aboutData->original : null;

        $final = array_prepend( $settings , true , 'isLoaded' );
        $finalData = array_prepend( $final , $about , 'about' );

        return response()->json($finalData);
    }

}
