<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'admin'])->prefix('theme/velvet-aura')->name('theme.velvet-aura.')->group(function () {

    Route::post('/header', function (\Illuminate\Http\Request $request) {
        $style = $request->input('style', 'style-1');
        if (!in_array($style, ['style-1', 'style-2', 'style-3'])) {
            $style = 'style-1';
        }
        rj_set_option('velvet_header_style', $style);
        return back()->with('success', __('Header style updated.'));
    })->name('header');

    Route::post('/footer', function (\Illuminate\Http\Request $request) {
        $style = $request->input('style', 'style-1');
        if (!in_array($style, ['style-1', 'style-2', 'style-3'])) {
            $style = 'style-1';
        }
        rj_set_option('velvet_footer_style', $style);
        return back()->with('success', __('Footer style updated.'));
    })->name('footer');

});
