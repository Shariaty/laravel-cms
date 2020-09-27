<?php

Route::group(['middleware' => ['web' , 'admin_auth', 'admin_locked' , 'can:'.config('permissions.PERMISSION_EVENTS')] , 'prefix' => 'administrator/events', 'namespace' => 'Modules\Events\Http\Controllers'], function()
{
    Route::get('/list', ['as' => 'admin.events.list', 'uses' => 'EventsController@index']);
    Route::post('/dataTables', ['as' => 'admin.events.datatables.data', 'uses' => 'EventsController@anyData']);
    Route::get('/add', ['as' => 'admin.events.add', 'uses' => 'EventsController@add']);
    Route::post('/create', ['as' => 'admin.events.create', 'uses' => 'EventsController@create']);
    Route::get('/edit/{event}', ['as' => 'admin.events.edit', 'uses' => 'EventsController@edit']);
    Route::post('/update/{event}', ['as' => 'admin.events.update', 'uses' => 'EventsController@update']);
    Route::post('/delete/{event}', ['as' => 'admin.events.delete', 'uses' => 'EventsController@delete']);
    Route::post('/AjaxStatusUpdate', ['as' => 'admin.events.status', 'uses' => 'EventsController@statusUpdate']);
    Route::get('/image/delete/{event}', ['as' => 'admin.events.image.delete', 'uses' => 'EventsController@eventImageDelete']);
});

//API Routes
Route::group(['middleware' => ['api'] , 'prefix' => 'api/events', 'namespace' => 'Modules\Events\Http\Controllers'], function()
{
    Route::get('getSpecificEvent/{event_slug}' , 'EventsApiController@getSingleEvent');
});
//API Routes

