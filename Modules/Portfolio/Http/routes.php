<?php

Route::group(['middleware' => ['web' , 'admin_auth', 'admin_locked' , 'can:'.config('permissions.PERMISSION_PORTFOLIO')], 'prefix' => 'administrator/portfolio', 'namespace' => 'Modules\Portfolio\Http\Controllers'], function()
{
    Route::get('/list', ['as' => 'admin.portfolio.list', 'uses' => 'PortfolioController@index']);
    Route::post('/dataTables', ['as' => 'datatables.data', 'uses' => 'PortfolioController@anyData']);
    Route::get('/add', ['as' => 'admin.portfolio.add', 'uses' => 'PortfolioController@add']);
    Route::post('/save', ['as' => 'admin.portfolio.save', 'uses' => 'PortfolioController@save']);
    Route::get('/edit/{product}', ['as' => 'admin.portfolio.edit', 'uses' => 'PortfolioController@edit']);
    Route::post('/update/{product}', ['as' => 'admin.portfolio.update', 'uses' => 'PortfolioController@update']);
    Route::post('/delete/{product}', ['as' => 'admin.portfolio.delete', 'uses' => 'PortfolioController@delete']);
    Route::post('/AjaxStatusUpdate', ['as' => 'admin.portfolio.AjaxStatusUpdate', 'uses' => 'PortfolioController@statusUpdate']);


    Route::post('/dropZoneUpload', ['as' => 'admin.portfolio.dropZoneUpload', 'uses' => 'PortfolioController@dropZoneUpload']);
    Route::post('/dropZone/image/remove', ['as' => 'admin.portfolio.dropZone.image.delete', 'uses' => 'PortfolioController@dropZoneImageRemove']);

    Route::post('/ajaxFileUpload', ['as' => 'admin.portfolio.ajaxFileUpload', 'uses' => 'PortfolioController@ajaxFileUpload']);
    Route::get('/fileRemove/{product}/{type}', ['as' => 'admin.portfolio.removeFile', 'uses' => 'PortfolioController@magazineFileRemove']);
    Route::get('/fileView/{product}/{type}', ['as' => 'admin.portfolio.fileView', 'uses' => 'PortfolioController@magazineFileView']);

    Route::post('/ajaxGetValues', ['as' => 'admin.portfolio.ajaxGetValues', 'uses' => 'PortfolioController@ajaxGetValues']);

    Route::group(['middleware' => ['can:'.config('permissions.PERMISSION_PORTFOLIO_CATEGORIES')]], function()
    {
        Route::get('/categories', ['as' => 'admin.portfolio.categories', 'uses' => 'PortfolioCategoryController@categoryList']);
        Route::get('/categories/add', ['as' => 'admin.portfolio.categories.add', 'uses' => 'PortfolioCategoryController@categoryAdd']);
        Route::post('/categories/create', ['as' => 'admin.portfolio.categories.create', 'uses' => 'PortfolioCategoryController@categoryCreate']);
        Route::get('/categories/edit/{portfolioCat}', ['as' => 'admin.portfolio.categories.edit', 'uses' => 'PortfolioCategoryController@categoryEdit']);
        Route::post('/categories/update/{portfolioCat}', ['as' => 'admin.portfolio.categories.update', 'uses' => 'PortfolioCategoryController@categoryUpdate']);
        Route::get('/categories/delete/{portfolioCat}', ['as' => 'admin.portfolio.categories.delete', 'uses' => 'PortfolioCategoryController@categoryDelete']);
        Route::post('/categories/AjaxStatusUpdate', ['as' => 'admin.portfolio.categories.status', 'uses' => 'PortfolioCategoryController@categoryStatusUpdate']);
        Route::get('/categories/removePicture/{portfolioCat}', ['as' => 'admin.portfolio.categories.imageRemove', 'uses' => 'PortfolioCategoryController@removePicture']);
    });

//    Route::group(['middleware' => ['can:'.config('permissions.PERMISSION_PORTFOLIO')]], function()
//    {
//        Route::get('/designers', ['as' => 'admin.portfolio.designers', 'uses' => 'DesignerController@designerList']);
//        Route::get('/designers/add', ['as' => 'admin.portfolio.designers.add', 'uses' => 'DesignerController@designerAdd']);
//        Route::post('/designers/create', ['as' => 'admin.portfolio.designers.create', 'uses' => 'DesignerController@designerCreate']);
//        Route::get('/designers/edit/{designer}', ['as' => 'admin.portfolio.designers.edit', 'uses' => 'DesignerController@designerEdit']);
//        Route::post('/designers/update/{designer}', ['as' => 'admin.portfolio.designers.update', 'uses' => 'DesignerController@designerUpdate']);
//        Route::get('/designers/delete/{designer}', ['as' => 'admin.portfolio.designers.delete', 'uses' => 'DesignerController@designerDelete']);
//        Route::post('/designers/AjaxStatusUpdate', ['as' => 'admin.portfolio.designers.status', 'uses' => 'DesignerController@designerStatusUpdate']);
//
//        Route::post('/designers/ajaxImageRemove', ['as' => 'admin.portfolio.designers.image.ajaxImageRemove', 'uses' => 'DesignerController@ajaxImageRemove']);
//
//    });

});

//API Routes
Route::group(['middleware' => ['api'] , 'prefix' => 'api', 'namespace' => 'Modules\Portfolio\Http\Controllers'], function()
{
    Route::get('products/getCategories/{portfolioCat?}', 'ProductsApiController@getCategories');


    Route::get('products', 'ProductsApiController@getAllProduct');
    Route::get('portfolio/{product}', 'ProductsApiController@getSpecificProduct');

    Route::post('productsList/filters', 'ProductsApiController@getFilters');

    Route::post('portfolio/search', 'ProductsApiController@getSearchedProduct');


    Route::post('singleProduct/getCombination', 'ProductsApiController@getCombination');
    Route::post('singleProduct/calculatePrice', 'ProductsApiController@calculatePrice');

    Route::get('getFinderData', 'ProductsApiController@getFinderData');

});
//API Routes