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

    public function configure(array $channelConfig): Kinesis
    {
        $this->kinesis = $this->configureKinesisClient($channelConfig);

        return $this;
    }

    public function putRecord(array $args = []): \Aws\Result
    {
        return $this->kinesis->putRecord($args);
    }

    public function putRecords(array $args = []): \Aws\Result
    {
        return $this->kinesis->putRecords($args);
    }

    private function configureKinesisClient(array $channelConfig): KinesisClient
    {
        $defaultConfig = $this->config->get('services.kinesis');

        $config = [
            'region' => Arr::get($channelConfig, 'region', Arr::get($defaultConfig, 'region')),
            'version' => Arr::get($channelConfig, 'version', Arr::get($defaultConfig, 'version', 'latest')),
        ];

        if ($this->hasCredentials($channelConfig)) {
            $config['credentials'] = $this->credentials($channelConfig);
        } else if ($this->hasCredentials($defaultConfig)) {
            $config['credentials'] = $this->credentials($defaultConfig);
        }

        return new KinesisClient($config);
    }

    private function hasCredentials(array $config): bool
    {
        return Arr::has($config, ['key', 'secret']);
    }

    private function credentials(array $config): array
    {
        return Arr::only($config, ['key', 'secret', 'token']);
    }
}
