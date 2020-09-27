<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAdminAuthenticated
{
    public function handle($request, Closure $next)
    {
        //If request comes from logged in user, he will
        //be redirect to home page.
//        if (Auth::guard()->check()) {
//            return redirect(route('admin.login'));
//        }

        //If request comes from logged in admin, he will
        //be redirected to admin's dashboard page.
        if (Auth::guard('web_admin')->check()) {
            return redirect(route('admin.dashboard'));
        }
        return $next($request);
    }
}
