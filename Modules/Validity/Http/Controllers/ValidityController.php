<?php

namespace Modules\Validity\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Validity\Imports\ValidityImport;
use Modules\Validity\Validity;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;


class ValidityController extends Controller
{
    protected  $redirectPath = 'administrator/validity/list';
    // -------------------------------------------------------------------------------
    public function index()
    {
        $count = Validity::count('id');
        return view('validity::list' , compact('count'))->with('title' , 'List of authorized identifications');
    }
    // -------------------------------------------------------------------------------
    public function anyData()
    {
        $dataTable =  Datatables::of(Validity::select(['id', 'title' , 'identification' , 'is_published', 'created_at']))
            ->editColumn('title', function ($validity) {
                return "<div style='text-align: left'>$validity->title</div>";
            })
            ->editColumn('created_at', function ($validity) {
                return $validity->created_at->format('Y/m/d');
            })
            ->editColumn('is_published', function ($validity) {
                return ($validity->is_published == 'N') ?
                    '<button class="btn btn-xs btn-default status-change" data-status="'.$validity->is_published.'" data-new="'.$validity->id.'"><i class="fa fa-ban fa-1x text-danger"></i></button>' :
                    '<button class="btn btn-xs btn-default status-change" data-status="'.$validity->is_published.'" data-new="'.$validity->id.'"><i class="fa fa-check fa-1x text-success"></i></button>' ;
            })
            ->addColumn('action' , function ($validity) {
                return $this->render($validity);
            })
            ->rawColumns(['is_published' , 'action' , 'title'])
            ->make(true);

        return $dataTable;
    }
    // -------------------------------------------------------------------------------
    public function render( $validity ) {
        $final = null;
        $final .= '<a data-id="'.$validity->id.'" class="btn btn-xs red delete_btn"><i class="fa fa-trash"></i></a>';
        return $final;
    }
    // -------------------------------------------------------------------------------
    protected function delete(Validity $validity){
        if($validity->delete()){
            return response()->json(['status' => 'success', 'message' => 'Item successfully removed']);
        }
        return response()->json(['status' => 'error', 'message' => 'There was problem in removing this item!']);
    }
    // -------------------------------------------------------------------------------
    protected function statusUpdate(Request $request){
        if($request->has('user_id') && $request->has('status')){
            $user = Validity::where('id' , $request->input('user_id'))->first();
            $user->is_published = $request->input('status');
            $user->update();
            return response(['status' => 'success' , 'message' => 'successfully updated' , 'newStatus' => $request->input('status')]);
        }
        return response(['status' => 'error' , 'message' => 'Something went wrong! contact the administrator'] , 404);

    }
    // -------------------------------------------------------------------------------
    protected function clearAll(){
        Validity::truncate();
        return redirect($this->redirectPath)->with('success' , 'All Items has been removed');
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function ajaxFileUpload(Request $request)
    {
        if($request->ajax()) {
            if($request->hasFile('file'))  {
                $rules = array('file' => 'required|mimes:xlsx|max:2048');
                $validator = Validator::make($request->all(), $rules);

                if($validator->fails()){
                    $errors = $validator->messages()->all();
                    $result = array('success' => false , 'message' => $errors);
                } else {
                    $file = $request->file('file');
                    Excel::import(new ValidityImport, $file);

                    $newCount = Validity::count('id');
                    $result = array('success' => true , 'message' => $newCount);
                }
                return response()->json($result);
            } else {
                return response()->json(array('error' => true , 'message' => [ 'File is not available or it is damaged , in this case you can use another file' ] ));
            }
        }
        return 'forbidden';
    }
}
