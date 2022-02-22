<?php

namespace PodPoint\MonologKinesis;

use Aws\Kinesis\KinesisClient;
use Illuminate\Config\Repository;
use Illuminate\Support\Arr;
use PodPoint\MonologKinesis\Contracts\Client;

class Kinesis implements Client
{
    /** @var KinesisClient */
    protected $kinesis;

    /** @var Repository */
    protected $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * Apply configuration to this client.
     *
     * @param  array  $channelConfig
     * @return $this
     */
    public function configure(array $channelConfig): Client
    {
        $this->kinesis = $this->configureKinesisClient($channelConfig);

        return $this;
    }

    /**
     * Configure the Kinesis client for logging records.
     *
     * @param  array  $channelConfig
     * @return KinesisClient
     */
    private function configureKinesisClient(array $channelConfig): KinesisClient
    {
        $defaultConfig = $this->config->get('services.kinesis');

        $config = [
            'region' => Arr::get($channelConfig, 'region', Arr::get($defaultConfig, 'region')),
            'version' => Arr::get($channelConfig, 'version', Arr::get($defaultConfig, 'version', 'latest')),
        ];

        if (Arr::has($channelConfig, ['key', 'secret'])) {
            $config['credentials'] = Arr::only($channelConfig, ['key', 'secret', 'token']);
        } elseif (Arr::has($defaultConfig, ['key', 'secret'])) {
            $config['credentials'] = Arr::only($defaultConfig, ['key', 'secret', 'token']);
        }

        return new KinesisClient($config);
    }

    /**
     * Put a new record in to the Kinesis stream.
     *
     * @param  array  $args
     * @return \Aws\Result
     */
    public function putRecord(array $args = []): \Aws\Result
    {
        return $this->kinesis->putRecord($args);
    }

    /**
     * Put multiple new records in to the Kinesis stream.
     *
     * @param  array  $args
     * @return \Aws\Result
     */
    public function putRecords(array $args = []): \Aws\Result
    {
        return $this->kinesis->putRecords($args);
    }
}
