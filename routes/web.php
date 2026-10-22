<?php

Route::get('/test-400', function () {
    abort(400);
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/frontend.php';

