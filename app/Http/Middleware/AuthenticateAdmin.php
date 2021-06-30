<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthenticateAdmin
{
    public function handle($request, Closure $next)
    {
        //If request does not comes from logged in admin
        //then he shall be redirected to admin Login page
        $status = Auth::guard('web_admin')->check();
        Log::info("Admin Auth Middleware - auth guard check status:".$status);

        if (! $status ) {
            return redirect(route('admin.login'));
        }

        return $next($request);
    }
}
