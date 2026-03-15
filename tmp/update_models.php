<?php
$dir = 'd:/ERPInventory/app/Models';
$files = glob($dir . '/*.php');

foreach ($files as $file) {
    $content = file_get_contents($file);
    if (strpos($content, '@mixin \Illuminate\Database\Eloquent\Builder') === false) {
        $content = preg_replace('/class (\w+) extends (Model|Authenticatable)/', "/**\n * @mixin \Illuminate\Database\Eloquent\Builder\n */\nclass $1 extends $2", $content);
        file_put_contents($file, $content);
        echo "Updated $file\n";
    }
}
