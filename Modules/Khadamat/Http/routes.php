<?php

Route::group(['middleware' => ['web' , 'admin_auth', 'admin_locked' , 'can:'.config('permissions.PERMISSION_SERVICES')], 'prefix' => 'administrator/services', 'namespace' => 'Modules\Khadamat\Http\Controllers'], function()
{
    Route::get('/list', ['as' => 'admin.services.list', 'uses' => 'ServiceController@index']);
    Route::post('/dataTables', ['as' => 'datatables.data', 'uses' => 'ServiceController@anyData']);
    Route::get('/add', ['as' => 'admin.services.add', 'uses' => 'ServiceController@add']);
    Route::post('/save', ['as' => 'admin.services.save', 'uses' => 'ServiceController@save']);
    Route::get('/edit/{service}', ['as' => 'admin.services.edit', 'uses' => 'ServiceController@edit']);
    Route::post('/update/{service}', ['as' => 'admin.services.update', 'uses' => 'ServiceController@update']);
    Route::get('/delete/{service}', ['as' => 'admin.services.delete', 'uses' => 'ServiceController@delete']);
    Route::post('/AjaxStatusUpdate', ['as' => 'admin.services.AjaxStatusUpdate', 'uses' => 'ServiceController@statusUpdate']);
    Route::get('/imageRemove/{service}', ['as' => 'admin.services.imageRemove', 'uses' => 'ServiceController@removePicture']);

    Route::post('/dropZoneUpload', ['as' => 'admin.services.dropZoneUpload', 'uses' => 'ServiceController@dropZoneUpload']);
    Route::post('/dropZone/image/remove', ['as' => 'admin.services.dropZone.image.delete', 'uses' => 'ServiceController@dropZoneImageRemove']);

    Route::post('/ajaxFileUpload', ['as' => 'admin.services.ajaxFileUpload', 'uses' => 'ServiceController@ajaxFileUpload']);
    Route::get('/fileRemove/{id}/{type}', ['as' => 'admin.services.removeFile', 'uses' => 'ServiceController@magazineFileRemove']);
    Route::get('/fileView/{id}/{type}', ['as' => 'admin.services.fileView', 'uses' => 'ServiceController@magazineFileView']);
});


//API Routes
Route::group(['middleware' => ['api'] , 'prefix' => 'api', 'namespace' => 'Modules\Services\Http\Controllers'], function()
{
    Route::get('products/getCategories/{portfolioCat?}', 'ProductsApiController@getCategories');


    Route::get('products', 'ProductsApiController@getAllProduct');
    Route::get('portfolio/{product}', 'ProductsApiController@getSpecificProduct');

    Route::post('productsList/filters', 'ProductsApiController@getFilters');

    Route::post('portfolio/search', 'ProductsApiController@getSearchedProduct');


    Route::post('singleProduct/getCombination', 'ProductsApiController@getCombination');
    Route::post('singleProduct/calculatePrice', 'ProductsApiController@calculatePrice');

    Route::get('getFinderData', 'ProductsApiController@getFinderData');

});
//API Routes