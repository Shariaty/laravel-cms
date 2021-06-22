<?php

namespace Modules\Contacts\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Contacts\Contact;
use Yajra\Datatables\Datatables;


class ContactsController extends Controller
{
    protected  $redirectPath = 'administrator/contacts/list';

    // -------------------------------------------------------------------------------
    public function index()
    {
        $items = Contact::orderBy('created_at')->paginate(20);
        return view('contacts::list' , compact('items'))->with('title' , 'Contact List');
    }
    // -------------------------------------------------------------------------------
    public function anyData()
    {
        return Datatables::of(Contact::select(['id' , 'is_read' , 'sender' , 'subject' , 'message' , 'created_at']))
            ->editColumn('created_at', function ($contact) {
                return $contact->created_at->format('Y/m/d');
            })
            ->editColumn('is_read', function ($contact) {
                return ($contact->is_read == 'N') ?
                    '<div id="icon-'.$contact->id.'"><i class="fa fa-eye-slash fa-1x text-danger"></i></div>' :
                    '<i class="fa fa-eye fa-1x text-success"></i>';
            })
            ->editColumn('message', function ($contact) {
                return '<span class="farsi">'.str_limit($contact->message , 75).'</span>';
            })
            ->addColumn('action' , function ($contact) {
                return $this->render($contact);
            })
            ->rawColumns(['is_read' , 'action' , 'message'])
            ->make(true);
    }
    // -------------------------------------------------------------------------------
    public function render( $contact ) {
        $final = null;
        $final .= '<a data-id="'.$contact->id.'" class="btn btn-xs red delete_btn"><i class="fa fa-trash"></i></a>';
        $final .= '<a data-id="'.$contact->id.'" data-sender="'.$contact->sender.'" data-seen="'.$contact->is_read.'" data-subject="'.$contact->subject.'" data-message="'.$contact->message.'" data-time="'.tarikhFarsi($contact->created_at).'" data-toggle="modal" data-target="#exampleModal" class="btn btn-xs btn-info"><i class="fa fa-search"></i></a>';
        return $final;
    }
    // -------------------------------------------------------------------------------
    public function view(Request $request , $slug)
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
            if(count($request->input('news_categories') > 1)) {
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
    protected function delete(Contact $contact){
        if($contact->delete()){
            return response()->json(['status' => 'success', 'message' => 'Item successfully removed']);
        }
        return response()->json(['status' => 'error', 'message' => 'There was problem in removing this item!']);
    }
    // -------------------------------------------------------------------------------
    protected function clearAll(){
        $ids = Contact::pluck('id')->toArray();
        Contact::whereIn('id', $ids)->delete();
        return redirect($this->redirectPath)->with('success' , 'All Items has been removed');
    }
    // -------------------------------------------------------------------------------
    protected function statusUpdate(Request $request){
        if($request->has('identifier') && $request->has('is_read')){
            $item = Contact::whereId($request->input('identifier'))->first();
            $item->is_read = $request->input('is_read');
            $item->update();
            return response(['status' => 'success' , 'message' => 'successfully updated']);
        }
        return response(['status' => 'error' , 'message' => 'Something went wrong! contact the administrator'] , 404);

    }
    // -------------------------------------------------------------------------------
}
