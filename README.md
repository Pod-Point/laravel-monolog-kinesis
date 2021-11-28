# Laravel Monolog Kinesis Driver

[![Packagist](https://img.shields.io/packagist/v/Pod-Point/laravel-monolog-kinesis.svg)](https://packagist.org/packages/pod-point/laravel-monolog-kinesis)

A simple package to forward Laravel application logs to a Kinesis stream.

## Installation

Require the package with composer:

Laravel < 6.0
```bash
composer require pod-point/laravel-monolog-kinesis:^2.0
```

Laravel 6.0+
```bash
composer require pod-point/laravel-monolog-kinesis:^3.0
```

## Usage

Simply use our `kinesis` driver on any of your channels within your `config/logging.php`:

```php
'kinesis' => [
    'driver' => 'kinesis',
    'stream' => env('LOGGING_STREAM'), // don't forget to define the Kinesis stream name
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    'level' => 'info' // default level is debug
],
```

*Note*: If you are using the log channel `stack`, ensure you add the `kinesis` channel.

## Permissions

If you are using an AWS Key, remember to add the `kinesis:PutRecord` and `kinesis:PutRecords` permissions to this user.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

---

<img src="https://d3h256n3bzippp.cloudfront.net/pod-point-logo.svg" align="right" />

Travel shouldn't damage the earth 🌍

Made with ❤️&nbsp;&nbsp;at [Pod Point](https://pod-point.com)
