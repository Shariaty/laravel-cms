<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\api\pagesController;
use App\Http\Controllers\api\paymentController;
use App\Http\Controllers\api\settingsController;
use App\Http\Controllers\api\visitors\visitorController;
use App\Http\Controllers\CountryCities\CountryCitiesController;

Route::get('settings', [settingsController::class, 'getAllSettings']);

Route::post('auth', [LoginController::class, 'numberReceive']);
Route::post('auth/login', [LoginController::class, 'login']);
Route::post('auth/activateAccount', [LoginController::class, 'activateAccount']);
Route::post('auth/checkUser', [LoginController::class, 'checkUser']);
Route::get('auth/users/me', [LoginController::class, 'getUser']);

Route::post('auth/resetPasswordRequest', [RegisterController::class, 'resetPassword']);
Route::post('auth/resetPassword', [RegisterController::class, 'resetPasswordAction']);

Route::post('auth/register', [RegisterController::class, 'register']);
Route::post('auth/accountActivate', [RegisterController::class, 'activate']);
Route::post('auth/sendCodeAgain', [RegisterController::class, 'sendCodeAgain']);
Route::post('auth/updateUserInfo', [RegisterController::class, 'updateUserInfo']);
Route::post('auth/addAddress', [RegisterController::class, 'addAddress']);
Route::post('auth/removeAddress', [RegisterController::class, 'removeAddress']);

Route::post('profile/ajaxImageUpload', [RegisterController::class, 'ajaxImageUpload']);
Route::post('profile/ajaxImageRemove', [RegisterController::class, 'ajaxImageRemove']);


Route::get('/pay', [paymentController::class, 'pay']);
Route::any('/callBackFromBank',[paymentController::class, 'callBack']);

Route::get('/getSpecificPage/{Page}', [pagesController::class, 'getSpecificPage']);

Route::get('/logVisit', [visitorController::class, 'saveVisitLog']);

Route::get('/getStates', [CountryCitiesController::class, 'getStates']);
Route::get('/getCities', [CountryCitiesController::class, 'getCities']);
Route::post('/getDistricts', [CountryCitiesController::class, 'getDistricts']);

