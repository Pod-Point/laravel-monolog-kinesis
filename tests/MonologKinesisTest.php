<?php

namespace PodPoint\MonologKinesis\Tests;

use Illuminate\Support\Arr;
use Mockery as m;

class MonologKinesisTest extends TestCase
{
    use InteractsWithKinesis;

    /** @define-env withDefaultCredentials */
    public function test_aws_credentials_can_be_configured_at_the_service_level()
    {
        $kinesisHandler = collect(logger()->channel('some_channel')->getHandlers())->first();

        $this->assertInstanceOf(\PodPoint\MonologKinesis\Handler::class, $kinesisHandler);

        /** @var \PodPoint\MonologKinesis\Handler $kinesisHandler */
        $this->assertEquals([
            'region' => 'eu-west-1',
            'version' => 'latest',
            'http' => [],
            'credentials' => [
                'key' => 'dummy-key',
                'secret' => 'dummy-secret',
            ],
        ], $kinesisHandler->getClient()->getAwsConfig());
    }

    /** @define-env withoutDefaultCredentials */
    public function test_aws_credentials_can_be_completely_optional()
    {
        $kinesisHandler = collect(logger()->channel('some_channel')->getHandlers())->first();

        $this->assertInstanceOf(\PodPoint\MonologKinesis\Handler::class, $kinesisHandler);

        /** @var \PodPoint\MonologKinesis\Handler $kinesisHandler */
        $this->assertEquals([
            'region' => 'eu-west-1',
            'version' => 'latest',
            'http' => [],
        ], $kinesisHandler->getClient()->getAwsConfig());
    }

    /** @define-env withNullDefaultCredentials */
    public function test_aws_credentials_can_be_null()
    {
        $kinesisHandler = collect(logger()->channel('some_channel')->getHandlers())->first();

        $this->assertInstanceOf(\PodPoint\MonologKinesis\Handler::class, $kinesisHandler);

        /** @var \PodPoint\MonologKinesis\Handler $kinesisHandler */
        $this->assertEquals([
            'region' => 'eu-west-1',
            'version' => 'latest',
            'http' => [],
        ], $kinesisHandler->getClient()->getAwsConfig());
    }

    /** @define-env withDefaultCredentials */
    public function test_aws_credentials_can_be_configured_at_the_channel_level_overriding_default_credentials()
    {
        $this->mockKinesisWith(function ($mock) {
            $mock->shouldReceive('configure')->once()->with(m::on(function ($argument) {
                return $argument['key'] === 'some_other_key'
                && $argument['secret'] === 'some_other_secret'
                && $argument['region'] === 'another_region';
            }))->andReturn($mock);

            $mock->shouldReceive('putRecord')->once();
        });

        config()->set('logging.default', 'another_channel');
        config()->set('logging.channels.another_channel', [
            'driver' => 'kinesis',
            'key' => 'some_other_key',
            'secret' => 'some_other_secret',
            'region' => 'another_region',
            'stream' => 'logging',
            'level' => 'debug',
        ]);

        logger()->warning('Something went wrong.');
    }

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
     * @dataProvider loggerLevelTestProvider
     * @define-env withoutDefaultCredentials
     */
    public function test_standard_log_levels_are_supported($logLevel)
    {
        $this->mockKinesis()
            ->shouldReceive('putRecord')
            ->once()
            ->with(m::on(function ($argument) use ($logLevel) {
                $data = json_decode($argument['Data'], true);

                return $data['level'] === strtoupper($logLevel);
            }));

        logger()->$logLevel("Test {$logLevel} message");
    }

    /** @define-env withoutDefaultCredentials */
    public function test_driver_does_not_log_below_default_log_level()
    {
        config()->set('logging.channels.some_channel.level', 'warning');

        $this->mockKinesis()->shouldReceive('putRecord')->once();

        logger()->warning('Test warning message');
        logger()->debug('Test debug message');
    }

    /** @define-env withoutDefaultCredentials */
    public function test_driver_does_log_greater_than_or_equal_to_default_log_level()
    {
        config()->set('logging.channels.some_channel.level', 'warning');

        $this->mockKinesis()->shouldReceive('putRecord')->twice();

        logger()->warning('Test warning message');
        logger()->error('Test error message');
    }

    /** @define-env withoutDefaultCredentials */
    public function test_data_pushed_to_kinesis_is_properly_formatted()
    {
        $this->mockKinesis()->shouldReceive('putRecord')->once()->with(m::on(function ($argument) {
            $hasKeys = Arr::has($argument, ['Data', 'PartitionKey', 'StreamName']);

            $hasJsonKeys = Arr::has(json_decode($argument['Data'], true), [
                'timestamp', 'host', 'project', 'env', 'message',
                'channel', 'level', 'extra', 'context',
            ]);

            return $hasKeys && $hasJsonKeys;
        }));

        logger()->info('Test info message');
    }

    /** @define-env withoutDefaultCredentials */
    public function test_data_pushed_to_kinesis_can_also_forward_some_context()
    {
        $this->mockKinesis()->shouldReceive('putRecord')->once()->with(m::on(function ($argument) {
            $data = json_decode($argument['Data'], true);

            return $data['context'] === ['some_context' => ['key' => 'value']];
        }));

        logger()->info('Test info message', ['some_context' => ['key' => 'value']]);
    }

    /** @define-env withoutDefaultCredentials */
    public function test_channel_specific_http_client_options_can_be_given()
    {
        $this->mockKinesisWith(function ($mock) {
            $mock->shouldReceive('configure')->once()->with(m::on(function ($argument) {
                return $argument['http'] === ['verify' => false];
            }))->andReturn($mock);

            $mock->shouldReceive('putRecord')->once();
        });

        config()->set('logging.default', 'another_channel');
        config()->set('logging.channels.another_channel', [
            'driver' => 'kinesis',
            'region' => 'another_region',
            'stream' => 'logging',
            'level' => 'debug',
            'http' => [
                'verify' => false,
            ],
        ]);

        logger()->warning('Something went wrong.');
    }
}
