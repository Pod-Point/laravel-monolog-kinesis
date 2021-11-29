<?php

namespace PodPoint\MonologKinesis\Tests;

use PodPoint\MonologKinesis\Contracts\Client;

trait InteractsWithClient
{
    public function mockClient(): \Mockery\MockInterface
    {
        $mock = $this->mock(Client::class);
        $mock->shouldReceive('configure')->andReturn($mock);

        return $mock;
    }
}
