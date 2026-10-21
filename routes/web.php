<?php

Route::get('/test-403', function () {
    abort(403);
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/frontend.php';
