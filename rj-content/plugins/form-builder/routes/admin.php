<?php

use Illuminate\Support\Facades\Route;
use RjPlugins\FormBuilder\Controllers\Admin\FormController;
use RjPlugins\FormBuilder\Controllers\Admin\SubmissionController;

Route::middleware(['web', 'auth', 'admin'])->prefix('admin/plugin/forms')->name('admin.plugin.forms.')->group(function () {
    Route::get('/', [FormController::class, 'index'])->name('index');
    Route::get('/create', [FormController::class, 'create'])->name('create');
    Route::post('/', [FormController::class, 'store'])->name('store');
    Route::get('/{form}/edit', [FormController::class, 'edit'])->name('edit');
    Route::put('/{form}', [FormController::class, 'update'])->name('update');
    Route::delete('/{form}', [FormController::class, 'destroy'])->name('destroy');

    Route::get('/{form}/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
    Route::get('/{form}/submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
    Route::delete('/{form}/submissions/{submission}', [SubmissionController::class, 'destroy'])->name('submissions.destroy');
});
