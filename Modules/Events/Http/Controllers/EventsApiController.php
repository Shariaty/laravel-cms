<?php

namespace Modules\Events\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Events\Events;


class EventsApiController extends Controller
{
    public function getSingleEvent($event_slug = null)
    {
        $event = Events::where('slug' , $event_slug)->first();
        return response()->json($event);
    }

}
