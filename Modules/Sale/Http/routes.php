<?php

Route::group(['middleware' => ['web' , 'admin_auth', 'admin_locked' , 'can:'.config('permissions.PERMISSION_SALE')], 'prefix' => 'administrator/sale', 'namespace' => 'Modules\Sale\Http\Controllers'], function()
{
    Route::get('/list', ['as' => 'admin.sale.list', 'uses' => 'SaleController@index']);
    Route::post('/dataTables', ['as' => 'datatables.data', 'uses' => 'SaleController@anyData']);
    Route::get('/add', ['as' => 'admin.sale.add', 'uses' => 'SaleController@add']);
    Route::post('/create', ['as' => 'admin.sale.create', 'uses' => 'SaleController@create']);

    Route::get('/edit/{saleInvoice}', ['as' => 'admin.sale.edit', 'uses' => 'SaleController@edit']);
    Route::post('/update/{saleInvoice}', ['as' => 'admin.sale.update', 'uses' => 'SaleController@update']);
    Route::post('/delete/{saleInvoice}', ['as' => 'admin.sale.delete', 'uses' => 'SaleController@delete']);

    Route::post('/ajaxGetPriceAndQuantity', ['as' => 'admin.sale.ajaxGetPriceAndQuantity', 'uses' => 'SaleController@ajaxGetPriceAndQuantity']);
});

Route::group(['middleware' => ['web' , 'admin_auth', 'admin_locked' , 'can:'.config('permissions.PERMISSION_SALE')], 'prefix' => 'administrator/orders', 'namespace' => 'Modules\Sale\Http\Controllers'], function()
{
    Route::get('/list', ['as' => 'admin.order.list', 'uses' => 'OrderController@index']);
    Route::post('/dataTables', ['as' => 'datatables.data', 'uses' => 'OrderController@anyData']);
    Route::get('/detail/{saleInvoice}', ['as' => 'admin.order.detail', 'uses' => 'OrderController@view']);

    Route::get('/updateStatus/{saleInvoice}', ['as' => 'admin.order.updateStatus', 'uses' => 'OrderController@updateStatus']);
    Route::get('/cancelOrder/{saleInvoice}', ['as' => 'admin.order.cancelOrder', 'uses' => 'OrderController@cancelOrder']);
});

Route::group(['middleware' => ['web' , 'admin_auth', 'admin_locked' , 'can:'.config('permissions.PERMISSION_SALE')], 'prefix' => 'administrator/payments', 'namespace' => 'Modules\Sale\Http\Controllers'], function()
{
    Route::get('/list', ['as' => 'admin.payments.list', 'uses' => 'PaymentsController@index']);
    Route::post('/dataTables', ['as' => 'payments.datatables.data', 'uses' => 'PaymentsController@anyData']);

});

Route::group(['middleware' => ['web' , 'admin_auth', 'admin_locked' , 'can:'.config('permissions.PERMISSION_SALE')], 'prefix' => 'administrator/priceList', 'namespace' => 'Modules\Sale\Http\Controllers'], function()
{
    Route::get('/list', ['as' => 'admin.priceList.list', 'uses' => 'PriceListController@index']);
    Route::post('/dataTables', ['as' => 'priceList.datatables.data', 'uses' => 'PriceListController@anyData']);
    Route::get('/add', ['as' => 'admin.priceList.add', 'uses' => 'PriceListController@add']);
    Route::post('/create', ['as' => 'admin.priceList.create', 'uses' => 'PriceListController@create']);

    Route::get('/edit/{priceList}', ['as' => 'admin.priceList.edit', 'uses' => 'PriceListController@edit']);
    Route::post('/update/{priceList}', ['as' => 'admin.priceList.update', 'uses' => 'PriceListController@update']);
    Route::post('/delete/{priceList}', ['as' => 'admin.priceList.delete', 'uses' => 'PriceListController@delete']);


    Route::get('/pdf/{priceList}', ['as' => 'admin.priceList.pdf', 'uses' => 'PriceListController@pdf']);



});

//API Routes
Route::group(['middleware' => [ 'api' , 'auth:api' ] , 'prefix' => 'api/sale', 'namespace' => 'Modules\Sale\Http\Controllers'], function()
{
    Route::post('/create', ['as' => 'api.sale.create', 'uses' => 'SaleApiController@create']);
    Route::post('/getOrderList', ['as' => 'api.sale.getOrderList', 'uses' => 'SaleApiController@getListOfOrders']);
});
//API Routes
