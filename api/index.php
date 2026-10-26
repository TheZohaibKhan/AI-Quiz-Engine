<?php
foreach ([
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
    '/tmp/storage/fonts',
    '/tmp/bootstrap/cache',
] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

$vars = [
    'LARAVEL_STORAGE_PATH' => '/tmp/storage',
    'VIEW_COMPILED_PATH'   => '/tmp/storage/framework/views',
    'APP_SERVICES_CACHE'   => '/tmp/bootstrap/cache/services.php',
    'APP_PACKAGES_CACHE'   => '/tmp/bootstrap/cache/packages.php',
    'APP_CONFIG_CACHE'     => '/tmp/bootstrap/cache/config.php',
    'APP_ROUTES_CACHE'     => '/tmp/bootstrap/cache/routes.php',
    'APP_EVENTS_CACHE'     => '/tmp/bootstrap/cache/events.php',
];
foreach ($vars as $k => $v) {
    putenv("$k=$v");
    $_ENV[$k] = $v;
    $_SERVER[$k] = $v;
}

require __DIR__ . '/../public/index.php';