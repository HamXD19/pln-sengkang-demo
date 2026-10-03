<?php

// Display errors for debugging serverless startup issues
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Prepare temporary writable directories in /tmp
$tmpDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
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

// Copy pre-seeded SQLite database to writable /tmp
$sqliteSource = __DIR__ . '/../database/database.sqlite';
$sqliteDest = '/tmp/database.sqlite';
if (!file_exists($sqliteDest) && file_exists($sqliteSource)) {
    @copy($sqliteSource, $sqliteDest);
}

// Copy cached manifest if available
if (file_exists(__DIR__ . '/../bootstrap/cache/packages.php')) {
    @copy(__DIR__ . '/../bootstrap/cache/packages.php', '/tmp/bootstrap/cache/packages.php');
}
if (file_exists(__DIR__ . '/../bootstrap/cache/services.php')) {
    @copy(__DIR__ . '/../bootstrap/cache/services.php', '/tmp/bootstrap/cache/services.php');
}

define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

try {
    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    $app->useStoragePath('/tmp/storage');
    $app->useBootstrapPath('/tmp/bootstrap');

    $request = \Illuminate\Http\Request::capture();
    $app->handleRequest($request);
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<!DOCTYPE html><html><head><title>Deployment Error</title>";
    echo "<style>body{font-family:sans-serif;padding:30px;background:#f8fafc;color:#1e293b;}pre{background:#1e293b;color:#f8fafc;padding:15px;border-radius:8px;overflow-x:auto;}</style></head><body>";
    echo "<h1>🚨 Serverless Execution Error</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Location:</strong> " . htmlspecialchars($e->getFile()) . " (Line " . $e->getLine() . ")</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</body></html>";
    exit(1);
}
