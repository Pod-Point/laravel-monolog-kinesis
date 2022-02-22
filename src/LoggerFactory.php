<?php

namespace PodPoint\MonologKinesis;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Monolog\Logger;
use PodPoint\MonologKinesis\Contracts\Client;

class LoggerFactory
{
    /** @var Application */
    protected $app;

    /**
     * Create a new factory instance.
     *
     * @param  Container  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Create a new instance of a Kinesis logger given the logging channel config.
     *
     * @param  array  $config
     * @return Logger
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function create(array $config): Logger
    {
        $client = $this->app->make(Client::class)->configure($config);
        $level = Arr::get($config, 'level', Logger::DEBUG);

        $kinesisHandler = new Handler($client, $config['stream'], $level);
        $kinesisHandler->setFormatter($this->createKinesisFormatter());

        return new Logger('kinesis', [$kinesisHandler]);
    }

    /**
     * Create a formatter for a Kinesis logger.
     *
     * @return Formatter
     */
    private function createKinesisFormatter(): Formatter
    {
        return new Formatter($this->app['config']->get('app.name'), $this->app->environment());
    }
}
