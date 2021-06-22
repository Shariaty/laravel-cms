<?php


use Modules\Contacts\Http\Controllers\ContactsController;

Route::group(['middleware' => ['web' , 'admin_auth', 'admin_locked' , 'can:'.config('permissions.PERMISSION_CONTACTS')] , 'prefix' => 'administrator/contacts',], function()
{
    Route::get('/list', [ContactsController::class, 'index'])->name('admin.contacts.list');
    Route::post('/dataTables', [ContactsController::class, 'anyData'])->name('admin.contacts.datatables.data');
    Route::post('/view/{contact}', [ContactsController::class, 'view'])->name('admin.contacts.view');
    Route::post('/delete/{contact}', [ContactsController::class, 'delete'])->name('admin.contacts.delete');
    Route::post('/AjaxStatusUpdate', [ContactsController::class, 'statusUpdate'])->name('admin.contacts.status');
    Route::get('/clearAll', [ContactsController::class, 'clearAll'])->name('admin.contacts.clearAll');
});

