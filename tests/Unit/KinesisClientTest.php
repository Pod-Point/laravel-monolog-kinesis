<?php

namespace PodPoint\KinesisLogger\Tests\Unit;

use Aws\Kinesis\KinesisClient;
use Illuminate\Support\Arr;
use Mockery;
use PodPoint\KinesisLogger\Tests\TestCase;

class KinesisClientTest extends TestCase
{
    /**
     * @return array
     */
    public function loggerLevelTestProvider(): array
    {
        return [
            ['debug'],
            ['info'],
            ['notice'],
            ['warning'],
            ['error'],
            ['critical'],
            ['alert'],
            ['emergency'],
        ];
    }

    /**
     * @dataProvider loggerLevelTestProvider $logLevel
     */
    public function testLoggerLevels($logLevel)
    {
        $this->app['config']->set('logging.channels', [
            'kinesis' => [
                'driver' => 'kinesis',
                'stream' => 'logging',
                'level' => 'debug',
            ],
        ]);

        $this->app['config']->set('logging.default', 'kinesis');

        $mocked = $this->getMockedKinesisClient();

        $mocked->shouldReceive('putRecord')->once()->with(Mockery::on(function ($argument) use ($logLevel) {
            $data = json_decode($argument['Data'], true);

            return $data['level'] == strtoupper($logLevel);
        }));

        $this->app->instance(KinesisClient::class, $mocked);

        logger()->$logLevel("Test {$logLevel} message");
    }

    public function testLoggerDoesNotLogBelowDefaultInfoLevel()
    {
        $this->app['config']->set('logging.channels', [
            'kinesis' => [
                'driver' => 'kinesis',
                'stream' => 'myStream',
            ],
        ]);

        $this->app['config']->set('logging.default', 'kinesis');

        $mocked = $this->getMockedKinesisClient();

        $mocked->shouldNotReceive('putRecord');

        $this->app->instance(KinesisClient::class, $mocked);

        logger()->debug('Test debug message');
    }

    public function testLoggerLogsGreaterThanOrEqualToDefaultInfoLevel()
    {
        $this->app['config']->set('logging.channels', [
            'kinesis' => [
                'driver' => 'kinesis',
                'stream' => 'myStream',
            ],
        ]);

        $this->app['config']->set('logging.default', 'kinesis');

        $mocked = $this->getMockedKinesisClient();

        $mocked->shouldReceive('putRecord')->twice();

        $this->app->instance(KinesisClient::class, $mocked);

        logger()->info('Test info message');
        logger()->warning('Test warning message');
    }

    public function testLoggerDoesNotLogBelowMinimumLevel()
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

        $mocked->shouldReceive('putRecord')->twice();

        $this->app->instance(KinesisClient::class, $mocked);

        logger()->debug('Test debug message');
        logger()->alert('Test alert message');
        logger()->emergency('Test emergency message');
    }

    public function testDataReturnsCorrectArrayKeys()
    {
        $this->app['config']->set('logging.channels', [
            'kinesis' => [
                'driver' => 'kinesis',
                'stream' => 'logging',
                'level' => 'debug',
            ],
        ]);

        $this->app['config']->set('logging.default', 'kinesis');

        $mocked = $this->getMockedKinesisClient();

        $mocked->shouldReceive('putRecord')->once()->with(Mockery::on(function ($argument) {
            $data = json_decode($argument['Data'], true);

            $hasKeys = Arr::has($argument, [
                'Data',
                'PartitionKey',
                'StreamName',
            ]);

            $hasJsonKeys = Arr::has($data, [
                'timestamp',
                'host',
                'project',
                'env',
                'message',
                'channel',
                'level',
                'extra',
                'context',
            ]);

            return $hasKeys && $hasJsonKeys;
        }));

        $this->app->instance(KinesisClient::class, $mocked);

        logger()->info('Test info message');
    }

    public function testDataReturnsCorrectStreamName()
    {
        $this->app['config']->set('logging.channels', [
            'kinesis' => [
                'driver' => 'kinesis',
                'stream' => 'myStream',
                'level' => 'debug',
            ],
        ]);

        $this->app['config']->set('logging.default', 'kinesis');

        $mocked = $this->getMockedKinesisClient();

        $mocked->shouldReceive('putRecord')->once()->with(Mockery::on(function ($argument) {
            return $argument['StreamName'] == 'myStream';
        }));

        $this->app->instance(KinesisClient::class, $mocked);

        logger()->info('Test info message');
    }

    public function testDataReturnsCorrectContext()
    {
        $this->app['config']->set('logging.channels', [
            'kinesis' => [
                'driver' => 'kinesis',
                'stream' => 'myStream',
                'level' => 'debug',
            ],
        ]);

        $this->app['config']->set('logging.default', 'kinesis');

        $mocked = $this->getMockedKinesisClient();

        $mocked->shouldReceive('putRecord')->once()->with(Mockery::on(function ($argument) {
            $data = json_decode($argument['Data'], true);

            return $data['context'] == ['testContext' => ['key' => 'value']];
        }));

        $this->app->instance(KinesisClient::class, $mocked);

        logger()->info('Test info message', ['testContext' => ['key' => 'value']]);
    }
}
