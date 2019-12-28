# Composer Hash Plugin

A Composer plugin for writing the Composer hash to a file on install/update to verify parity with VCS.

## Overview

This package aims to solve the problem of your installed dependencies getting out of sync with those defined by your lock file.
As such, it is intended to be used in projects where the `composer.lock` file is under version control.

Once installed, the plugin will write the current `content-hash` from your `composer.lock` file to a new `composer.hash` file after each `composer install` or `update`.
This is the only thing it will do automatically.
This new file is intended to be excluded from version control.
The hashes can then be verified, but that has to be done (semi) manually. 

#### Verifying the Hash

The plugin exposes a single `ComposerHash\verify($path)` function where `$path` is the absolute path to the project root directory containing `composer.json`.
This function's only job is to check that the `composer.hash` matches that from the `composer.lock` file (if it doesn't, a `HashMismatchException` is thrown.
Other exceptions are thrown if called with an invalid path or if composer files are unreadable.
 
## Installation

```
composer require aaemnnosttv/composer-hash-plugin
```
