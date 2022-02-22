<?php

namespace PodPoint\MonologKinesis\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;
use PodPoint\MonologKinesis\MonologKinesisServiceProvider;

abstract class TestCase extends Orchestra
{
    /**
     * Get package providers.
     *
     * @param  Application  $app
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            MonologKinesisServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('services.kinesis', [
            'key' => 'dummy-key',
            'secret' => 'dummy-secret',
            'region' => 'eu-west-1',
        ]);

        $app['config']->set('logging.default', 'some_channel');

        $app['config']->set('logging.channels.some_channel', [
            'driver' => 'kinesis',
            'stream' => 'logging',
            'level' => 'debug',
        ]);
    }
}
