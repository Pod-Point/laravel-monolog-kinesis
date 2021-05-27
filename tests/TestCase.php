<?php

namespace PodPoint\KinesisLogger\Tests;

use Aws\Kinesis\KinesisClient;
use Illuminate\Foundation\Application;
use Mockery;
use Orchestra\Testbench\TestCase as Orchestra;
use PodPoint\KinesisLogger\KinesisMonologServiceProvider;

abstract class TestCase extends Orchestra
{
    /**
     * Get package providers.
     *
     * @param Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            KinesisMonologServiceProvider::class,
        ];
    }

    /**
     * @return Mockery\MockInterface
     */
    protected function getMockedKinesisClient()
    {
        return Mockery::mock(KinesisClient::class);
    }
}
