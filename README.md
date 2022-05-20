# Laravel Monolog Kinesis Driver


[![Latest Version on Packagist](https://img.shields.io/packagist/v/pod-point/laravel-monolog-kinesis.svg?style=flat-square)](https://packagist.org/packages/pod-point/laravel-monolog-kinesis)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/pod-point/laravel-monolog-kinesis/run-tests?label=tests)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/pod-point/laravel-monolog-kinesis.svg?style=flat-square)](https://packagist.org/packages/pod-point/laravel-monolog-kinesis)

A simple package to forward Laravel application logs to a Kinesis stream.

## Installation

Require the package with composer:

```bash
composer require pod-point/laravel-monolog-kinesis
```

For Laravel < 6.0 you can use `pod-point/laravel-monolog-kinesis:^2.0`.

### Setting up the AWS Kinesis service

Add your AWS key ID, secret and default region to your `config/services.php`:

```php
<?php

return [

    // ...

    'kinesis' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

];
```

## Usage

Simply use the `kinesis` driver on any of your channels within your `config/logging.php`:

```php
<?php

return [

    // ...

    'channels' => [

        'some_channel' => [
            'driver' => 'kinesis',
            'stream' => 'some_stream_name',
            'level' => 'info', // default level is debug
        ],

    ],

];
```

You can optionally specify a different `key`, `secret` and `region` at the channel level too if necessary:

```php
<?php

return [

    // ...

    'channels' => [

        'some_channel' => [
            'driver' => 'kinesis',
            'stream' => env('LOGGING_KINESIS_STREAM'),
            'level' => env('LOG_LEVEL', 'debug'),
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
        ],

    ],

];
```

### HTTP options

You can configure a set of `http` options that are applied to http requests and transfers created when using the AWS SDK from both the `service` and `channel` levels.

```php
// ...
'key' => env('AWS_ACCESS_KEY_ID'),
'secret' => env('AWS_SECRET_ACCESS_KEY'),
'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
'http' => [
    'verify' => false
]
```

More details about all the supported options can be found from the [AWS documentation](https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_configuration.html#config-http).


## Permissions

If you are using an AWS Key, remember to add the `kinesis:PutRecord` and `kinesis:PutRecords` permissions to this user.

## Changelog

Please see our [Releases](/releases) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

---

<img src="https://d3h256n3bzippp.cloudfront.net/pod-point-logo.svg" align="right" />

Travel shouldn't damage the earth 🌍

Made with ❤️&nbsp;&nbsp;at [Pod Point](https://pod-point.com)
