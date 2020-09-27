<?php

namespace Modules\Magazines\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Modules\Magazines\Magazine;
use Modules\Magazines\MagazineCategory;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\File;


class MagazinesController extends Controller
{
    protected  $redirectPath = 'administrator/magazines/list';
    protected  $destinationPathOfMagazines = PATH_ROOT.('/uploads/admins/magazine-pictures');
    protected  $destinationPathOfMagazineFiles = PATH_ROOT.('/uploads/admins/magazine-files');

    // -------------------------------------------------------------------------------
    public function index()
    {
        $items = Magazine::orderBy('created_at')->notfake()->paginate(20);
        return view('magazines::list' , compact('items'))->with('title' , 'Magazine List');
    }
    // -------------------------------------------------------------------------------
    public function anyData()
    {
        return Datatables::of(Magazine::select(['id' , 'is_published' , 'title' , 'downloads' , 'created_at'])->notfake()->with('categories'))
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
            ->editColumn('downloads', function ($news) {
                return '<span class="badge badge-danger">'.$news->downloads.'</span>';
            })
            ->addColumn('action' , function ($news) {
                return $this->render($news);
            })
            ->rawColumns(['is_published' , 'action' , 'categories' , 'downloads'])
            ->make(true);
    }
    // -------------------------------------------------------------------------------
    public function render( $news ) {
        $final = null;
        $final .= '<a href="'.route('admin.magazines.edit' , $news->id).'" class="btn btn-xs btn-warning"><i class="fa fa-edit"></i></a>';
        $final .= '<a data-id="'.$news->id.'" class="btn btn-xs red delete_btn"><i class="fa fa-trash"></i></a>';

        return $final;
    }
    // -------------------------------------------------------------------------------
    public function add()
    {
        $this->removeFakeOnes();
        $rand = mt_rand(00000 , 99999);
        $news = Magazine::create(array('slug' => 'Undifined'.$rand , 'fake' => 'Y'));

        return redirect(route('admin.magazines.edit' , $news->id));
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

            $news = Magazine::create($data);

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
            $request->session()->flash('Success', trans('notify.CREATE_SUCCESS_NOTIFICATION'));
        } else {
            $request->session()->flash('Error', trans('notify.CREATE_FAILED_NOTIFICATION'));
        }

        return redirect($this->redirectPath);


        DB::beginTransaction();
        try {

            DB::commit();
            $success = true;
        } catch (\Exception $e) {
            $success = false;
            DB::rollback();
        }
        if($success) {
        } else {
        }
    }
    // -------------------------------------------------------------------------------
    public function edit($id)
    {
        $news = Magazine::whereId($id)->first();
        $categories = MagazineCategory::published()->orderBy('title' , 'ASC')->pluck('title','id');

        if( count($news->categories)) {
            $selectedCategory = $news->categories;
        } else {
            $selectedCategory = '';
        }
        return view('magazines::edit' , compact('news' , 'categories' , 'selectedCategory'))->with('title' , 'Edit: '.$news->title);
    }
    // -------------------------------------------------------------------------------
    public function update(Request $request , $slug)
    {
        $news = Magazine::where('slug' , '=' , $slug)->first();
        $this->validatorUpdate($request->all() , $news)->validate();

        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
        } else {
            $request->merge(array('is_published' => 'N'));
        }

        DB::beginTransaction();
        try {

            if(!empty($request->input('finalFile'))) {
                if(!empty($news->img)){
                    $this->imageDelete($news->img , $this->destinationPathOfMagazines);
                }

                $name = $this->Base64imageUpload($request->input('finalFile') , $this->destinationPathOfMagazines , 213 , 318);
                if(!empty($name)){
                    $request->merge(array('img' => $name));
                }
            }
            if(!empty($request->input('title'))){
                $slug = slug_utf8($request->input('title'));
                $request->merge(array('slug' => $slug));
            }

            $request->merge(array('fake' => 'N'));
            $data = $request->except(['_token' , 'news_categories' , 'image' , 'finalFile']);

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
        $result = Magazine::whereId($blog)->first();

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
            'slug'  => 'required|max:100|unique:magazines,slug,NULL,id,deleted_at,NULL',
            'news_categories' => 'required|array',
            'image'   => 'image|mimes:jpeg,png,jpg|max:1024'
        ]);
    }
    // -------------------------------------------------------------------------------
    protected function validatorUpdate(array $data , $news)
    {
        return Validator::make($data, [
            'title' => 'required|max:100',
            'slug'  => 'unique:magazines,slug,'.$news->id.',id,deleted_at,NULL',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:1024'
        ]);
    }
    // -------------------------------------------------------------------------------
    protected function statusUpdate(Request $request){
        if($request->has('user_id') && $request->has('status')){
            $user = Magazine::where('id' , $request->input('user_id'))->first();
            $user->is_published = $request->input('status');
            $user->update();
            return response(['status' => 'success' , 'message' => 'successfully updated' , 'newStatus' => $request->input('status')]);
        }
        return response(['status' => 'error' , 'message' => 'Something went wrong! contact the administrator'] , 404);

    }
    // -------------------------------------------------------------------------------
    protected function removeFakeOnes(){
        $items = Magazine::select('id' , 'img' , 'file')->where('fake' , 'Y')->where('created_at' , '<=' , Carbon::now()->subDay())->get();

        foreach ($items as $item){
            if(!empty($item->img)){
                $this->imageDelete($item->img , $this->destinationPathOfMagazines);
            }
            if(!empty($item->file)){
                $this->removeFile($item->id);
            }
            $item->forceDelete();
        }

        return true;
    }
    // -------------------------------------------------------------------------------




    // -------------------------------------------------------------------------------
    public function categoryList()
    {
        $categories = MagazineCategory::orderBy('created_at')->paginate(20);
        return view('magazines::category.list' , compact('categories'))->with('title' , 'Categories List');
    }
    // -------------------------------------------------------------------------------
    public function categoryAdd()
    {
        return view('magazines::category.add')->with('title' , 'Category Create');
    }
    // -------------------------------------------------------------------------------
    public function categoryCreate(Request $request)
    {
        if($request->has('slug')){
            $request->merge(array('slug' => slug_utf8($request->input('slug'))));
        }

        Validator::make($request->all(), [
            'title' => 'required|max:100',
            'slug'  => 'required|max:100|unique:magazines_categories,slug,NULL,id,deleted_at,NULL'
        ])->validate();


        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
        } else {
            $request->merge(array('is_published' => 'N'));
        }

        $data = $request->except(['_token']);
        MagazineCategory::create($data);

        $request->session()->flash('Success', trans('notify.CREATE_SUCCESS_NOTIFICATION'));
        return redirect(route('admin.magazines.categories'));
    }
    // -------------------------------------------------------------------------------
    public function categoryEdit(MagazineCategory $cat)
    {
        return view('magazines::category.edit' , compact('cat'))->with('title' , 'Edit: '.$cat->title);
    }
    // -------------------------------------------------------------------------------
    public function categoryUpdate(Request $request , MagazineCategory $cat)
    {
        if($request->has('slug')){
            $request->merge(array('slug' => slug_utf8($request->input('slug'))));
        }

        Validator::make($request->all(), [
            'title' => 'required|max:100',
            'slug'  => 'required|max:100|required|max:100|unique:magazines_categories,slug,'.$cat->id.',id,deleted_at,NULL'
        ])->validate();

        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
        } else {
            $request->merge(array('is_published' => 'N'));
        }

        $data = $request->except(['_token']);

        $cat->update($data);

        $request->session()->flash('Success', trans('notify.UPDATE_SUCCESS_NOTIFICATION'));
        return redirect(route('admin.magazines.categories'));
    }
    // -------------------------------------------------------------------------------
    protected function categoryDelete(MagazineCategory $cat , Request $request){
        $cat->delete();
        $request->session()->flash('success', trans('notify.DELETE_SUCCESS_NOTIFICATION'));
        return redirect(route('admin.magazines.categories'));
    }
    // -------------------------------------------------------------------------------
    protected function categoryStatusUpdate (Request $request)
    {
        if($request->has('user_id') && $request->has('status')){
            $cat = MagazineCategory::where('id' , $request->input('user_id'))->first();
            $cat->is_published = $request->input('status');
            $cat->update();
            return response(['status' => 'success' , 'message' => 'successfully updated' , 'newStatus' => $request->input('status')]);
        }
        return response(['status' => 'error' , 'message' => 'Something went wrong! contact the administrator'] , 404);
    }
    // -------------------------------------------------------------------------------


    /** -------------------------------------------------------------------------- */
    protected function itemImageDelete ($slug){
        $news = Magazine::where('slug' , '=' , $slug)->first();
        $this->imageDelete($news->img , $this->destinationPathOfMagazines);
        $news->update(array('img' => null));
        return redirect()->back();
    }
    /** -------------------------------------------------------------------------- */
    public function ajaxImageRemove(Request $request)
    {
        if($request->ajax()){

            $id = intval($request->input('id'));

            if($id){
                $item = Magazine::whereId($id)->first();
                if($item->img){
                    @$this->imageDelete($item->img , $this->destinationPathOfMagazines);
                    $item->update(array('img' => null));
                    return response()->json([ 'status' => 'success' ,  'message' => trans('notify.DELETE_SUCCESS_NOTIFICATION')]);
                } else {
                    return response()->json([ 'status' => 'error' ,  'message' => trans('notify.DATA_NOT_PROVIDED_ERROR_NOTIFICATION')]);
                }
            }
            return response()->json([ 'status' => 'error' ,  'message' => trans('notify.DATA_NOT_PROVIDED_ERROR_NOTIFICATION')]);
        }
        return 'Forbidden';
    }
    /** -------------------------------------------------------------------------- */
    protected function Base64imageUpload($image , $destinationPath , $height = 318 , $width = 213){;

        $extension = getBase64extension($image);
        $convertedImage = Image::make(file_get_contents($image));

        $name = time().$extension;
        $convertedImage->resize($height, $width)->save($destinationPath.'/'.$name);
        return $name;
    }
    /** -------------------------------------------------------------------------- */
    protected function imageDelete ($fileName , $destinationPath)
    {
        $fullFileName = $destinationPath . '/' . $fileName;

        if (File::exists($fullFileName)) {
            @File::delete($fullFileName);
            return true;
        }
        return false;
    }
    /** -------------------------------------------------------------------------- */



    
    
    /** ------------------------------------------------------------------------------------------------------------- */
    public function ajaxFileUpload(Request $request)
    {

        if($request->ajax()) {
            if($request->hasFile('file'))  {
                $rules = array('file' => 'required|mimes:pdf,docx|max:2048');
                $validator = Validator::make($request->all(), $rules);

                if($validator->fails()){
                    $errors = $validator->messages()->all();
                    $result = array('success' => false , 'message' => $errors);
                } else {
                    $id = intval($request->input('id'));
                    $item = Magazine::whereId($id)->first();
                    if($item){
                        $file = $request->file('file');
                        $name = $this->uploadFile($file , $item);

                        if($item->file)
                            $this->removeFile($item);

                        $item->update(array('file' => $name));
                    }
                    $result = array('success' => true , 'message' => $name , 'slug' => $item->slug);
                }
                return response()->json($result);
            } else {
                return response()->json(array('error' => true , 'message' => [ 'File is not available or it is damaged , in this case you can use another file' ] ));
            }
        }

        return 'forbidden';
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function magazineFileRemove($item)
    {
        if($this->removeFile($item)){
            return back()->with('info' , trans('notify.DELETE_SUCCESS_NOTIFICATION'));
        }
        return back()->with('error' , trans('notify.DATA_NOT_PROVIDED_ERROR_NOTIFICATION'));
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    protected function profileResumeView()
    {

        $headers = array(
//            'Content-Disposition' => 'inline',
            'Content-Type' => 'application/pdf'
        );

        $filePath = PATH_ROOT.('uploads/candidates/profile-resumes');
        $fullFileName = $filePath.'/'.getCurrentUserProfile()->candidate_resume;

        if(!auth()->check()){
            return 'Forbidden';
        } else {
//            return response()->download($fullFileName);
            return response()->file($fullFileName , $headers);
        }
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    protected function uploadFile ($file , $item){

        $extension = $file->getClientOriginalExtension();
        $fileName = Carbon::now()->toDateString().'--'.rand(11111,99999).'.'.$extension; // Make File name

        $file->move($this->destinationPathOfMagazineFiles,$fileName);
        return $fileName;
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    protected function removeFile ($item){
        $magazine = Magazine::whereId($item)->first();
        if(isset($magazine->file) &&  $magazine->file){
            $fullFileName = $this->destinationPathOfMagazineFiles.'/'.$magazine->file ;

            if(File::exists($fullFileName)){
                @File::delete($fullFileName);
                $magazine->update(array('file' => null));
                return true;
            }
        }
        return false;
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    protected function magazineFileView(Magazine $magazine){

            $headers = array(
//            'Content-Disposition' => 'inline',
                'Content-Type' => 'application/pdf'
            );

            $fullFileName = $this->destinationPathOfMagazineFiles.'/'.$magazine->file;
            return response()->download($fullFileName);
//                return response()->file($fullFileName, $headers);
    }
}
