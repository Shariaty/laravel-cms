<?php


// Skills Section   -------------------------------------------------------------------------------------
use Modules\Skill\Http\Controllers\SkillController;

Route::group( ['middleware' => 'can:'.config('permissions.PERMISSION_SKILLS') , 'prefix' => 'administrator'] ,function() {
    Route::get('/skills/list', [SkillController::class, 'index'])->name('admin.skills.list');
    Route::get('/skills/add', [SkillController::class, 'add'])->name('admin.skills.add');
    Route::get('/skills/edit/{skill}', [SkillController::class, 'edit'])->name('admin.skills.edit');
    Route::post('/skills/update/{skill}', [SkillController::class, 'update'])->name('admin.skills.update');
    Route::get('/skills/delete/{skill}', [SkillController::class, 'delete'])->name('admin.skills.delete');
    Route::post('/skills/AjaxStatusUpdate', [SkillController::class, 'statusUpdate'])->name('admin.skills.status');
    Route::get('/skills/image/delete/{skill}', [SkillController::class, 'SkillImageDelete'])->name('admin.skills.image.delete');

    Route::post('/skills/ajaxFileUpload', [SkillController::class, 'ajaxFileUpload'])->name('admin.skills.ajaxFileUpload');
    Route::get('/skills/fileRemove/{skill}/{type}', [SkillController::class, 'magazineFileRemove'])->name('admin.skills.removeFile');
    Route::get('/skills/fileView/{skill}/{type}', [SkillController::class, 'magazineFileView'])->name('admin.skills.fileView');


    Route::get('/skills/categories', [SkillController::class, 'categoryList'])->name('admin.skills.categories');
    Route::get('/skills/categories/add', [SkillController::class, 'categoryAdd'])->name('admin.skills.categories.add');
    Route::post('/skills/categories/create', [SkillController::class, 'categoryCreate'])->name('admin.skills.categories.create');
    Route::get('/skills/categories/edit/{skill_cat}', [SkillController::class, 'categoryEdit'])->name('admin.skills.categories.edit');
    Route::post('/skills/categories/update/{skill_cat}', [SkillController::class, 'categoryUpdate'])->name('admin.skills.categories.update');
    Route::post('/skills/categories/AjaxStatusUpdate', [SkillController::class, 'categoryStatusUpdate'])->name('admin.skills.categories.status');
    Route::get('/skills/categories/delete/{skill_cat}', [SkillController::class, 'categoryDelete'])->name('admin.skills.categories.delete');
    Route::post('/skills/AjaxSort', [SkillController::class, 'AjaxSort'])->name('admin.skills.AjaxSort');
});
// Skills Section   -------------------------------------------------------------------------------------
