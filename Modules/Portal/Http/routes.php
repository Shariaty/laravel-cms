<?php
Route::group(['middleware' => ['web' , 'admin_auth', 'admin_locked' , 'can:'.config('permissions.PERMISSION_PORTAL_UPDATER')] , 'prefix' => 'administrator/portal', 'namespace' => 'Modules\Portal\Http\Controllers'], function()
{
    Route::get('/list', ['as' => 'admin.portalUpdater.list', 'uses' => 'PortalController@index']);
    Route::post('/dataTables', ['as' => 'admin.portalUpdater.datatables.data', 'uses' => 'PortalController@anyData']);
    Route::post('/delete/{portal}', ['as' => 'admin.portalUpdater.delete', 'uses' => 'PortalController@delete']);
    Route::post('/AjaxStatusUpdate', ['as' => 'admin.portalUpdater.status', 'uses' => 'PortalController@statusUpdate']);
    Route::post('/ajaxFileUpload', ['as' => 'admin.portalUpdater.ajaxFileUpload', 'uses' => 'PortalController@ajaxFileUpload']);
    Route::get('/clearAll', ['as' => 'admin.portalUpdater.clearAll', 'uses' => 'PortalController@clearAll']);

    Route::post('/add', ['as' => 'admin.portalUpdater.add', 'uses' => 'PortalController@add']);




    Route::get('/portalList', ['as' => 'admin.portal.list', 'uses' => 'PortalController@portalIndex']);
    Route::post('/portalDataTables', ['as' => 'admin.portal.datatables.data', 'uses' => 'PortalController@portalAnyData']);
    Route::post('/portalDelete/{portalTask}', ['as' => 'admin.portal.delete', 'uses' => 'PortalController@portalDelete']);
    Route::post('/portalAjaxFileUpload', ['as' => 'admin.portal.ajaxFileUpload', 'uses' => 'PortalController@portalAjaxFileUpload']);


    Route::get('/taskPlayer/{portalTask?}', ['as' => 'admin.portal.taskPlayer', 'uses' => 'PortalController@taskPlayer']);


    Route::get('/portalRecordsList/{portalId?}', ['as' => 'admin.portal.records.list', 'uses' => 'PortalController@portalRecordsIndex']);
    Route::post('/portalRecordsListDataTable', ['as' => 'admin.portal.records.datatables.data', 'uses' => 'PortalController@portalRecordsAnyData']);

});
