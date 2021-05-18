# Laravel Kinesis Logger

[![Build Status](https://travis-ci.org/Pod-Point/laravel-monolog-kinesis.svg?branch=master)](https://travis-ci.org/Pod-Point/laravel-monolog-kinesis) [![Packagist](https://img.shields.io/packagist/v/Pod-Point/laravel-monolog-kinesis.svg)](https://packagist.org/packages/pod-point/laravel-monolog-kinesis)

Package to log Laravel application logs to a Kinesis stream.

## Installation

Require the package in composer:

```javascript
"require": {
    "pod-point/laravel-monolog-kinesis": "^2.0"
},
```

Add the service provider to your `config/app.php` providers array:

```php
'providers' => [
    PodPoint\KinesisLogger\Providers\ServiceProvider::class
]
```

Then, publish the config files:

```php
php artisan vendor:publish --provider="PodPoint\KinesisLogger\Providers\ServiceProvider"
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
