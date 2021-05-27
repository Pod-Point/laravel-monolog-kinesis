<?php

namespace PodPoint\KinesisLogger\Tests\Unit;

use Aws\Kinesis\KinesisClient;
use PodPoint\KinesisLogger\Tests\TestCase;

class KinesisFormatterTest extends TestCase
{
    // add data provider for different levels
    public function testLoggerLevels()
    {
        $this->app['config']->set('logging.channels', [
            'kinesis' => [
                'driver' => 'kinesis',
                'stream' => 'logging',
                'level' => 'info',
            ],
        ]);

        $this->app['config']->set('logging.default', 'kinesis');

        $mocked = $this->getMockedKinesisClient();

        $mocked->shouldReceive('putRecord')->once()->with(\Mockery::on(function ($argument) {
            $data = json_decode($argument['Data'], true);
            return $data['level'] == 'WARNING';
        }));

        $this->app->instance(KinesisClient::class, $mocked);

        logger()->warning('Test warning message');
    }

    // test logger does not log if level is higher than logged level

    public function testDataReturnsCorrectArrayKeys()
    {

    }
}
