<?php

namespace App\Http\Controllers\api\visitors;

use App\Http\Controllers\Controller;
use App\Visitor;
use Illuminate\Http\Request;


class visitorController extends Controller
{
    public function saveVisitLog(Request $request)
    {
        $visitor_ip = geoip()->getClientIP();
        $location = geoip()->getLocation($visitor_ip);
        $location = array_replace($location->toArray() , ['ip' => ip2long($location->ip)]);

        if( !$location['default'] ){
            Visitor::create($location);
        }

        return response()
            ->json(['status' => 1 , 'message' => 'visitor log saved successfully' , 'location' => $location]);

    }
}
