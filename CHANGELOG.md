# Changelog

All notable changes to `laravel-monolog-kinesis` will be documented in this file.

## 3.0.0 - 2021-06-04

- First release supporting PHP 7.2+ and Laravel 6+ [#5](https://github.com/Pod-Point/laravel-monolog-kinesis/pull/5)
- Drop support for Monolog 1.* and PHPUnit 7.*
- Switch to Github Actions from Travis CI
- Moved to Laravel custom log driver implementation

### Breaking Changes:

- This version no longer supports Laravel < 6. Please use version 2 which supports it.

## 2.0.0 - 2021-06-04

- Fix for deprecated `getMonolog` method [#3](https://github.com/Pod-Point/laravel-monolog-kinesis/pull/3)
