<?php

namespace PodPoint\KinesisLogger\Providers;

use Aws\Kinesis\KinesisClient;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Push Monolog events to Kinesis.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/kinesis.php' => config_path('kinesis.php'),
        ]);

        app()->bind(KinesisClient::class, function () {
            $config = [
                'region' => config('kinesis.aws.region'),
                'version' => 'latest'
            ];

            $key = config('kinesis.aws.key');
            $secret = config('kinesis.aws.secret');

            if ($key && $secret) {
                $config['credentials'] = [
                    'key' => $key,
                    'secret' => $secret,
                ];
            }

            return new KinesisClient($config);
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
