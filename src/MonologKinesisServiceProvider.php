<?php

namespace PodPoint\MonologKinesis;

use Illuminate\Contracts\Container\Container;
use Illuminate\Log\LogManager;
use Illuminate\Support\ServiceProvider;
use PodPoint\MonologKinesis\Client as KinesisClient;
use PodPoint\MonologKinesis\Contracts\Client;

class MonologKinesisServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function register()
    {
        $this->app->bind(Client::class, KinesisClient::class);

        $this->app->make(LogManager::class)->extend('kinesis', function (Container $app, array $config) {
            return (new Driver)($app, $config);
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
