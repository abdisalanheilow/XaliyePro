<?php

use App\Models\Item;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();
$items = Item::get(['id', 'name', 'type', 'status']);
foreach ($items as $i) {
    echo "ID:{$i->id}|N:{$i->name}|T:{$i->type}|S:{$i->status}\n";
}
