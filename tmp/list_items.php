<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Item;
use Illuminate\Contracts\Console\Kernel;

$items = Item::all();
foreach ($items as $item) {
    echo 'ID: '.$item->id.' | Name: '.$item->name.' | Type: '.$item->type.' | Status: '.$item->status."\n";
}
