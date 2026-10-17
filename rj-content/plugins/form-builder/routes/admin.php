<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'admin'])->prefix('admin/plugin/forms')->name('admin.plugin.forms.')->group(function () {
    // Form Builder admin route-ları sonradan bura gələcək
    Route::get('/', function () {
        return 'Form Builder Admin (Coming Soon)';
    })->name('index');
});