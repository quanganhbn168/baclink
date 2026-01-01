<?php
spl_autoload_register(function ($class) {
    if ($class === 'App\Http\Livewire\Admin\ProductForm') {
        echo "<h1>[DEBUG] Attempting to load ProductForm...</h1>";
        echo "<pre>";
        debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        echo "</pre>";
    }
}, true, true);
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
