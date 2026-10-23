Route::post('/theme/velvet-aura/header', function (\Illuminate\Http\Request $request) {
    $style = $request->input('style', 'style-1');

    if (!in_array($style, ['style-1', 'style-2', 'style-3'])) {
        $style = 'style-1';
    }

    rj_set_option('velvet_header_style', $style);

    return back()->with('success', __('Header style updated.'));
})->name('admin.theme.velvet-aura.header');
