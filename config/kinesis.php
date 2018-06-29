<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Stream
    |--------------------------------------------------------------------------
    |
    | This is the name of the Kinesis stream we should send application logs to.
    |
    */

    'stream' => env('LOGGING_STREAM'),

    /*
    |--------------------------------------------------------------------------
    | Log level
    |--------------------------------------------------------------------------
    |
    | Minimum log level we should send to Kinesis.
    |
    */

    'level' => \Monolog\Logger::INFO,

    /*
    |--------------------------------------------------------------------------
    | Credentials
    |--------------------------------------------------------------------------
    |
    | Optional AWS credentials we should use to send to Kinesis.
    | It is recommended to leave these empty and add permissions to the
    | EC2 execution role instead.
    |
    */

    'aws' => [
        'key' => env('AWS_KEY'),
        'secret' => env('AWS_SECRET'),
        'region' => env('AWS_REGION'),
    ]

];
