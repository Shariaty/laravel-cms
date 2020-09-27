<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Admin;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
//use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
//    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    /** ------------------------------------------------------------------------------------------------------------- */
    protected function guard()
    {
        return Auth::guard('web_admin');
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function showLoginForm()
    {
        return view('admin.auth.login')->with('title' , trans('admin.LOGIN_PAGE_TITLE'));
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required' ,
            'captcha' => 'required|valid_captcha',
        ]);
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        if ( ! $this->guard()->attempt(['email' => $request->email, 'password' => $request->password , 'status' => 1])) {
            return redirect()->back()
                ->withInput($request->only('email' , 'remember'))->withErrors([
                    'email' => trans('auth.LoginUserFailed')
                ]);
        }

        $this->updateLastLogin($this->guard()->user());
        return redirect(route('admin.dashboard'))->with('success' , trans('auth.SUCCESS_LOGIN_MESSAGE'));
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    protected function updateLastLogin($user) {
        $admin = Admin::where('id' , $user->id)->first();
        $admin->update(array('last_active' => Carbon::now()));
        return true;
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        return redirect(route('admin.login'));
    }
}
