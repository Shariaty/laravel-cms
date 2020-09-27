<?php

Route::group(['middleware' => ['web' , 'admin_auth', 'admin_locked' , 'can:'.config('permissions.PERMISSION_NEWS')] , 'prefix' => 'administrator/news', 'namespace' => 'Modules\News\Http\Controllers'], function()
{
    Route::get('/list', ['as' => 'admin.news.list', 'uses' => 'NewsController@index']);
    Route::post('/dataTables', ['as' => 'admin.news.datatables.data', 'uses' => 'NewsController@anyData']);
    Route::get('/add', ['as' => 'admin.news.add', 'uses' => 'NewsController@add']);
    Route::post('/create', ['as' => 'admin.news.create', 'uses' => 'NewsController@create']);
    Route::get('/edit/{news}', ['as' => 'admin.news.edit', 'uses' => 'NewsController@edit']);
    Route::post('/update/{news}', ['as' => 'admin.news.update', 'uses' => 'NewsController@update']);
    Route::post('/delete/{news}', ['as' => 'admin.news.delete', 'uses' => 'NewsController@delete']);
    Route::post('/AjaxStatusUpdate', ['as' => 'admin.news.status', 'uses' => 'NewsController@statusUpdate']);
    Route::get('/image/delete/{news}', ['as' => 'admin.news.image.delete', 'uses' => 'NewsController@newsImageDelete']);


    Route::get('/categories', ['as' => 'admin.news.categories', 'uses' => 'NewsController@categoryList']);
    Route::get('/categories/add', ['as' => 'admin.news.categories.add', 'uses' => 'NewsController@categoryAdd']);
    Route::post('/categories/create', ['as' => 'admin.news.categories.create', 'uses' => 'NewsController@categoryCreate']);
    Route::get('/categories/edit/{newsCat}', ['as' => 'admin.news.categories.edit', 'uses' => 'NewsController@categoryEdit']);
    Route::post('/categories/update/{newsCat}', ['as' => 'admin.news.categories.update', 'uses' => 'NewsController@categoryUpdate']);
    Route::get('/categories/delete/{newsCat}', ['as' => 'admin.news.categories.delete', 'uses' => 'NewsController@categoryDelete']);

    Route::post('/categories/AjaxStatusUpdate', ['as' => 'admin.news.categories.status', 'uses' => 'NewsController@categoryStatusUpdate']);
});

//API Routes
Route::group(['middleware' => ['api'] , 'prefix' => 'api/news', 'namespace' => 'Modules\News\Http\Controllers'], function()
{
    Route::get( ''     , 'NewsApiController@getAllNews');
    Route::get('item/{news}' , 'NewsApiController@getSingleNews');

    Route::get( 'categories'     , 'NewsApiController@getAllcategories');
});
//API Routes

