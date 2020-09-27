<?php

namespace Modules\Articles\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Articles\Blog;
use Modules\Articles\BlogCategory;


class ArticlesApiController extends Controller
{
    public function getAll(Request $request)
    {
        $builder = Blog::query();
        $term = $request->all();

        if(!empty($term['category'])){
            $category_slug = $term['category'];

            $builder->whereHas('categories', function ($query) use ($category_slug) {
                $query->where('slug', $category_slug);
            });
        }

        if(!empty($term['time'])){
            $year = $term['time'];
            $builder->where( DB::raw('YEAR(created_at)') , $year);
        }

        $news = $builder->with('categories')->paginate(10);
        return response()->json($news);
    }

    public function getSingle($slug){
        $news = Blog::whereSlug($slug)->with('categories')->first();
        return response()->json($news);
    }

    public function getAllcategories()
    {
        $final = [];

        $final['list'] = BlogCategory::published()->withCount('posts')->orderBy('posts_count' , 'DESC')->get();
//        $final['timings'] = DB::table('posts')->where('deleted_at' , null)->select(DB::raw('YEAR(created_at) year'))->distinct()->get();
        $final['timings'] = Blog::select( DB::raw('YEAR(created_at) year') )->groupBy('year')->orderBy('year' , 'DESC')->get();

        return response()->json($final);
    }
}
