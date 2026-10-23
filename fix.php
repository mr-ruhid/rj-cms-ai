<?php

$basePath = __DIR__ . '/rj-content/plugins/rj-commerce';

// 1. Admin Controller-lərin namespace-ni düzəlt
$adminPath = $basePath . '/src/Controllers/Admin';
if (is_dir($adminPath)) {
    foreach (glob($adminPath . '/*.php') as $file) {
        $content = file_get_contents($file);
        $content = str_replace('namespace RjCommerce\Controllers;', 'namespace RjCommerce\Controllers\Admin;', $content);
        file_put_contents($file, $content);
    }
}

// 2. API Controller-lərin namespace-ni düzəlt
$apiPath = $basePath . '/src/Controllers/Api';
if (is_dir($apiPath)) {
    foreach (glob($apiPath . '/*.php') as $file) {
        $content = file_get_contents($file);
        $content = str_replace('namespace RjCommerce\Controllers;', 'namespace RjCommerce\Controllers\Api;', $content);
        file_put_contents($file, $content);
    }
}

// 3. Middleware namespace-ni düzəlt
$middlewarePath = $basePath . '/src/Middleware';
if (is_dir($middlewarePath)) {
    foreach (glob($middlewarePath . '/*.php') as $file) {
        $content = file_get_contents($file);
        $content = str_replace('namespace RjCommerce\Http\Middleware;', 'namespace RjCommerce\Middleware;', $content);
        file_put_contents($file, $content);
    }
}

// 4. routes/admin.php faylındakı use yollarını düzəlt
$adminRoutePath = $basePath . '/routes/admin.php';
if (file_exists($adminRoutePath)) {
    $content = file_get_contents($adminRoutePath);
    $controllers = [
        'ProductController', 'OrderController', 'CategoryController', 'BrandController',
        'TagController', 'AttributeController', 'CouponController', 'ReviewController',
        'ShippingController', 'SettingsController'
    ];
    foreach ($controllers as $c) {
        $content = str_replace("use RjCommerce\\Controllers\\{$c};", "use RjCommerce\\Controllers\\Admin\\{$c};", $content);
    }
    file_put_contents($adminRoutePath, $content);
}

// 5. routes/api.php faylındakı use yollarını düzəlt
$apiRoutePath = $basePath . '/routes/api.php';
if (file_exists($apiRoutePath)) {
    $content = file_get_contents($apiRoutePath);
    $apiControllers = ['ApiOrderController', 'ApiProductController'];
    foreach ($apiControllers as $c) {
        $content = str_replace("use RjCommerce\\Controllers\\{$c};", "use RjCommerce\\Controllers\\Api\\{$c};", $content);
        $content = str_replace("use RjCommerce\\Http\\Controllers\\Api\\{$c};", "use RjCommerce\\Controllers\\Api\\{$c};", $content);
    }
    $content = str_replace("\\RjCommerce\\Http\\Middleware\\AuthenticateCommerceApi::class", "\\RjCommerce\\Middleware\\AuthenticateCommerceApi::class", $content);
    file_put_contents($apiRoutePath, $content);
}

echo "Bütün namespace-lər və yollar avtomatik düzəldildi!\n";
