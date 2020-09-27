<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LockController extends Controller
{
    use AuthenticatesUsers {
        logout as performLogout;
    }

    protected function guard()
    {
        return Auth::guard('web_admin');
    }

    public function lockScreenGet()
    {
        if ($this->guard()->check()) {
            Session::put('locked' , true);
            $user = $this->guard()->user();
            return view('admin.auth.lock' , compact('user'));
        }

        return redirect(route('admin.login'));
    }

    public function lockScreenPost(Request $request)
    {
        $this->validate($request, [
            'password' => 'required'
        ]);

        $password = $request->input('password');
        if(Hash::check($password , $this->guard()->user()->password)){

            if($this->guard()->check() && $this->guard()->user()->status == 'N'){
                return redirect(route('admin.lock'))->withErrors([
                    'password' => 'Your account has been disabled',
                ]);
            }

            Session::forget('locked');
            return redirect(route('admin.dashboard'));
        }

        return redirect(route('admin.lock'))->withErrors([
            'password' => 'Password is not correct!',
        ]);
    }

    public function lockScreenCancel(Request $request)
    {
        $this->performLogout($request);
        Session::forget('locked');
        return redirect(route('admin.login'));
    }

}
