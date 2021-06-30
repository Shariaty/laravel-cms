<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class LockCheck
{
    public function handle($request, Closure $next)
    {
        Log::info("Admin Lock Middleware - auth guard check status:".Session::get('locked'));

        if (Session::get('locked') == true){
            $request->session()->flash('error' , 'Your Account is locked ,Try to open it through lock screen page to continue');
            return redirect(route('admin.lock'));
        }
        return $next($request);
    }
}
