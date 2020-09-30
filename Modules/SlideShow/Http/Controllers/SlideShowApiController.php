<?php

namespace Modules\SlideShow\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\SlideShow\SlideShow;
use Modules\SlideShow\SlideShowCategory;


class SlideShowApiController extends Controller
{
    public function slides( $slug = null ){

        $slides = [];
        if ($slug) {
           $category =  SlideShowCategory::where('slug' , $slug)->where('is_published' , 'Y')->first();
           $slides = $category->slides()->where('is_published' , 'Y')->orderBy('sort' , 'ASC')->get();
        } else {
            $slides = SlideShow::where('is_published' , 'Y')->whereHas('category', function ($query) {
                $query->where('is_published', 'Y');
            })->orderBy('sort' , 'ASC')->get();
        }

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
