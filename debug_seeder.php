<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Starting seeder...\n";
    (new Database\Seeders\BaclinkSeeder)->run();
    echo "Seeder finished successfully!\n";
} catch (\Exception $e) {
    $msg = "ERROR CAUGHT:\n" . $e->getMessage() . "\n";
    if ($e instanceof \Illuminate\Database\QueryException) {
        $msg .= "SQL that failed: " . $e->getSql() . "\n";
        $msg .= "Bindings: " . json_encode($e->getBindings()) . "\n";
    }
    $msg .= "In file: " . $e->getFile() . " on line " . $e->getLine() . "\n";
    file_put_contents('seeder_log.txt', $msg);
    echo "ERROR logged to seeder_log.txt\n";
}
