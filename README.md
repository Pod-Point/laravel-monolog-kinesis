# Laravel Kinesis Logger

[![Packagist](https://img.shields.io/packagist/v/Pod-Point/laravel-monolog-kinesis.svg)](https://packagist.org/packages/pod-point/laravel-monolog-kinesis)

Package to log Laravel application logs to a Kinesis stream.

## Installation

Require the package in composer:

Laravel < 6.0
```javascript
"require": {
    "pod-point/laravel-monolog-kinesis": "^2.0"
},
```

Laravel 6.0+
```javascript
"require": {
    "pod-point/laravel-monolog-kinesis": "^3.0"
},
```

Add the service provider to your `config/app.php` providers array:

```php
'providers' => [
    PodPoint\KinesisLogger\KinesisMonologServiceProvider::class
]
```

Then, publish the config files:

```php
php artisan vendor:publish --provider="PodPoint\KinesisLogger\KinesisMonologServiceProvider"
```

Make sure to set the `LOGGING_STREAM` in your env file.

Finally, add the logger to your `config/logging.php`

```php
'kinesis' => [
    'driver' => 'kinesis',
    'stream' => env('LOGGING_STREAM'),
    'level' => 'info' // default level info
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
