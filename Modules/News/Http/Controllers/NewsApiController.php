<?php

namespace Modules\News\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\News\News;
use Modules\News\NewsCat;

class NewsApiController extends Controller
{
    public function getAllNews()
    {
        $news = News::published()->with('categories')->paginate(10);
        return response()->json($news);

    }

    public function getSingleNews($slug){

        $news = News::whereSlug($slug)->with('categories')->first();

        return response()->json($news);
    }

    public function getAllcategories()
    {
        $categories = NewsCat::published()->with('news')->withCount('news')->orderBy('news_count' , 'DESC')->get();
        return response()->json($categories);

    }

}
