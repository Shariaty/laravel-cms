<?php

namespace Modules\SlideShow\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\SlideShow\SlideShow;
use Modules\Validity\Validity;


class SlideShowApiController extends Controller
{
    public function slides(){
        $slides = SlideShow::where('is_published' , 'Y')->orderBy('sort' , 'ASC')->get();
        $finalSlides = [];

        foreach ($slides as $slide) {
            if ($slide->file){
                array_push($finalSlides , ['title' => $slide->title, 'desc' => $slide->desc , 'image' => $slide->fullImage , 'link' => $slide->link]);
            }
        }

        return response()->json(['status' => 'success' , 'slides' => $finalSlides]);
    }
    // -------------------------------------------------------------------------------

}
