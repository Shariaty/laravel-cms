<?php

Route::group(['middleware' => ['web' , 'admin_auth', 'admin_locked' , 'can:'.config('permissions.PERMISSION_COMMENTS')] , 'prefix' => 'administrator/comments', 'namespace' => 'Modules\Comments\Http\Controllers'], function()
{
    Route::get('/list', ['as' => 'admin.comments.list', 'uses' => 'CommentsController@index']);
    Route::post('/dataTables', ['as' => 'datatables.data', 'uses' => 'CommentsController@anyData']);
    Route::post('/AjaxStatusUpdate', ['as' => 'admin.comments.status', 'uses' => 'CommentsController@statusUpdate']);
    Route::post('/delete/{comment}', ['as' => 'admin.comments.delete', 'uses' => 'CommentsController@delete']);

});