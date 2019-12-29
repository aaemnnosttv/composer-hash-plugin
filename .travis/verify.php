<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

try {
    ComposerHash\verify(getcwd());
    echo "Hashes match.\n";
} catch (Exception $exception) {
    echo $exception->getMessage() . "\n";
    exit(1);
}