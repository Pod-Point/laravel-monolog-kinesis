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
                $client = $app->make(Client::class);

                $formatter = $app->make($config['formatter'] ?? Formatter::class, [
                    'name' => $app->config->get('app.name'),
                    'environment' => $app->environment(),
                ]);

                return (new LoggerFactory($client, $formatter))->create($config);
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
