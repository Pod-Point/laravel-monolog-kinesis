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
     * @param Application $app
     *
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
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('logging.default', 'kinesis-channel');

        $app['config']->set('logging.channels.kinesis-channel', [
            'driver' => 'kinesis',
            'key' => 'dummy-key',
            'secret' => 'dummy-secret',
            'region' => 'eu-west-1',
            'stream' => 'logging',
            'level' => 'debug',
        ]);
    }
}
