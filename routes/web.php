<?php
Route::get('/test-419', function () {
    abort(419);
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/frontend.php';

