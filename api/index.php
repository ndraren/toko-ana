<?php

/**
 * Entry point untuk Vercel Serverless Function
 * Menyiapkan folder temporary di /tmp agar Laravel tidak error read-only
 */

$tmpDirs = [
    '/tmp/views',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Redirect cache bootstrap ke /tmp (karena /var/task/bootstrap/cache di Vercel itu read-only)
$envMappings = [
    'VERCEL'             => '1',
    'APP_DEBUG'          => 'true',
    'SESSION_DRIVER'     => 'cookie',
    'CACHE_DRIVER'       => 'array',
    'LOG_CHANNEL'        => 'stderr',
    'VIEW_COMPILED_PATH' => '/tmp/views',
    'APP_SERVICES_CACHE' => '/tmp/bootstrap/cache/services.php',
    'APP_PACKAGES_CACHE' => '/tmp/bootstrap/cache/packages.php',
    'APP_CONFIG_CACHE'   => '/tmp/bootstrap/cache/config.php',
    'APP_ROUTES_CACHE'   => '/tmp/bootstrap/cache/routes.php',
    'APP_EVENTS_CACHE'   => '/tmp/bootstrap/cache/events.php',
];

foreach ($envMappings as $key => $val) {
    putenv("{$key}={$val}");
    $_ENV[$key] = $val;
    $_SERVER[$key] = $val;
}

// Teruskan ke file public/index.php bawaan Laravel
require __DIR__ . '/../public/index.php';
