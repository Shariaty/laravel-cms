<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;



class PublicUsersController extends Controller
{
    protected $redirectPath = 'administrator/siteUsers';

    // -------------------------------------------------------------------------------
    public function index()
    {
        $items = User::orderBy('created_at')->paginate(20);
        return view('admin.publicUsers.index' , compact('items'))->with('title' , 'Public users list');
    }
    // -------------------------------------------------------------------------------
    public function anyData()
    {
        return Datatables::of(User::select(['id' , 'fullName' , 'cell' , 'email' , 'status' , 'last_login' , 'created_at']))
            ->editColumn('fullName', function ($user) {
                return $user->fullName ? $user->fullName : '<span style="color: red; font-weight: bold;"> -- </span>';
            })
            ->editColumn('email', function ($user) {
                return $user->email ? $user->email : '<span style="color: red; font-weight: bold;"> -- </span>';
            })
            ->editColumn('created_at', function ($user) {
                return $user->created_at->format('Y/m/d');
            })
            ->editColumn('status', function ($user) {
                return  $this->_generateStatus($user) ;
            })
            ->editColumn('last_login', function ($user) {
                if(!empty($user->last_login)) {
                    return Carbon::parse($user->last_login)->format('Y/m/d H:m');
                } else {
                    return '<span style="color: red; font-weight: bold;">Never</span>';
                }
            })
            ->addColumn('action' , function ($user) {
                return $this->render($user);
            })
            ->rawColumns(['status', 'email' , 'last_login' , 'action' , 'category'])
            ->make(true);
    }
    // -------------------------------------------------------------------------------
    public function render( $user ) {
        $final = null;

        if($user->status == PUS_ACTIVE) {
            $final .= '<a data-id="'.$user->id.'" class="btn btn-xs red delete_btn">Disable User</a>';
        } else if ($user->status == PUS_DISABLED) {
            $final .= '<a data-id="'.$user->id.'" class="btn btn-xs green delete_btn">Enable User</a>';
        } else {
            $final .= '<i class="fa fa-clock-o"></i>';
        }

        return $final;
    }
    // -------------------------------------------------------------------------------
    protected function statusUpdate(Request $request){
        $id = $request->input('identifier');
        if($id){

            $user = User::whereId($id)->first();
            if($user){

                if ($user->status == PUS_DISABLED) {

                    $user->status = PUS_ACTIVE ;
                    $user->update();

                } elseif ($user->status == PUS_ACTIVE ) {

                    $user->status = PUS_DISABLED ;
                    $user->update();
                }
            }

            return response(['status' => 'success' , 'message' => LBL_COMMON_UPDATE_SUCCESSFUL ]);
        }

        return response(['status' => 'error' , 'message' => LBL_COMMON_ERROR] , 404);

    }
    // -------------------------------------------------------------------------------
    public function create()
    {
        return view('admin.publicUsers.create')->with('title' , 'Public users create');
    }
    // -------------------------------------------------------------------------------
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'fullName' => 'required|max:100',
            'cell' => 'required|numeric|unique:users',
            'email-address' => 'sometimes|email|max:100',
            'pass' => 'sometimes|required|min:6',
        ]);
    }
    // -------------------------------------------------------------------------------
    public function save(Request $request)
    {
        $data = $request->all();
        if( !isset($data['pass']) ) {
            $data = array_except($data , 'pass');
        }

        if( !isset($data['email-address']) ) {
            $data = array_except($data , 'email-address');
        }

        $this->validator($data)->validate();

        if(isset($data['pass'])) {
            $data = array_add($data , 'password' , bcrypt($data['pass']));
            $data = array_except($data , 'pass');
        }

        if(isset($data['pass'])) {
            $data = array_add($data , 'email' , $data['email-address']);
            $data = array_except($data , 'email-address');
        }

        $user = User::create($data);
        if ($user) {
            return redirect( route('admin.publicUsers'))->with('success' , 'User created');
        }
        return redirect( route('admin.publicUsers'))->with('error' , LBL_COMMON_ERROR);
    }
    // -------------------------------------------------------------------------------
    protected function _generateStatus($user){
        switch ($user->status) {
            case  PUS_WAIT_FOR_CONFIRM :
                return '<span style="color: darkorange; font-weight: bold;">wait for activation</span>';
            case  PUS_ACTIVE :
                return '<span style="color: darkgreen; font-weight: bold;">active</span>';
            case  PUS_DISABLED :
                return '<span style="color: red; font-weight: bold;">disabled</span>';
            default :
                return '<span style="color: grey; font-weight: bold;">unknown</span>';
        }
    }
}