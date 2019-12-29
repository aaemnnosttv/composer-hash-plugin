<?php

require_once __DIR__ . '/vendor/autoload.php';

try {
    ComposerHash\verify(__DIR__);
    echo "Hashes match.\n";
} catch (Exception $exception) {
    echo $exception->getMessage() . "\n";
    exit(1);
}
