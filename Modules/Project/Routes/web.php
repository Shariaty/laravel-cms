<?php

// Projects Section   -------------------------------------------------------------------------------------
use Modules\Project\Http\Controllers\ProjectController;

Route::group( ['middleware' => 'can:'.config('permissions.PERMISSION_PROJECTS')  , 'prefix' => 'administrator'] ,function() {
    Route::get('/projects/list', [ProjectController::class, 'index'])->name('admin.projects.list');
    Route::get('/projects/add', [ProjectController::class, 'add'])->name('admin.projects.add');
    Route::post('/projects/create', [ProjectController::class, 'create'])->name('admin.projects.create');
    Route::get('/projects/edit/{project}', [ProjectController::class, 'edit'])->name('admin.projects.edit');
    Route::post('/projects/update/{project}', [ProjectController::class, 'update'])->name('admin.projects.update');
    Route::get('/projects/delete/{project}', [ProjectController::class, 'delete'])->name('admin.projects.delete');
    Route::post('/projects/AjaxStatusUpdate', [ProjectController::class, 'statusUpdate'])->name('admin.projects.status');
    Route::get('/projects/image/delete/{project}', [ProjectController::class, 'ProjectImageDelete'])->name('admin.projects.image.delete');


    Route::get('/projects/categories', [ProjectController::class, 'categoryList'])->name('admin.projects.categories');
    Route::get('/projects/categories/add', [ProjectController::class, 'categoryAdd'])->name('admin.projects.categories.add');
    Route::post('/projects/categories/create', [ProjectController::class, 'categoryCreate'])->name('admin.projects.categories.create');
    Route::get('/projects/categories/edit/{cat}', [ProjectController::class, 'categoryEdit'])->name('admin.projects.categories.edit');
    Route::post('/projects/categories/update/{cat}', [ProjectController::class, 'categoryUpdate'])->name('admin.projects.categories.update');
    Route::post('/projects/categories/AjaxStatusUpdate', [ProjectController::class, 'categoryStatusUpdate'])->name('admin.projects.categories.status');
    Route::get('/projects/categories/delete/{cat}', [ProjectController::class, 'categoryDelete'])->name('admin.projects.categories.delete');
    Route::post('/projects/AjaxSort', [ProjectController::class, 'AjaxSort'])->name('admin.projects.AjaxSort');
});
// Projects Section   -------------------------------------------------------------------------------------