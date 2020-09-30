<?php

use Modules\SlideShow\Http\Controllers\SlideShowController;

Route::group(['middleware' => ['web' , 'admin_auth', 'admin_locked' , 'can:'.config('permissions.PERMISSION_SLIDESHOW')] , 'prefix' => 'administrator/slide-show', 'namespace' => 'Modules\SlideShow\Http\Controllers'], function()
{
    Route::get('/list/{slide_cat}', ['as' => 'admin.slide.list', 'uses' => 'SlideShowController@index']);
    Route::get('/add/{slide_cat}', ['as' => 'admin.slide.add', 'uses' => 'SlideShowController@add']);
    Route::get('/edit/{slide_cat}/{slide}', ['as' => 'admin.slide.edit', 'uses' => 'SlideShowController@edit']);
    Route::post('/update/{slide}', ['as' => 'admin.slide.update', 'uses' => 'SlideShowController@update']);
    Route::get('/delete/{slide}', ['as' => 'admin.slide.delete', 'uses' => 'SlideShowController@delete']);
    Route::post('/AjaxStatusUpdate', ['as' => 'admin.slide.status', 'uses' => 'SlideShowController@statusUpdate']);
    Route::get('/image/delete/{slide}', ['as' => 'admin.slide.image.delete', 'uses' => 'SlideShowController@SkillImageDelete']);

    Route::post('/AjaxSort', ['as' => 'admin.slide.AjaxSort', 'uses' => 'SlideShowController@AjaxSort']);

    Route::post('/ajaxFileUpload', ['as' => 'admin.slide.ajaxFileUpload', 'uses' => 'SlideShowController@ajaxFileUpload']);
    Route::get('/fileRemove/{slide}/{type}', ['as' => 'admin.slide.removeFile', 'uses' => 'SlideShowController@magazineFileRemove']);
    Route::get('/fileView/{slide}/{type}', ['as' => 'admin.slide.fileView', 'uses' => 'SlideShowController@magazineFileView']);


    Route::get('/categories', [SlideShowController::class, 'categoryList'])->name('admin.slide.categories');
    Route::get('/categories/add', [SlideShowController::class, 'categoryAdd'])->name('admin.slide.categories.add');
    Route::post('/categories/create', [SlideShowController::class, 'categoryCreate'])->name('admin.slide.categories.create');
    Route::get('/categories/edit/{slide_cat}', [SlideShowController::class, 'categoryEdit'])->name('admin.slide.categories.edit');
    Route::post('/categories/update/{slide_cat}', [SlideShowController::class, 'categoryUpdate'])->name('admin.slide.categories.update');
    Route::post('/categories/AjaxStatusUpdate', [SlideShowController::class, 'categoryStatusUpdate'])->name('admin.slide.categories.status');
    Route::get('/categories/delete/{slide_cat}', [SlideShowController::class, 'categoryDelete'])->name('admin.slide.categories.delete');
});

//API Routes
Route::group(['middleware' => ['api'] , 'prefix' => 'api/slide-show', 'namespace' => 'Modules\SlideShow\Http\Controllers'], function()
{
    Route::get( 'slides/{slide_cat?}' , 'SlideShowApiController@slides' );
});
//API Routes