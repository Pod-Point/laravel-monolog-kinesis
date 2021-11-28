<?php

namespace PodPoint\MonologKinesis\Tests;

use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Mockery as m;
use PodPoint\MonologKinesis\Contracts\Client;

class KinesisLoggingDriverTest extends TestCase
{
    use InteractsWithClient;

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

    /** @dataProvider loggerLevelTestProvider $logLevel */
    public function test_standard_log_levels_are_supported($logLevel)
    {
        $this->mockClient()
            ->shouldReceive('putRecord')
            ->once()
            ->with(m::on(function ($argument) use ($logLevel) {
                $data = json_decode($argument['Data'], true);

                return $data['level'] === strtoupper($logLevel);
            }));

        logger()->$logLevel("Test {$logLevel} message");
    }

    public function test_driver_does_not_log_below_default_log_level()
    {
        config()->set('logging.channels.kinesis-channel.level', 'warning');

        $this->mockClient()->shouldReceive('putRecord')->once();

        logger()->warning('Test warning message');
        logger()->debug('Test debug message');
    }

    public function test_driver_does_log_greater_than_or_equal_to_default_log_level()
    {
        config()->set('logging.channels.kinesis-channel.level', 'warning');

        $this->mockClient()->shouldReceive('putRecord')->twice();

        logger()->warning('Test warning message');
        logger()->error('Test error message');
    }

    public function test_data_pushed_to_kinesis_is_properly_formatted()
    {
        $this->mockClient()->shouldReceive('putRecord')->once()->with(m::on(function ($argument) {
            $hasKeys = Arr::has($argument, ['Data', 'PartitionKey', 'StreamName']);

            $hasJsonKeys = Arr::has(json_decode($argument['Data'], true), [
                'timestamp', 'host', 'project', 'env', 'message',
                'channel', 'level', 'extra', 'context',
            ]);

            return $hasKeys && $hasJsonKeys;
        }));

        logger()->info('Test info message');
    }

    public function test_a_kinesis_stream_name_has_to_be_specified()
    {
        config()->set('logging.channels.kinesis-channel.stream');
        Event::fake([MessageLogged::class]);

        $this->mockClient()->shouldNotReceive('putRecord');

        logger()->info('Test info message');

        Event::assertDispatched(function (MessageLogged $event) {
            return $event->level === 'emergency'
                && Str::contains($event->context['exception']->getMessage(), '($stream) must be of type string');
        });
    }

    public function test_data_pushed_to_kinesis_can_also_forward_some_context()
    {
        $this->mockClient()->shouldReceive('putRecord')->once()->with(m::on(function ($argument) {
            $data = json_decode($argument['Data'], true);

            return $data['context'] === ['some-context' => ['key' => 'value']];
        }));

        logger()->info('Test info message', ['some-context' => ['key' => 'value']]);
    }

    public function test_channel_specific_aws_credentials_can_be_given()
    {
        $mock = $this->mock(Client::class);

        $mock->shouldReceive('configure')->once()->with(m::on(function ($argument) {
            return $argument['key'] === 'some-other-key'
                && $argument['secret'] === 'some-other-secret'
                && $argument['region'] === 'another-region';
        }))->andReturn($mock);

        $mock->shouldReceive('putRecord')->once();

        config()->set('logging.default', 'another-kinesis-channel');
        config()->set('logging.channels.another-kinesis-channel', [
            'driver' => 'kinesis',
            'key' => 'some-other-key',
            'secret' => 'some-other-secret',
            'region' => 'another-region',
            'stream' => 'logging',
            'level' => 'debug',
        ]);

        logger()->warning('Something went wrong.');
    }
}
