<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Normalize case between REQUEST_URI and SCRIPT_NAME on Windows/XAMPP subdirectories
// so Symfony's prepareBaseUrl case-sensitive prefix matching works seamlessly with lowercase/mixed URLs.
if (isset($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME'])) {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    if ($scriptDir !== '/' && $scriptDir !== '.') {
        $len = strlen($scriptDir);
        if (strncasecmp($_SERVER['REQUEST_URI'], $scriptDir, $len) === 0) {
            $_SERVER['REQUEST_URI'] = $scriptDir . substr($_SERVER['REQUEST_URI'], $len);
        }
    }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
