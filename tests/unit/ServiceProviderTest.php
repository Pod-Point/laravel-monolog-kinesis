<?php

use PodPoint\KinesisLogger\Providers\ServiceProvider;
use Orchestra\Testbench\TestCase;
use PodPoint\KinesisLogger\Monolog\KinesisHandler;

class ServiceProviderTest extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('kinesis.stream', 'stream');
        $app['config']->set('kinesis.aws.region', 'region');
        $app['config']->set('kinesis.aws.key', 'key');
        $app['config']->set('kinesis.aws.secret', 'secret');
        $app['config']->set('kinesis.level', 1);


        $app['config']->set('logging.channels', [
            'kinesis' => [
                'driver' => 'monolog',
                'handler' => \PodPoint\KinesisLogger\Monolog\KinesisHandler::class,
                'with' => [
                    'streamName' => config('kinesis.stream'),
                    'level' => config('kinesis.level'),
                ],
                'formatter' => \PodPoint\KinesisLogger\Monolog\KinesisFormatter::class,
                'formatter_with' => [
                    'name' => config('app.name'),
                ],
            ],
        ]);

        $app['config']->set('logging.default', 'kinesis');
    }

    /**
     * Test the service provider registers the handler.
     */
    public function testServiceProvider()
    {
        $provider = new ServiceProvider($this->app);
        $provider->boot();

        $monolog = Log::getLogger();

        $this->assertEquals(KinesisHandler::class, get_class($monolog->popHandler()));
    }
}
