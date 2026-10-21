<?php
Route::get('/test_link', function () {
    abort(401);
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/frontend.php';

