<?php

use App\Http\Controllers\Admin\Auth\LockController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\admin\auth\ProfileController;
use App\Http\Controllers\Admin\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PagesController;
use App\Http\Controllers\Admin\PublicUsersController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\CountryCities\CountryCitiesController;
use App\Http\Controllers\Password\ForgotPasswordController;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::group( ['prefix' => 'administrator'] ,function() {

    Route::get('403', function () { return view('errors.403')->with('title' , '403'); })->name('403');

    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.reset');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}',[ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ForgotPasswordController::class, 'reset'])->name('password.reset.post');

    Route::get('/lock',[LockController::class, 'lockScreenGet'])->name('admin.lock');
    Route::post('/lock',[LockController::class, 'lockScreenPost'])->name('admin.lock');
    Route::get('/CancelLockScreen',[LockController::class, 'lockScreenCancel'])->name('admin.lock.cancel');

    Route::group( ['middleware' => 'admin_guest'] ,function() {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [LoginController::class, 'login'])->name('admin.login');
    });

    Route::group( ['middleware' => ['admin_auth','admin_locked']] ,function() {

        Route::get('/', [DashboardController::class, 'index'])->name('admin');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/logout', [LoginController::class, 'logout'])->name('admin.logout');
        Route::get('/settings', [SettingsController::class, 'settingsView'])->name('admin.site.settings')->middleware('can:'.config('permissions.PERMISSION_SETTINGS'));
        Route::post('/settings', [SettingsController::class, 'settingsUpdate'])->name('admin.site.settings')->middleware('can:'.config('permissions.PERMISSION_SETTINGS'));

        Route::get('/mediaManager', [DashboardController::class, 'mediaManager'])->name('admin.mediaManager')->middleware('can:'.config('permissions.PERMISSION_FILE_MANAGER'));
        Route::post('/AjaxGetCities', [CountryCitiesController::class, 'AjaxGetCities'])->name('admin.AjaxGetCities');


        // Users Profile Section --------------------------------------------------------------------------------
        Route::get('/profile', [ProfileController::class, 'profile'])->name('admin.profile');
        Route::get('/profile/edit', [ProfileController::class, 'profileEdit'])->name('admin.profile.edit');
        Route::post('/profile/update', [ProfileController::class, 'profileUpdate'])->name('admin.profile.update');
        Route::post('/profile/ajaxImageUpload', [ProfileController::class, 'ajaxUpload'])->name('admin.ajax.imageUpload');
        Route::get('/profile/ImageRemove', [ProfileController::class, 'profilePictureRemove'])->name('admin.profile.image.remove');
        // Users Profile Section --------------------------------------------------------------------------------

        // Admin Users Section ----------------------------------------------------------------------------------
        Route::group( ['middleware' => 'can:'.config('permissions.PERMISSION_ADMIN_USERS')] ,function() {
            Route::resource('roles' , RoleController::class , ['except' => ['destroy'] ]);
            Route::get('/roles/delete/{role}', [ProfileController::class, 'destroy'])->name('roles.delete');

            Route::get('/users', [RegisterController::class, 'usersList'])->name('admin.users');
            Route::get('/users/create', [RegisterController::class, 'createAdmin'])->name('admin.user.create');
            Route::post('/users/create', [RegisterController::class, 'postCreate'])->name('admin.user.create');
            Route::get('/users/update/{admin}', [RegisterController::class, 'updateAdmin'])->name('admin.user.update');
            Route::post('/users/update/{admin}', [RegisterController::class, 'postUpdate'])->name('admin.user.update');
            Route::get('/user/remove/{admin}', [RegisterController::class, 'userDelete'])->name('admin.user.remove');
            Route::post('/users/AjaxStatusUpdate', [RegisterController::class, 'statusUpdate'])->name('admin.status.update');
        });
        // Admin Users Section ----------------------------------------------------------------------------------

        // Public Users Section ----------------------------------------------------------------------------------
        Route::group( ['middleware' => 'can:'.config('permissions.PERMISSION_PUBLIC_USERS')] ,function() {
            Route::get('/publicUsers', [PublicUsersController::class, 'index'])->name('admin.publicUsers');
            Route::post('/publicUsers/dataTables', [PublicUsersController::class, 'anyData'])->name('admin.publicUsers.datatables.data');
            Route::post('/publicUsers/AjaxStatusUpdate', [PublicUsersController::class, 'statusUpdate'])->name('admin.publicUsers.status.update');

            Route::get('/publicUsers/create', [PublicUsersController::class, 'create'])->name('admin.publicUsers.create');
            Route::post('/publicUsers/save', [PublicUsersController::class, 'save'])->name('admin.publicUsers.save');
        });
        // Public Users Section ----------------------------------------------------------------------------------

        // Pages Section    -------------------------------------------------------------------------------------
        Route::group( ['middleware' => 'can:'.config('permissions.PERMISSION_STATIC_PAGES')] ,function() {
            Route::get('/pages/list', [PagesController::class, 'index'])->name('admin.pages.list');
            Route::get('/pages/add', [PagesController::class, 'add'])->name('admin.pages.add');
            Route::post('/pages/create', [PagesController::class, 'create'])->name('admin.pages.create');
            Route::get('/pages/edit/{page}', [PagesController::class, 'edit'])->name('admin.pages.edit');
            Route::post('/pages/update/{page}', [PagesController::class, 'update'])->name('admin.pages.update');
            Route::get('/pages/delete/{page}', [PagesController::class, 'delete'])->name('admin.pages.delete');
        });
        // Pages Section    -------------------------------------------------------------------------------------

    });
});
