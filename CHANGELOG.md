# Changelog

All notable changes to `laravel-monolog-kinesis` will be documented in this file.

## v4.0.0 - 2022-02-22

### Refactoring + Laravel 9 support [#8](https://github.com/Pod-Point/laravel-monolog-kinesis/pull/8)

- Fix CI pipeline (was using outdated Ubuntu 16)
- No need for our own `config/monolog-kinesis.php` file, we can piggy back on `config/services.php` for default AWS creds
- Using the decorator pattern to create `src/Kinesis.php` in order to avoid binding the raw `KinesisClient` from the AWS SDK into the container. Let's say we need this Client to stream another kind of data through Kinesis but using another region or another set of credentials, we wouldn't be able to resolve a different instance, with a different config. This gives us much more control over our Client and makes it easier to test too.
- Binding against an interface instead of a class so it's easier to test and makes it bespoke to our package as we're binding against `PodPoint\MonologKinesis\Contracts\Client` instead of `Aws\Kinesis\KinesisClient`.
- Ability to define AWS creds both at channel level and at `config/services.php` level (for the defaults)
- Add missing tests
- Improve naming convention
- Update `README.md`
- Automatically update `CHANGELOG.md` upon new releases
- Adding Laravel 9 and PHP 8.1 support

## v3.0.0 - 2021-06-04

- First release supporting PHP 7.2+ and Laravel 6+ [#5](https://github.com/Pod-Point/laravel-monolog-kinesis/pull/5)
- Drop support for Monolog `1.*` and PHPUnit `7.*`
- Switch to Github Actions from Travis CI
- Moved to Laravel custom log driver implementation

### Breaking Changes:

- This version no longer supports Laravel `5.*`, please use version `2.*` which supports it.

## v2.0.0 - 2018-11-05

- Fix for deprecated `getMonolog` method [#3](https://github.com/Pod-Point/laravel-monolog-kinesis/pull/3)
- Update `README.md`

## v1.3.0 - 2018-10-10

- Add `register()` for the old Laravel abstract class [#2](https://github.com/Pod-Point/laravel-monolog-kinesis/pull/2)

## v1.2.0 - 2018-07-02

- Set default region

## v1.1.0 - 2018-06-29

- Make credentials optional

## v1.0.0 - 2018-06-29

- Initial release
