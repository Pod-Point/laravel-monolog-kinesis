<?php

namespace PodPoint\KinesisLogger\Providers;

use Aws\Kinesis\KinesisClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use PodPoint\KinesisLogger\Monolog\KinesisFormatter;
use PodPoint\KinesisLogger\Monolog\KinesisHandler;

class LoggingProvider extends LaravelServiceProvider
{
    /**
     * Push Monolog events to Kinesis.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/kinesis.php' => config_path('kinesis.php'),
        ]);

        if (config('kinesis.stream')) {
            $monolog = Log::getMonolog();

            $client = new KinesisClient([
                'credentials' => [
                    'key'    => config('kinesis.key'),
                    'secret' => config('kinesis.secret')
                ],
                'region' => 'eu-west-1',
                'version' => 'latest'
            ]);

            $handler = new KinesisHandler($client, config('kinesis.stream'), config('kinesis.level'));
            $handler->setFormatter(new KinesisFormatter(config('app.name'), app('environment')));

            $monolog->pushHandler($handler);
        }
    }
}
