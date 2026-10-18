<?php

use Illuminate\Support\Facades\Route;
use RjPlugins\FormBuilder\Controllers\Frontend\FormSubmitController;

Route::middleware('web')->group(function () {
    Route::post('/rj-form/{slug}', [FormSubmitController::class, 'submit'])->name('plugin.form.submit');
});
