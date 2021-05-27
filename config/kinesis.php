<?php
return [
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
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'eu-west-1'),
    ],

];
