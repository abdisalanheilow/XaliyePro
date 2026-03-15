<?php
$dir = 'd:/ERPInventory/resources/views';

function processDir($dir) {
    echo "Processing $dir...\n";
    $files = glob($dir . '/*');
    foreach ($files as $file) {
        if (is_dir($file)) {
            processDir($file);
        } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            $content = file_get_contents($file);
            $newContent = preg_replace('/@(foreach|forelse|if|elseif|while|unless|switch)\(/', '@$1 (', $content);
            if ($newContent !== $content) {
                file_put_contents($file, $newContent);
                echo "Updated $file\n";
            }
        }
    }
}

processDir($dir);
