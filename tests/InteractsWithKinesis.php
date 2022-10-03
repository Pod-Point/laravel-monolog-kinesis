<?php

namespace PodPoint\MonologKinesis\Tests;

use Closure;
use Illuminate\Foundation\Application;
use PodPoint\MonologKinesis\Contracts\Client;

trait InteractsWithKinesis
{
    protected function mockKinesis(): \Mockery\MockInterface
    {
        $mock = $this->mock(Client::class);

        $mock->shouldReceive('configure')->andReturn($mock);

        return $mock;
    }

    protected function mockKinesisWith(Closure $mock): \Mockery\MockInterface
    {
        return $this->mock(Client::class, $mock);
    }

    protected function withDefaultCredentials(Application $app): void
    {
        $app->config->set('services.kinesis.key', 'dummy-key');
        $app->config->set('services.kinesis.secret', 'dummy-secret');
    }

    protected function withNullDefaultCredentials(Application $app): void
    {
        $app->config->set('services.kinesis.key', null);
        $app->config->set('services.kinesis.secret', null);
    }

    protected function withoutDefaultCredentials(Application $app): void
    {
        // ...
    }

    protected function assertLoggerChannelAwsConfigEquals(string $channel, array $expected): void
    {
        $kinesisHandler = collect(logger()->channel($channel)->getHandlers())->first();

        $this->assertInstanceOf(Handler::class, $kinesisHandler);

        /** @var \PodPoint\MonologKinesis\Handler $kinesisHandler */
        $this->assertEquals($expected, $kinesisHandler->getClient()->getAwsConfig());
    }
}
