# Changelog

All notable changes to `laravel-monolog-kinesis` will be documented in this file.

## 3.0.0 - 2021-06-04

- First release supporting PHP 7.2+ and Laravel 6+ [#5](https://github.com/Pod-Point/laravel-monolog-kinesis/pull/5)
- Drop support for Monolog `1.*` and PHPUnit `7.*`
- Switch to Github Actions from Travis CI
- Moved to Laravel custom log driver implementation

### Breaking Changes:

- This version no longer supports Laravel `5.*`, please use version `2.*` which supports it.

## 2.0.0 - 2018-11-05

- Fix for deprecated `getMonolog` method [#3](https://github.com/Pod-Point/laravel-monolog-kinesis/pull/3)
- Update `README.md`

## 1.3.0 - 2018-10-10

- Add `register()` for the old Laravel abstract class [#2](https://github.com/Pod-Point/laravel-monolog-kinesis/pull/2)

## 1.2.0 - 2018-07-02

- Set default region

## 1.1.0 - 2018-06-29

- Make credentials optional

## 1.0.0 - 2018-06-29

- Initial release
