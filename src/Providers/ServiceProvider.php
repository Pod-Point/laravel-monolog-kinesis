<?php

namespace PodPoint\KinesisLogger\Providers;

use Aws\Kinesis\KinesisClient;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use PodPoint\KinesisLogger\Monolog\KinesisFormatter;
use PodPoint\KinesisLogger\Monolog\KinesisHandler;

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

        if (config('kinesis.stream')) {
            $monolog = Log::getLogger();

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

            $client = new KinesisClient($config);

            $handler = new KinesisHandler($client, config('kinesis.stream'), config('kinesis.level'));
            $handler->setFormatter(new KinesisFormatter(config('app.name'), App::environment()));

            $monolog->pushHandler($handler);
        }
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
