<?php

namespace PodPoint\MonologKinesis;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Monolog\Formatter\FormatterInterface;
use Monolog\Logger;
use PodPoint\MonologKinesis\Contracts\Client;

class LoggerFactory
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var FormatterInterface
     */
    private $formatter;

    /**
     * Create a new factory instance.
     *
     * @param  Client  $client
     * @param  FormatterInterface  $formatter
     * @return void
     */
    public function __construct(Client $client, FormatterInterface $formatter)
    {
        $this->client = $client;
        $this->formatter = $formatter;
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
        $client = $this->client->configure($config);
        $level = Arr::get($config, 'level', Logger::DEBUG);

        $kinesisHandler = new Handler($client, $config['stream'], $level);
        $kinesisHandler->setFormatter($this->formatter);

        return new Logger('kinesis', [$kinesisHandler]);
    }
}
