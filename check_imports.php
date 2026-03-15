<?php
$models = array_map(fn($f) => substr($f, 0, -4), array_filter(scandir('app/Models'), fn($f) => str_ends_with($f, '.php')));
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('app/Http/Controllers'));
foreach ($it as $f) {
    if ($f->isDir() || !str_ends_with($f->getFilename(), '.php')) continue;
    $c = file_get_contents($f->getPathname());
    foreach ($models as $m) {
        if (preg_match('/\b' . $m . '::[a-zA-Z]/', $c)) {
            if (!preg_match('/use App\\\\Models\\\\' . $m . ';/', $c) && !preg_match('/namespace App\\\\Models;/', $c)) {
                echo "Missing import for $m in " . $f->getPathname() . "\n";
            }
        }
    }
}
