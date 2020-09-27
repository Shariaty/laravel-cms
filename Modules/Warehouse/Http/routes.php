<?php

Route::group(['middleware' => ['web' , 'admin_auth', 'admin_locked' , 'can:'.config('permissions.PERMISSION_WAREHOUSE')], 'prefix' => 'administrator/warehouse', 'namespace' => 'Modules\Warehouse\Http\Controllers'], function()
{
    Route::get('/list', ['as' => 'admin.warehouse.list', 'uses' => 'WarehouseController@index']);
    Route::post('/dataTables', ['as' => 'datatables.data', 'uses' => 'WarehouseController@anyData']);
    Route::get('/add', ['as' => 'admin.warehouse.add', 'uses' => 'WarehouseController@add']);
    Route::post('/create', ['as' => 'admin.warehouse.create', 'uses' => 'WarehouseController@create']);
    Route::get('/edit/{purchaseInvoice}', ['as' => 'admin.warehouse.edit', 'uses' => 'WarehouseController@edit']);
    Route::post('/update/{purchaseInvoice}', ['as' => 'admin.warehouse.update', 'uses' => 'WarehouseController@update']);
    Route::post('/delete/{purchaseInvoice}', ['as' => 'admin.warehouse.delete', 'uses' => 'WarehouseController@delete']);


    Route::get('/outgo/list', ['as' => 'admin.warehouse.outgo.list', 'uses' => 'WarehouseOutGoController@index']);
    Route::post('/outgo/dataTables', ['as' => 'outgo.datatables.data', 'uses' => 'WarehouseOutGoController@anyData']);
    Route::get('/outgo/detail/{wareHouse}', ['as' => 'admin.warehouse.outgo.detail', 'uses' => 'WarehouseOutGoController@view']);


    Route::get('/inventory/list', ['as' => 'admin.warehouse.inventory.list', 'uses' => 'WarehouseController@inventoryIndex']);
    Route::post('/inventory/dataTables', ['as' => 'inventory.datatables.data', 'uses' => 'WarehouseController@inventoryData']);


    Route::get('/inventory/pdf', ['as' => 'admin.inventory.pdf', 'uses' => 'WarehouseController@pdf']);



});