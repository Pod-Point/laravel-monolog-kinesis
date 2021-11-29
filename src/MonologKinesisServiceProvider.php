<?php

namespace PodPoint\MonologKinesis;

use Illuminate\Contracts\Container\Container;
use Illuminate\Log\LogManager;
use Illuminate\Support\ServiceProvider;
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
                return (new Driver)($app, $config);
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
}
