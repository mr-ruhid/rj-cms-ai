<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'admin'])->prefix('admin/plugin/forms')->name('admin.plugin.forms.')->group(function () {
    Route::get('/', function () {
        return 'Form Builder Admin Panel (Tezliklə)';
    })->name('index');
});