<?php

Route::group(['middleware' => ['web' , 'admin_auth', 'admin_locked' , 'can:'.config('permissions.PERMISSION_MAGAZINES')] , 'prefix' => 'administrator/magazines', 'namespace' => 'Modules\Magazines\Http\Controllers'], function()
{
    Route::get('/list', ['as' => 'admin.magazines.list', 'uses' => 'MagazinesController@index']);
    Route::post('/dataTables', ['as' => 'admin.magazines.datatables.data', 'uses' => 'MagazinesController@anyData']);
    Route::get('/add', ['as' => 'admin.magazines.add', 'uses' => 'MagazinesController@add']);
    Route::post('/create', ['as' => 'admin.magazines.create', 'uses' => 'MagazinesController@create']);
    Route::get('/edit/{magazine}', ['as' => 'admin.magazines.edit', 'uses' => 'MagazinesController@edit']);
    Route::post('/update/{magazine}', ['as' => 'admin.magazines.update', 'uses' => 'MagazinesController@update']);
    Route::post('/delete/{magazine}', ['as' => 'admin.magazines.delete', 'uses' => 'MagazinesController@delete']);
    Route::post('/AjaxStatusUpdate', ['as' => 'admin.magazines.status', 'uses' => 'MagazinesController@statusUpdate']);
    Route::get('/image/delete/{magazine}', ['as' => 'admin.magazines.image.delete', 'uses' => 'MagazinesController@itemImageDelete']);

    Route::post('/ajaxFileUpload', ['as' => 'admin.magazines.ajaxFileUpload', 'uses' => 'MagazinesController@ajaxFileUpload']);
    Route::get('/fileRemove/{magazine}', ['as' => 'admin.magazines.removeFile', 'uses' => 'MagazinesController@magazineFileRemove']);
    Route::get('/fileView/{magazine}', ['as' => 'admin.magazines.fileView', 'uses' => 'MagazinesController@magazineFileView']);

    Route::post('/ajaxImageRemove', ['as' => 'admin.magazines.image.ajaxImageRemove', 'uses' => 'MagazinesController@ajaxImageRemove']);

    Route::group(['middleware' => ['can:'.config('permissions.PERMISSION_MAGAZINE_CATEGORIES')]], function()
    {
        Route::get('/categories', ['as' => 'admin.magazines.categories', 'uses' => 'MagazinesController@categoryList']);
        Route::get('/categories/add', ['as' => 'admin.magazines.categories.add', 'uses' => 'MagazinesController@categoryAdd']);
        Route::post('/categories/create', ['as' => 'admin.magazines.categories.create', 'uses' => 'MagazinesController@categoryCreate']);
        Route::get('/categories/edit/{magazineCat}', ['as' => 'admin.magazines.categories.edit', 'uses' => 'MagazinesController@categoryEdit']);
        Route::post('/categories/update/{magazineCat}', ['as' => 'admin.magazines.categories.update', 'uses' => 'MagazinesController@categoryUpdate']);
        Route::get('/categories/delete/{magazineCat}', ['as' => 'admin.magazines.categories.delete', 'uses' => 'MagazinesController@categoryDelete']);

        Route::post('/categories/AjaxStatusUpdate', ['as' => 'admin.magazines.categories.status', 'uses' => 'MagazinesController@categoryStatusUpdate']);
    });

});


//API Routes
Route::group(['middleware' => ['api'] , 'prefix' => 'api', 'namespace' => 'Modules\Magazines\Http\Controllers'], function()
{
    Route::get('magazines/getCategoryInfo', 'MagazinesApiController@getCategoryInfo');
    Route::get('magazines/download/{magazine}', 'MagazinesApiController@downloadFile');
});
//API Routes