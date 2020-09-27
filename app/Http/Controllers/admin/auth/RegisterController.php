<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;



class RegisterController extends Controller
{
    protected $redirectPath = 'administrator/users';

    protected function guard()
    {
        return Auth::guard('web_admin');
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function usersList()
    {
        $users = Admin::orderBy('created_at' , 'ASC')->where('id', '!=' , 0)->paginate(20);
        return view('admin.users.usersList' , compact('users'))->with('title' , 'List of the users');
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function createAdmin()
    {
        return view('admin.users.create')->with('title' , 'Create Administrator');
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function postCreate(Request $request)
    {
        $this->validator($request->all())->validate();

        $data = $request->all();

        $user = $this->create($data);
        $user->roles()->sync($request->input('permission_id'));


        $request->session()->flash('success', 'Admin user'. $user->email.'created successfully!');
        return redirect($this->redirectPath);

    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function updateAdmin(Admin $admin)
    {
        $user = $admin;
        if($this->protectSuperAdmin($admin)){
            return redirect(route('admin.users'))->with('warning' , 'Super Admin is not editable');
        }

        if( count($admin->roles)) {
            $selectedCategory = $admin->roles->pluck('id');
        } else {
            $selectedCategory = '';
        }

        return view('admin.users.update' , compact('user' , 'selectedCategory'))->with('title' , 'Edit Administrator: '.$admin->email);
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function postUpdate(Admin $admin , Request $request)
    {
        Validator::make($request->all(), [
            'firstname' => 'required|max:100',
            'lastname' => 'required|max:100',
            'password' => 'min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        if(!empty($request->input('password'))){
            $request->replace(array( 'password' => bcrypt($request->input('password')) ));
            $data = $request->except(['_token' , 'email']);
        } else {
            $data = $request->except(['_token' , 'email' , 'password']);
        }

        if($this->protectSuperAdmin($admin)){
            return redirect(route('admin.users'))->with('warning' , 'Super Admin is not editable');
        }

        $admin->update($data);
        $admin->roles()->sync($request->input('permission_id'));

        $request->session()->flash('success', trans('notify.UPDATE_SUCCESS_NOTIFICATION'));
        return redirect($this->redirectPath);
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'firstname' => 'required|max:100',
            'lastname' => 'required|max:100',
            'email' => 'required|email|max:100|unique:admin_users',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
            'permission_id' => 'required|array'
        ]);
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    protected function create(array $data)
    {
        $user = Admin::create([
            'email'             => $data['email'],
            'firstname'         => $data['firstname'],
            'lastname'          => $data['lastname'],
            'password'          => bcrypt($data['password']),
            'status'            => 1
        ]);
        return $user;
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    protected function userDelete(Admin $admin , Request $request){

        if($this->protectSuperAdmin($admin)){
           return redirect(route('admin.users'))->with('warning' , 'Super Admin is not editable');
        }

        $admin->delete();
        $request->session()->flash('success', 'User Successfully Removed');
        return redirect($this->redirectPath);

    }
    /** ------------------------------------------------------------------------------------------------------------- */
    protected function statusUpdate(Request $request){
        if($request->has('user_id') && $request->has('status')){
            $user = Admin::where('id' , $request->input('user_id'))->first();
            $user->status = $request->input('status');
            $user->update();
            return response(['status' => 'success' , 'message' => 'successfully updated' , 'newStatus' => $request->input('status')]);
        }
        return response(['status' => 'error' , 'message' => 'Something went wrong! contact the administrator'] , 404);

    }
    /** ------------------------------------------------------------------------------------------------------------- */
    protected function protectSuperAdmin($admin){
        if (!empty($admin)){
            if($admin->id == 1 || $admin->id == 0){
                return true ;
            }
        }
        return false;
    }
    /** ------------------------------------------------------------------------------------------------------------- */

}
