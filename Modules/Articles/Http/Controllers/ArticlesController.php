<?php

namespace Modules\Articles\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Modules\Articles\Blog;
use Modules\Articles\BlogCategory;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\File;


class ArticlesController extends Controller
{
    protected  $redirectPath = 'administrator/articles/list';
    protected  $destinationPathOfBlog = PATH_ROOT.('/uploads/admins/article-pictures');

    // -------------------------------------------------------------------------------
    public function index()
    {
        $news = Blog::orderBy('created_at')->paginate(20);
        return view('articles::list' , compact('news'))->with('title' , 'Blog List');
    }
    // -------------------------------------------------------------------------------
    public function anyData()
    {
        return Datatables::of(Blog::select(['id' , 'is_published' , 'title' , 'views' , 'created_at'])->with('categories'))
            ->editColumn('created_at', function ($news) {
                return $news->created_at->format('Y/m/d');
            })
            ->editColumn('is_published', function ($news) {
                return ($news->is_published == 'N') ?
                    '<button class="btn btn-xs btn-default status-change" data-status="'.$news->is_published.'" data-new="'.$news->id.'"><i class="fa fa-ban fa-1x text-danger"></i></button>' :
                    '<button class="btn btn-xs btn-default status-change" data-status="'.$news->is_published.'" data-new="'.$news->id.'"><i class="fa fa-check fa-1x text-success"></i></button>' ;
            })
            ->editColumn('categories', function ($news) {
                $final = null;
                foreach ($news->categories as $cat) {
                    $final .= '<span class="badge badge-success" style="font-family: Tahoma, Helvetica, Arial; margin: 2px; ">'.$cat->title.'</span>';
                }
                return $final;
            })
            ->editColumn('views', function ($news) {
                return '<span class="badge badge-danger">'.$news->views.'</span>';
            })
            ->addColumn('action' , function ($news) {
                return $this->render($news);
            })
            ->rawColumns(['is_published', 'categories' , 'action' , 'views'])
            ->make(true);
    }
    // -------------------------------------------------------------------------------
    public function render( $news ) {
        $final = null;
        $final .= '<a href="'.route('admin.articles.edit' , $news->id).'" class="btn btn-xs btn-warning"><i class="fa fa-edit"></i></a>';
        $final .= '<a data-id="'.$news->id.'" class="btn btn-xs red delete_btn"><i class="fa fa-trash"></i></a>';

        return $final;
    }
    // -------------------------------------------------------------------------------
    public function add()
    {
        $categories = BlogCategory::published()->orderBy('title' , 'ASC')->pluck('title', 'id');
        return view('articles::add' , compact('categories'))->with('title' , 'Blog Create');
    }
    // -------------------------------------------------------------------------------
    public function create(Request $request)
    {
        if($request->has('news_categories'))
            $request['news_categories'] = stringArrayConvertToIntArray($request['news_categories']);

        $this->validator($request->all())->validate();

        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
        } else {
            $request->merge(array('is_published' => 'N'));
        }

        DB::beginTransaction();
        try {
            if(!empty($request->file('image'))) {
                $name = $this->imageUpload($request->file('image') , $this->destinationPathOfBlog , null , 500);

                if(!empty($name)){
                    $request->merge(array('img' => $name));
                }
            }

            $data = $request->except(['_token' , 'news_categories' , 'image']);

            $news = Blog::create($data);

            if((count($request->input('news_categories')) >= 1)) {
                $news->categories()->sync($request->input('news_categories'));
            }

            DB::commit();
            $success = true;
        } catch (\Exception $e) {
            dd($e);
            $success = false;
            DB::rollback();
        }
        if($success) {
            $request->session()->flash('Success', trans('notify.CREATE_SUCCESS_NOTIFICATION'));
        } else {
            $request->session()->flash('Error', trans('notify.CREATE_FAILED_NOTIFICATION'));
        }

        return redirect($this->redirectPath);
    }
    // -------------------------------------------------------------------------------
    public function edit($id)
    {
        $news = Blog::whereId($id)->first();
        $categories = BlogCategory::published()->orderBy('title' , 'ASC')->pluck('title','id');

        if( count($news->categories)) {
            $selectedCategory = $news->categories;
        } else {
            $selectedCategory = '';
        }
        return view('articles::edit' , compact('news' , 'categories' , 'selectedCategory'))->with('title' , 'Edit: '.$news->title);
    }
    // -------------------------------------------------------------------------------
    public function update(Request $request , $slug)
    {
        $news = Blog::where('slug' , '=' , $slug)->first();
        $this->validatorUpdate($request->all() , $news)->validate();

        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
        } else {
            $request->merge(array('is_published' => 'N'));
        }

        DB::beginTransaction();
        try {

            if(!empty($request->file('image'))) {

                if(!empty($news->img)){
                    $this->imageDelete($news->img , $this->destinationPathOfBlog);
                }

                $name = $this->imageUpload($request->file('image') , $this->destinationPathOfBlog , null , 500);
                if(!empty($name)){
                    $request->merge(array('img' => $name));
                }
            }
            $data = $request->except(['_token' , 'news_categories' , 'image']);

            $news->update($data);
            if((count($request->input('news_categories')) >= 1)) {
                $news->categories()->sync($request->input('news_categories'));
            }
            DB::commit();
            $success = true;
        } catch (\Exception $e) {
            $success = false;
            DB::rollback();
        }
        if($success) {
            $request->session()->flash('Success', trans('notify.UPDATE_SUCCESS_NOTIFICATION'));
        } else {
            $request->session()->flash('Error', trans('notify.UPDATE_FAILED_NOTIFICATION'));
        }

        return redirect($this->redirectPath);
    }
    // -------------------------------------------------------------------------------
    protected function delete($blog){
        $result = Blog::whereId($blog)->first();

        if($result->delete()){
            return response()->json(['status' => 'success', 'message' => 'Item successfully removed']);
        }
        return response()->json(['status' => 'error', 'message' => 'There was problem in removing this item!']);
    }
    // -------------------------------------------------------------------------------
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'title' => 'required|max:100',
//            'slug'  => 'required|max:100|unique:posts,slug,NULL,id,deleted_at,NULL',
            'news_categories' => 'required|array',
            'image'   => 'image|mimes:jpeg,png,jpg|max:1024',
            'body'   => 'required'
        ]);
    }
    // -------------------------------------------------------------------------------
    protected function validatorUpdate(array $data , $news)
    {
        return Validator::make($data, [
            'title' => 'required|max:100',
//            'slug'  => 'required|max:100|unique:posts,slug,'.$news->id.',id,deleted_at,NULL',
            'image'   => 'image|mimes:jpeg,png,jpg|max:1024',
            'body'   => 'required'
        ]);
    }
    // -------------------------------------------------------------------------------
    protected function statusUpdate(Request $request){
        if($request->has('user_id') && $request->has('status')){
            $user = Blog::where('id' , $request->input('user_id'))->first();
            $user->is_published = $request->input('status');
            $user->update();
            return response(['status' => 'success' , 'message' => 'successfully updated' , 'newStatus' => $request->input('status')]);
        }
        return response(['status' => 'error' , 'message' => 'Something went wrong! contact the administrator'] , 404);

    }
    // -------------------------------------------------------------------------------
    protected function newsImageDelete ($slug){
        $news = Blog::where('slug' , '=' , $slug)->first();
        $this->imageDelete($news->img , $this->destinationPathOfBlog);
        $news->update(array('img' => null));
        return redirect()->back();
    }
    // -------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------
    public function categoryList()
    {
        $categories = BlogCategory::orderBy('created_at')->paginate(20);
        return view('articles::category.list' , compact('categories'))->with('title' , 'Categories List');
    }
    // -------------------------------------------------------------------------------
    public function categoryAdd()
    {
        return view('articles::category.add')->with('title' , 'Category Create');
    }
    // -------------------------------------------------------------------------------
    public function categoryCreate(Request $request)
    {
        Validator::make($request->all(), [
            'title' => 'required|max:100',
//            'slug'  => 'required|max:100|unique:posts_categories,slug,NULL,id,deleted_at,NULL'
        ])->validate();


        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
        } else {
            $request->merge(array('is_published' => 'N'));
        }

        $data = $request->except(['_token' , 'news_categories']);
        BlogCategory::create($data);

        $request->session()->flash('Success', trans('notify.CREATE_SUCCESS_NOTIFICATION'));
        return redirect(route('admin.articles.categories'));
    }
    // -------------------------------------------------------------------------------
    public function categoryEdit(BlogCategory $cat)
    {
        return view('articles::category.edit' , compact('cat'))->with('title' , 'Edit: '.$cat->title);
    }
    // -------------------------------------------------------------------------------
    public function categoryUpdate(Request $request , BlogCategory $cat)
    {
        Validator::make($request->all(), [
            'title' => 'required|max:100',
//            'slug'  => 'required|max:100|required|max:100|unique:posts_categories,slug,'.$cat->id.',id,deleted_at,NULL'
        ])->validate();

        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
        } else {
            $request->merge(array('is_published' => 'N'));
        }

        $data = $request->except(['_token']);

        $cat->update($data);

        $request->session()->flash('Success', trans('notify.UPDATE_SUCCESS_NOTIFICATION'));
        return redirect(route('admin.articles.categories'));
    }
    // -------------------------------------------------------------------------------
    protected function categoryDelete(BlogCategory $cat , Request $request){
        $cat->delete();
        $request->session()->flash('success', trans('notify.DELETE_SUCCESS_NOTIFICATION'));
        return redirect(route('admin.articles.categories'));
    }
    // -------------------------------------------------------------------------------
    protected function categoryStatusUpdate (Request $request)
    {
        if($request->has('user_id') && $request->has('status')){
            $cat = BlogCategory::where('id' , $request->input('user_id'))->first();
            $cat->is_published = $request->input('status');
            $cat->update();
            return response(['status' => 'success' , 'message' => 'successfully updated' , 'newStatus' => $request->input('status')]);
        }
        return response(['status' => 'error' , 'message' => 'Something went wrong! contact the administrator'] , 404);
    }
    // -------------------------------------------------------------------------------


    // -------------------------------------------------------------------------------
    protected function imageUpload ($image , $destinationPath , $height = null , $width = 100){
        $name = time().'.'.$image->getClientOriginalExtension();
        $img = Image::make($image->getRealPath());

        $img->resize($height, $width, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$name);

        return $name;
    }
    // -------------------------------------------------------------------------------
    protected function imageDelete ($fileName , $destinationPath)
    {
        $fullFileName = $destinationPath . '/' . $fileName;

        if (File::exists($fullFileName)) {
            File::delete($fullFileName);
            return true;
        }
        return false;
    }
    // -------------------------------------------------------------------------------

}
