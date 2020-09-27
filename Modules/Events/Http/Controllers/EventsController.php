<?php

namespace Modules\Events\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Modules\Events\Events;

//use Modules\News\News;
//use Modules\News\NewsCat;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\File;


class EventsController extends Controller
{
    protected  $redirectPath = 'administrator/events/list';
    protected  $destinationPathOfNews = PATH_ROOT.('/uploads/admins/events-pictures');

    // -------------------------------------------------------------------------------
    public function index()
    {
        $events = Events::orderBy('created_at')->paginate(20);
        return view('events::list' , compact('events'))->with('title' , 'Event List');
    }
    // -------------------------------------------------------------------------------
    public function anyData()
    {
        return Datatables::of(Events::select(['id' , 'is_published' , 'slug' , 'title' , 'views' , 'created_at']))
            ->editColumn('created_at', function ($news) {
                return $news->created_at->format('Y/m/d');
            })
            ->editColumn('is_published', function ($news) {
                return ($news->is_published == 'N') ?
                '<button class="btn btn-xs btn-default status-change" data-status="'.$news->is_published.'" data-new="'.$news->id.'"><i class="fa fa-ban fa-1x text-danger"></i></button>' :
                '<button class="btn btn-xs btn-default status-change" data-status="'.$news->is_published.'" data-new="'.$news->id.'"><i class="fa fa-check fa-1x text-success"></i></button>' ;
            })
            ->editColumn('views', function ($news) {
                return '<span class="badge badge-danger">'.$news->views.'</span>';
            })
            ->addColumn('action' , function ($news) {
                return $this->render($news);
            })
            ->rawColumns(['is_published' , 'action' , 'views'])
            ->make(true);
    }
    // -------------------------------------------------------------------------------
    public function render( $event ) {
        $final = null;
        $final .= '<a href="'.route('admin.events.edit' , $event->slug).'" class="btn btn-xs btn-warning"><i class="fa fa-edit"></i></a>';
        $final .= '<a data-id="'.$event->id.'" class="btn btn-xs red delete_btn"><i class="fa fa-trash"></i></a>';

        return $final;
    }
    // -------------------------------------------------------------------------------
    public function add()
    {
        $now = Carbon::now()->second(0);
        return view('events::add' , compact('now'))->with('title' , 'Event Create');
    }
    // -------------------------------------------------------------------------------
    public function create(Request $request)
    {
        $this->validator($request->all())->validate();

        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
        } else {
            $request->merge(array('is_published' => 'N'));
        }

        DB::beginTransaction();
        try {
            if(!empty($request->file('image'))) {
                $name = $this->imageUpload($request->file('image') , $this->destinationPathOfNews , null , 500);

                if(!empty($name)){
                    $request->merge(array('img' => $name));
                }
            }

            $data = $request->except(['_token' , 'image']);

            Events::create($data);
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
    }
    // -------------------------------------------------------------------------------
    public function edit(Events $event)
    {
        return view('events::edit' , compact('event'))->with('title' , 'Edit: '.$event->title);
    }
    // -------------------------------------------------------------------------------
    public function update(Request $request , Events $event)
    {
        if($request->has('title')){
            $request->merge(array('slug' => slug_utf8($request->input('title'))));
        }

        $this->validatorUpdate($request->all() , $event)->validate();

        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
        } else {
            $request->merge(array('is_published' => 'N'));
        }

        DB::beginTransaction();
        try {

            if(!empty($request->file('image'))) {

                if(!empty($event->img)){
                    $this->imageDelete($event->img , $this->destinationPathOfNews);
                }

                $name = $this->imageUpload($request->file('image') , $this->destinationPathOfNews , null , 500);
                if(!empty($name)){
                    $request->merge(array('img' => $name));
                }
            }

            $data = $request->except(['_token' , 'image']);

            $event->update($data);
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
    protected function delete($event_id){
        $event = Events::where('id' , $event_id)->first();
        if($event->delete()){
            return response()->json(['status' => 'success', 'message' => 'Item successfully removed']);
        }
        return response()->json(['status' => 'error', 'message' => 'There was problem in removing this item!']);
    }
    // -------------------------------------------------------------------------------
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'title' => 'required|max:100',
            'event_date'  => 'required',
            'image'   => 'image|mimes:jpeg,png,jpg|max:1024',
//            'body'   => 'required'
        ]);
    }
    // -------------------------------------------------------------------------------
    protected function validatorUpdate(array $data , $event)
    {
        return Validator::make($data, [
            'title' => 'required|max:100',
//            'slug'  => 'required|max:100|unique:news,slug,'.$news->id,
            'image'   => 'image|mimes:jpeg,png,jpg|max:1024'
        ]);
    }
    // -------------------------------------------------------------------------------
    protected function statusUpdate(Request $request){
        if($request->has('user_id') && $request->has('status')){
            $user = Events::where('id' , $request->input('user_id'))->first();
            $user->is_published = $request->input('status');
            $user->update();
            return response(['status' => 'success' , 'message' => 'successfully updated' , 'newStatus' => $request->input('status')]);
        }
        return response(['status' => 'error' , 'message' => 'Something went wrong! contact the administrator'] , 404);

    }
    // -------------------------------------------------------------------------------
    protected function eventImageDelete(Events $event){
        $this->imageDelete($event->img , $this->destinationPathOfNews);
        $event->update(array('img' => null));
        return redirect()->back();
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
