<?php

Route::group(['middleware' => ['web' , 'admin_auth', 'admin_locked' , 'can:'.config('permissions.PERMISSION_STORES')], 'prefix' => 'administrator/stores', 'namespace' => 'Modules\Stores\Http\Controllers'], function()
{
    Route::get('/list', ['as' => 'admin.stores.list', 'uses' => 'StoresController@index']);
    Route::post('/dataTables', ['as' => 'datatables.data', 'uses' => 'StoresController@anyData']);

    Route::get('/add', ['as' => 'admin.stores.add', 'uses' => 'StoresController@add']);
    Route::post('/save', ['as' => 'admin.stores.save', 'uses' => 'StoresController@save']);

    Route::get('/edit/{store}', ['as' => 'admin.stores.edit', 'uses' => 'StoresController@edit']);
    Route::post('/update/{store}', ['as' => 'admin.stores.update', 'uses' => 'StoresController@update']);

    Route::post('/delete/{store}', ['as' => 'admin.stores.delete', 'uses' => 'StoresController@delete']);
    Route::post('/AjaxStatusUpdate', ['as' => 'admin.stores.AjaxStatusUpdate', 'uses' => 'StoresController@statusUpdate']);
//    Route::post('/AjaxGetCities', ['as' => 'admin.stores.AjaxGetCities', 'uses' => 'StoresController@getSearchedStores']);

    Route::post('/ajaxZipFileUpload', ['as' => 'admin.stores.ajaxZipFileUpload', 'uses' => 'StoresController@ajaxFileUpload']);
    Route::get('/fileRemove/{store}', ['as' => 'admin.stores.removeFile', 'uses' => 'StoresController@magazineFileRemove']);
    Route::get('/fileView/{store}', ['as' => 'admin.stores.fileView', 'uses' => 'StoresController@magazineFileView']);


    Route::post('/dropZoneUpload', ['as' => 'admin.stores.dropZoneUpload', 'uses' => 'StoresController@dropZoneUpload']);
    Route::post('/dropZone/image/remove', ['as' => 'admin.stores.dropZone.image.delete', 'uses' => 'StoresController@dropZoneImageRemove']);
});



//API Routes
Route::group(['middleware' => ['api'] , 'prefix' => 'api', 'namespace' => 'Modules\Stores\Http\Controllers'], function()
{
    Route::get('stores', 'StoresApiController@getAllStores');
    Route::get('stores/{store}', 'StoresApiController@getSpecificStore');

    Route::post('getSearchedStores', 'StoresApiController@getSearchedStores');

    Route::post('createStoreByUser', 'StoresApiController@createStoreByUser');
    Route::get('getStoresListOfUser', 'StoresApiController@getStoresListOfUser');

    Route::post('updateStore', 'StoresApiController@updateStore');



    Route::post('store/ajaxImageUpload', 'StoresApiController@ajaxImageUpload');
    Route::post('store/getAllImages', 'StoresApiController@getAllImages');
    Route::post('store/ajaxImageRemove', 'StoresApiController@ajaxImageRemove');
});
//API Routes