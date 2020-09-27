<?php

Route::group(['middleware' => ['web' , 'admin_auth', 'admin_locked' , 'can:'.config('permissions.PERMISSION_BLOG')] , 'prefix' => 'administrator/articles', 'namespace' => 'Modules\Articles\Http\Controllers'], function()
{
    Route::get('/list', ['as' => 'admin.articles.list', 'uses' => 'ArticlesController@index']);
    Route::post('/dataTables', ['as' => 'admin.articles.datatables.data', 'uses' => 'ArticlesController@anyData']);
    Route::get('/add', ['as' => 'admin.articles.add', 'uses' => 'ArticlesController@add']);
    Route::post('/create', ['as' => 'admin.articles.create', 'uses' => 'ArticlesController@create']);
    Route::get('/edit/{blog}', ['as' => 'admin.articles.edit', 'uses' => 'ArticlesController@edit']);
    Route::post('/update/{blog}', ['as' => 'admin.articles.update', 'uses' => 'ArticlesController@update']);
    Route::post('/delete/{blog}', ['as' => 'admin.articles.delete', 'uses' => 'ArticlesController@delete']);
    Route::post('/AjaxStatusUpdate', ['as' => 'admin.articles.status', 'uses' => 'ArticlesController@statusUpdate']);
    Route::get('/image/delete/{blog}', ['as' => 'admin.articles.image.delete', 'uses' => 'ArticlesController@newsImageDelete']);


    Route::get('/categories', ['as' => 'admin.articles.categories', 'uses' => 'ArticlesController@categoryList']);
    Route::get('/categories/add', ['as' => 'admin.articles.categories.add', 'uses' => 'ArticlesController@categoryAdd']);
    Route::post('/categories/create', ['as' => 'admin.articles.categories.create', 'uses' => 'ArticlesController@categoryCreate']);
    Route::get('/categories/edit/{articleCat}', ['as' => 'admin.articles.categories.edit', 'uses' => 'ArticlesController@categoryEdit']);
    Route::post('/categories/update/{articleCat}', ['as' => 'admin.articles.categories.update', 'uses' => 'ArticlesController@categoryUpdate']);
    Route::get('/categories/delete/{articleCat}', ['as' => 'admin.articles.categories.delete', 'uses' => 'ArticlesController@categoryDelete']);

    Route::post('/categories/AjaxStatusUpdate', ['as' => 'admin.articles.categories.status', 'uses' => 'ArticlesController@categoryStatusUpdate']);

});


//API Routes
Route::group(['middleware' => ['api'] , 'prefix' => 'api/posts', 'namespace' => 'Modules\Articles\Http\Controllers'], function()
{
    Route::get( ''     , 'ArticlesApiController@getAll');
    Route::get('item/{blog}' , 'ArticlesApiController@getSingle');

    Route::get( 'categories'     , 'ArticlesApiController@getAllcategories');
});
//API Routes