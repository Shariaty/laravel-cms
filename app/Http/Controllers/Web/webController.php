<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class webController extends Controller
{
    public function index() {
        if (Auth::guard('web_admin')->check()) {
            return redirect(route('admin.dashboard'));
        } else {
            return redirect(route('admin.login'));
        }
    }
}
