<?php

foreach (['/database/migrations/*', '/storage/*migration.php'] as $pattern) {
    $base = realpath(__DIR__.'/../../vendor/orchestra/testbench/fixture');

    $files = glob($base.$pattern);

    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
}

require_once 'vendor/autoload.php';
