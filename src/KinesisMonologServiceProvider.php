<?php

namespace PodPoint\KinesisLogger;

use Aws\Kinesis\KinesisClient;
use Illuminate\Contracts\Container\Container;
use Illuminate\Log\LogManager;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Monolog\Logger;
use PodPoint\KinesisLogger\Monolog\KinesisFormatter;
use PodPoint\KinesisLogger\Monolog\KinesisHandler;
use Psr\Log\LogLevel;

class KinesisMonologServiceProvider extends LaravelServiceProvider
{
    /**
     * Push Monolog events to Kinesis.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/monolog-kinesis.php' => config_path('monolog-kinesis.php'),
        ]);

        if ($this->app['log'] instanceof LogManager) {
            $this->app['log']->extend('kinesis', function (Container $app, array $config) {
                $handler = new KinesisHandler($config['stream'], $config['level'] ?? LogLevel::INFO);

                return new Logger('kinesis', [$handler]);
            });
        }

        app()->bind(KinesisFormatter::class, function () {
            return new KinesisFormatter(config('app.name'), $this->app->environment());
        });

        app()->singleton(KinesisClient::class, function () {
            $config = [
                'region' => config('monolog-kinesis.aws.region'),
                'version' => 'latest',
            ];

            $key = config('monolog-kinesis.aws.key');
            $secret = config('monolog-kinesis.aws.secret');

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
