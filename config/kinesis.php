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
    | The AWS credentials we should use to send to Kinesis.
    |
    */

    'key' => env('AWS_KEY'),
    'secret' => env('AWS_SECRET'),

];
