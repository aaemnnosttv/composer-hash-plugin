<?php

namespace ComposerHash;

use InvalidArgumentException;
use RuntimeException;

class Hash
{
    /**
     * Verify Composer hashes in the given project root.
     *
     * @param string $composer_root_dir Absolute path to project root directory.
     *
     * @throws InvalidArgumentException Thrown if the provided path is an invalid Composer project root.
     * @throws RuntimeException Thrown if the required composer files are unreadable.
     * @throws HashMismatchException Thrown if the composer hashes do not match.
     */
    public static function verify($composer_root_dir) {
        $dir = rtrim($composer_root_dir, '/');

        if (! file_exists("$dir/composer.json")) {
            throw new InvalidArgumentException("Invalid Composer project root directory at $composer_root_dir");
        }

        $generated_hash = self::json_get("$dir/composer.hash", 'hash');
        $composer_lock_hash = self::json_get("$dir/composer.lock", 'content-hash');

        if ($generated_hash !== $composer_lock_hash) {
            throw new HashMismatchException('Composer hash mismatch. Please run "composer install".');
        }
    }

    private static function json_get($file_path, $key) {
        if (! is_readable($file_path)) {
            throw new RuntimeException("Required Composer file is unreadable: $file_path");
        }

        $decoded = json_decode(file_get_contents($file_path), true);

        if ($decoded === null) {
            throw new RuntimeException("Failed to decode JSON in $file_path");
        }

        if (! array_key_exists($key, $decoded)) {
            return null;
        }

        return $decoded[$key];
    }
}
