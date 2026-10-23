<?php
$directory = __DIR__ . '/rj-content/plugins/rj-commerce';
$replacements = [
    'RjCommerce\Http\Controllers\Admin' => 'RjCommerce\Controllers',
    'RjCommerce\Http\Controllers' => 'RjCommerce\Controllers',
    'RjCommerce\Http\Middleware' => 'RjCommerce\Middleware',
    'RjCommerce\Http\Models' => 'RjCommerce\Models',
    'RjCommerce\Http\Services' => 'RjCommerce\Services',
];

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $original = $content;

        foreach ($replacements as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }

        if ($content !== $original) {
            file_put_contents($file->getPathname(), $content);
            echo "Updated: " . $file->getPathname() . "\n";
        }
    }
}
echo "Bütün namespace-lər uğurla yeniləndi!!!\n";
