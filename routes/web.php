<?php

use App\Http\Controllers\Web\webController;
use Illuminate\Support\Facades\Route;

//Administrator Routs
include_once 'administrator.php';
//Administrator Routs


Route::get('/', [ webController::class, 'index'])->name('home');