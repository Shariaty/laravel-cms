<?php

//API Routes
Route::group(['middleware' => ['api'] , 'prefix' => 'api/ratings', 'namespace' => 'Modules\Ratings\Http\Controllers'], function()
{
    Route::get('rate' , 'RatingsApiController@rate');
    Route::get('calculateRate/{id}' , 'RatingsApiController@calculateRate');
});
//API Routes