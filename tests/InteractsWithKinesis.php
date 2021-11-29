<?php

namespace PodPoint\MonologKinesis\Tests;

use Closure;
use PodPoint\MonologKinesis\Contracts\Client;

trait InteractsWithKinesis
{
    public function mockKinesis(): \Mockery\MockInterface
    {
        $mock = $this->mock(Client::class);
        $mock->shouldReceive('configure')->andReturn($mock);

        return $mock;
    }

    public function mockKinesisWith(Closure $mock): \Mockery\MockInterface
    {
        return $this->mock(Client::class, $mock);
    }
}
