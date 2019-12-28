<?php

namespace ComposerHash;

use RuntimeException;
use InvalidArgumentException;

/**
 * Verify Composer hashes in the given project root.
 *
 * @param string $composer_root_dir Absolute path to project root directory.
 *
 * @throws InvalidArgumentException Thrown if the provided path is an invalid Composer project root.
 * @throws RuntimeException Thrown if the required composer files are unreadable.
 * @throws HashMismatchException Thrown if the composer hashes do not match.
 */
function verify($composer_root_dir) {
    $dir = rtrim($composer_root_dir, '/');
    $composer_lock_path = "$dir/composer.lock";
    $composer_hash_path = "$dir/composer.hash";

    if (! file_exists("$dir/composer.json")) {
        throw new InvalidArgumentException("Invalid Composer project root directory at $composer_root_dir");
    }

    if (! is_readable($composer_lock_path) || ! is_readable($composer_hash_path)) {
        throw new RuntimeException("Required Composer files are unreadable.");
    }

    $generated_hash = trim(file_get_contents($composer_hash_path));
    $composer_lock = json_decode(file_get_contents($composer_lock_path), true);
    $composer_lock_hash = $composer_lock['content-hash'] ?? '';

    if ($generated_hash !== $composer_lock_hash) {
        throw new HashMismatchException('Composer hash mismatch. Please run "composer install".');
    }
}
