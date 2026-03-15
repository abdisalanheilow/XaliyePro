<?php

use Database\Seeders\AccountingDataSeeder;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

try {
    $seeder = new AccountingDataSeeder;
    $seeder->run();
    echo "Success\n";
} catch (Throwable $e) {
    $msg = 'ERROR: '.$e->getMessage()."\n".$e->getTraceAsString();
    file_put_contents('error_full.log', $msg);
    echo "Failed. See error_full.log\n";
}
