<?php

namespace Modules\Comments\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Comments\Comment;
use Modules\Stores\Store;
use Yajra\Datatables\Datatables;


class CommentsController extends Controller
{
    public function index()
    {
        return view('comments::list')->with('title' , 'Comments List');
    }
    // -------------------------------------------------------------------------------
    public function anyData()
    {
        return Datatables::of(Comment::all())
            ->editColumn('commentable_type' , function ($item) {
                switch ($item->commentable_type){
                    case 'Modules\Stores\Store': return '<span class="badge badge-info">فروشگاه</span>';
                    default: return '<span class="badge badge-default">نا مشخص</span>';
                }
            })
            ->editColumn('status', function ($item) {
                return ($item->status == COMMENT_TYPE_WAIT_TO_CONFIRM) ?
                    '<button class="btn btn-xs btn-default status-change" data-status="'.$item->status.'" data-new="'.$item->id.'"><i class="fa fa-ban fa-1x text-danger"></i></button>' :
                    '<button class="btn btn-xs btn-default status-change" data-status="'.$item->status.'" data-new="'.$item->id.'"><i class="fa fa-check fa-1x text-success"></i></button>' ;
            })
            ->addColumn('action' , function ($item) {
                return $this->render($item);
            })
            ->make(true);
    }
    // -------------------------------------------------------------------------------
    public function render( $item ) {
        $final = '';
        $final .= '<a data-id="'.$item->id.'" class="btn btn-xs red delete_btn"><i class="fa fa-trash"></i></a>';
        return $final;
    }
    // -------------------------------------------------------------------------------
    protected function statusUpdate(Request $request){
        if($request->has('user_id') && $request->has('status')){
            $comment = Comment::where('id' , $request->input('user_id'))->first();
            $comment->status = $request->input('status');
            $comment->update();
            return response(['status' => 'success' , 'message' => 'successfully updated' , 'newStatus' => $request->input('status')]);
        }
        return response(['status' => 'error' , 'message' => 'Something went wrong! contact the administrator'] , 404);

    }
    // -------------------------------------------------------------------------------
    public function delete(Comment $comment)
    {

        $result = DB::transaction( function () use ($comment) {
            if($comment->delete()) {
                return true;
            }
            return false;
        });
        if ($result)
            return response()->json(['status' => 'success', 'message' => LBL_COMMON_DELETE_SUCCESSFUL]);

        return response()->json(['status' => 'error', 'message' => LBL_COMMON_DELETE_ERROR]);
    }
    // -------------------------------------------------------------------------------

}
