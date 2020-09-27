<?php
Route::group(['middleware' => ['web' , 'admin_auth', 'admin_locked' , 'can:'.config('permissions.PERMISSION_VALIDITY')] , 'prefix' => 'administrator/validity', 'namespace' => 'Modules\Validity\Http\Controllers'], function()
{
    Route::get('/list', ['as' => 'admin.validity.list', 'uses' => 'ValidityController@index']);
    Route::post('/dataTables', ['as' => 'admin.validity.datatables.data', 'uses' => 'ValidityController@anyData']);
    Route::post('/delete/{validity}', ['as' => 'admin.validity.delete', 'uses' => 'ValidityController@delete']);
    Route::post('/AjaxStatusUpdate', ['as' => 'admin.validity.status', 'uses' => 'ValidityController@statusUpdate']);
    Route::post('/ajaxFileUpload', ['as' => 'admin.validity.ajaxFileUpload', 'uses' => 'ValidityController@ajaxFileUpload']);
    Route::get('/clearAll', ['as' => 'admin.validity.clearAll', 'uses' => 'ValidityController@clearAll']);
});


//API Routes
Route::group(['middleware' => ['api'] , 'prefix' => 'api/validity', 'namespace' => 'Modules\Validity\Http\Controllers'], function()
{
    Route::post( 'confirm' , 'ValidityApiController@confirm' );
});
//API Routes