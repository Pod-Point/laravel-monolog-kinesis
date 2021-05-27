<?php

namespace PodPoint\KinesisLogger\Tests\Unit;

use Illuminate\Support\Facades\Log;
use Monolog\Logger;
use PodPoint\KinesisLogger\Monolog\KinesisHandler;
use PodPoint\KinesisLogger\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    /**
     * Test the service provider registers the handler.
     */
    public function testServiceProvider()
    {
        $this->app['config']->set('logging.channels', [
            'kinesis' => [
                'driver' => 'kinesis',
                'stream' => 'logging',
                'level' => 'info',
            ],
        ]);

        $this->app['config']->set('logging.default', 'kinesis');

        /** @var Logger $monolog */
        $monolog = Log::getLogger();

        $this->assertInstanceOf(KinesisHandler::class, array_first($monolog->getHandlers()));
    }
}
