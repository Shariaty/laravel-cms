<?php

Route::group(['middleware' => ['web' , 'admin_auth', 'admin_locked' , 'can:'.config('permissions.PERMISSION_SLIDESHOW')] , 'prefix' => 'administrator/slide-show', 'namespace' => 'Modules\SlideShow\Http\Controllers'], function()
{
    Route::get('/list', ['as' => 'admin.slide.list', 'uses' => 'SlideShowController@index']);
    Route::get('/add', ['as' => 'admin.slide.add', 'uses' => 'SlideShowController@add']);
    Route::get('/edit/{slide}', ['as' => 'admin.slide.edit', 'uses' => 'SlideShowController@edit']);
    Route::post('/update/{slide}', ['as' => 'admin.slide.update', 'uses' => 'SlideShowController@update']);
    Route::get('/delete/{slide}', ['as' => 'admin.slide.delete', 'uses' => 'SlideShowController@delete']);
    Route::post('/AjaxStatusUpdate', ['as' => 'admin.slide.status', 'uses' => 'SlideShowController@statusUpdate']);
    Route::get('/image/delete/{slide}', ['as' => 'admin.slide.image.delete', 'uses' => 'SlideShowController@SkillImageDelete']);

    Route::post('/AjaxSort', ['as' => 'admin.slide.AjaxSort', 'uses' => 'SlideShowController@AjaxSort']);

    Route::post('/ajaxFileUpload', ['as' => 'admin.slide.ajaxFileUpload', 'uses' => 'SlideShowController@ajaxFileUpload']);
    Route::get('/fileRemove/{slide}/{type}', ['as' => 'admin.slide.removeFile', 'uses' => 'SlideShowController@magazineFileRemove']);
    Route::get('/fileView/{slide}/{type}', ['as' => 'admin.slide.fileView', 'uses' => 'SlideShowController@magazineFileView']);
});

//API Routes
Route::group(['middleware' => ['api'] , 'prefix' => 'api/slide-show', 'namespace' => 'Modules\SlideShow\Http\Controllers'], function()
{
    Route::get( 'slides' , 'SlideShowApiController@slides' );
});
//API Routes