<?php

use Illuminate\Support\Facades\Route;
use admin\tags\Controllers\TagManagerController;

Route::name('admin.')->middleware(['web', 'admin.auth'])->group(function () {  
    Route::resource('tags', TagManagerController::class);
    Route::post('tags/updateStatus', [TagManagerController::class, 'updateStatus'])->name('tags.updateStatus');
});
