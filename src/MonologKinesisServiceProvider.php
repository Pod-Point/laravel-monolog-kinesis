<?php

namespace PodPoint\MonologKinesis;

use Illuminate\Contracts\Container\Container;
use Illuminate\Log\LogManager;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Monolog\Logger;
use PodPoint\MonologKinesis\Contracts\Client;

class MonologKinesisServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Client::class, Kinesis::class);

        $this->app->resolving(LogManager::class, function (LogManager $manager) {
            $manager->extend('kinesis', function (Container $app, array $config) {
                return $this->createKinesisLogger($app, $config);
            });
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Create a new instance of a Kinesis logger.
     *
     * @param  Container  $app
     * @param  array  $config
     * @return Logger
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function createKinesisLogger(Container $app, array $config): Logger
    {
        $client = $app->make(Client::class)->configure($config);
        $level = Arr::get($config, 'level', Logger::DEBUG);

        $kinesisHandler = new Handler($client, $config['stream'], $level);
        $kinesisHandler->setFormatter($this->createKinesisFormatter($app));

        return new Logger('kinesis', [$kinesisHandler]);
    }

    /**
     * Create a formatter for a Kinesis logger.
     *
     * @param  Container  $app
     * @return Formatter
     */
    private function createKinesisFormatter(Container $app): Formatter
    {
        /** @var \Illuminate\Contracts\Foundation\Application $app */
        return new Formatter($app['config']->get('app.name'), $app->environment());
    }
}
