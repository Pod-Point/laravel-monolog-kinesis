<?php

namespace PodPoint\MonologKinesis;

use Illuminate\Contracts\Container\Container;
use Monolog\Logger;
use PodPoint\MonologKinesis\Contracts\Client;

class Driver
{
    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __invoke(Container $app, array $config): Logger
    {
        $client = $app->make(Client::class)->configure($config);
        $level = $config['level'] ?? Logger::DEBUG;

        $kinesisHandler = new Handler($client, $config['stream'], $level);
        $kinesisHandler->setFormatter($this->createKinesisFormatter($app));

        return new Logger('kinesis', [$kinesisHandler]);
    }

    private function createKinesisFormatter(Container $app): Formatter
    {
        /** @var \Illuminate\Contracts\Foundation\Application $app */
        return new Formatter($app['config']->get('app.name'), $app->environment());
    }
}
