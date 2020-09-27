<?php

namespace App\Http\Controllers\api;

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
        $brands = Manufacture::all();

        $newEvent = null;
        if (hasModule('Events')){
            $event = Events::published()->whereDate('event_date', '>=', Carbon::now())->orderBy('event_date' , 'ASC')->first();
            if ($event) {
                $newEvent = $event;
            }
        }

        $portfolio = [];
        if (hasModule('Portfolio')){
            $items = Portfolio::published()->notFake()->orderBy('created_at' , 'ASC')->get();
            foreach ($items as $item){
                array_push($portfolio , [
                    'id' => $item->id,
                    'title' => $item->title,
                    'desc' => $item->desc,
                    'meta' => $item->meta,
                    'image' => $item->cover_image,
                ]);
            }
        }

        $pageObject = new pagesController();
        $aboutData = $pageObject->getSpecificPage('about-us');
        $about = (isset($aboutData->original) && $aboutData->original) ? $aboutData->original : null;
        
        $final = array_prepend( $settings , true , 'isLoaded' );
        $finalData = array_prepend( $final , $brands , 'brands' );
        $finalData = array_prepend( $finalData , $newEvent , 'event' );
        $finalData = array_prepend( $finalData , $about , 'about' );
        $finalData = array_prepend( $finalData , $portfolio , 'portfolio' );

        return response()->json($finalData);
    }

}
