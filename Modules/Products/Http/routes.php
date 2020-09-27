<?php

Route::group(['middleware' => ['web' , 'admin_auth', 'admin_locked' , 'can:'.config('permissions.PERMISSION_PRODUCTS')], 'prefix' => 'administrator/products', 'namespace' => 'Modules\Products\Http\Controllers'], function()
{
    Route::get('/list', ['as' => 'admin.products.list', 'uses' => 'ProductsController@index']);
    Route::post('/dataTables', ['as' => 'datatables.data', 'uses' => 'ProductsController@anyData']);
    Route::get('/add', ['as' => 'admin.products.add', 'uses' => 'ProductsController@add']);
    Route::post('/save', ['as' => 'admin.products.save', 'uses' => 'ProductsController@save']);
    Route::get('/edit/{product}', ['as' => 'admin.products.edit', 'uses' => 'ProductsController@edit']);
    Route::post('/update/{product}', ['as' => 'admin.products.update', 'uses' => 'ProductsController@update']);
    Route::post('/delete/{product}', ['as' => 'admin.products.delete', 'uses' => 'ProductsController@delete']);
    Route::post('/AjaxStatusUpdate', ['as' => 'admin.products.AjaxStatusUpdate', 'uses' => 'ProductsController@statusUpdate']);

    Route::post('/AjaxGetOptions', ['as' => 'admin.products.AjaxGetOptions', 'uses' => 'ProductsController@AjaxGetOptions']);

    Route::get('/list/subProducts/{product}', ['as' => 'admin.subProducts.list', 'uses' => 'subProductsController@subProductsList']);
    Route::get('/subProduct/delete/{id}', ['as' => 'admin.subProduct.delete', 'uses' => 'subProductsController@delete']);
    Route::get('/subProduct/add/{product}', ['as' => 'admin.subProduct.add', 'uses' => 'subProductsController@add']);
    Route::get('/subProduct/edit/{product}', ['as' => 'admin.subProduct.edit', 'uses' => 'subProductsController@edit']);
    Route::post('/subProduct/update/{product}', ['as' => 'admin.subProduct.update', 'uses' => 'subProductsController@update']);

    Route::post('/ajaxGetBomUnit', 'subProductsController@ajaxGetBomUnit');


    Route::post('/dropZoneUpload', ['as' => 'admin.products.dropZoneUpload', 'uses' => 'ProductsController@dropZoneUpload']);
    Route::post('/dropZone/image/remove', ['as' => 'admin.products.dropZone.image.delete', 'uses' => 'ProductsController@dropZoneImageRemove']);

    Route::post('/ajaxFileUpload', ['as' => 'admin.products.ajaxFileUpload', 'uses' => 'ProductsController@ajaxFileUpload']);
    Route::get('/fileRemove/{product}', ['as' => 'admin.products.removeFile', 'uses' => 'ProductsController@magazineFileRemove']);
    Route::get('/fileView/{product}', ['as' => 'admin.products.fileView', 'uses' => 'ProductsController@magazineFileView']);

    Route::post('/ajaxGetValues', ['as' => 'admin.products.ajaxGetValues', 'uses' => 'ProductsController@ajaxGetValues']);

    Route::group(['middleware' => ['can:'.config('permissions.PERMISSION_PRODUCT_CATEGORIES')]], function()
    {
        Route::get('/categories', ['as' => 'admin.products.categories', 'uses' => 'ProductCategoryController@categoryList']);
        Route::get('/categories/add', ['as' => 'admin.products.categories.add', 'uses' => 'ProductCategoryController@categoryAdd']);
        Route::post('/categories/create', ['as' => 'admin.products.categories.create', 'uses' => 'ProductCategoryController@categoryCreate']);
        Route::get('/categories/edit/{productCat}', ['as' => 'admin.products.categories.edit', 'uses' => 'ProductCategoryController@categoryEdit']);
        Route::post('/categories/update/{productCat}', ['as' => 'admin.products.categories.update', 'uses' => 'ProductCategoryController@categoryUpdate']);
        Route::get('/categories/delete/{productCat}', ['as' => 'admin.products.categories.delete', 'uses' => 'ProductCategoryController@categoryDelete']);
        Route::post('/categories/AjaxStatusUpdate', ['as' => 'admin.products.categories.status', 'uses' => 'ProductCategoryController@categoryStatusUpdate']);
    });
});



//API Routes
Route::group(['middleware' => ['api'] , 'prefix' => 'api', 'namespace' => 'Modules\Products\Http\Controllers'], function()
{
    Route::get('products/getCategories', 'ProductsApiController@getCategories');


    Route::get('products', 'ProductsApiController@getAllProduct');
    Route::get('products/{product}', 'ProductsApiController@getSpecificProduct');

    Route::post('productsList/filters', 'ProductsApiController@getFilters');

    Route::post('products/search', 'ProductsApiController@getSearchedProduct');


    Route::post('singleProduct/getCombination', 'ProductsApiController@getCombination');
    Route::post('singleProduct/calculatePrice', 'ProductsApiController@calculatePrice');

    Route::get('getFinderData', 'ProductsApiController@getFinderData');

    Route::post('products/wizardSearch', 'ProductsApiController@wizardSearch');
});
//API Routes