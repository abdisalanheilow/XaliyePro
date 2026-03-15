<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Account;
use Illuminate\Contracts\Console\Kernel;

$accounts = Account::where('name', 'like', '%Cash%')
    ->orWhere('name', 'like', '%Payable%')
    ->get(['id', 'name', 'code']);

foreach ($accounts as $a) {
    echo "{$a->name}: {$a->code} (ID: {$a->id})\n";
}
