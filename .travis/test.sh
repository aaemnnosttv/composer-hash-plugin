#!/usr/bin/env bash

set -ex

cd /tmp
mkdir test-project
cd test-project

composer init -q
composer config repos.hash-plugin path "$TRAVIS_BUILD_DIR"
composer config repos.packagist false
# Note that hash file does not exist yet.
[ ! -f composer.hash ] || exit 1
composer require aaemnnosttv/composer-hash-plugin:dev-master
# Hash file should exist now.
[ -f composer.hash ] || exit 2
# Store the hash for comparison later.
HASH_1=$(<composer.hash)
# Test hashes match.
composer hash-verify
# Install Composer (should already be cached) to change the hash.
composer require composer/composer:\^1
# Store the hash for comparison.
HASH_2=$(<composer.hash)
# Test hashes match.
composer hash-verify
# Ensure hashes changed between installs.
if [ "$HASH_1" == "$HASH_2" ]; then
    echo "Hash file did not update between installs!"
    exit 1
fi
