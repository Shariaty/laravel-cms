<?php

namespace App\Http\Controllers\Api\Visitors;

use App\Http\Controllers\Controller;
use App\Visitor;
use Illuminate\Http\Request;
use Torann\GeoIP\Facades\GeoIP;


class visitorController extends Controller
{

    public function __construct()
    {
        $this->middleware('throttle:8,1')->only('saveVisitLog');
    }

    public function saveVisitLog(Request $request)
    {
        $visitor_ip = GeoIP::getClientIP();
        $location = GeoIP::getLocation($visitor_ip);
        $location = array_replace($location->toArray() , ['ip' => ip2long($location->ip)]);
        if( !$location['default'] ){
            Visitor::create($location);
        }

        return response()
            ->json(['status' => 1 , 'message' => 'visitor log saved successfully' , 'location' => $location]);

    }
}
