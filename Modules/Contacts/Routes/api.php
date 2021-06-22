<?php

use Modules\Contacts\Http\Controllers\ContactsApiController;

Route::get( 'getContactInfo' , [ContactsApiController::class, 'getContactInfo']);
Route::post('contactMessageSend' ,  [ContactsApiController::class, 'contactMessageSend']);
