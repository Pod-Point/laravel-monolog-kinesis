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

    /** @var array */
    protected $awsConfig = [];

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
        $defaultConfig = $this->config->get('services.kinesis');

        $config = [
            'region' => Arr::get($channelConfig, 'region', Arr::get($defaultConfig, 'region')),
            'version' => Arr::get($channelConfig, 'version', Arr::get($defaultConfig, 'version', 'latest')),
            'http' => Arr::get($channelConfig, 'http', Arr::get($defaultConfig, 'http', [])),
        ];

        if ($this->configHasCredentials($channelConfig)) {
            $config['credentials'] = Arr::only($channelConfig, ['key', 'secret', 'token']);
        } elseif ($this->configHasCredentials($defaultConfig)) {
            $config['credentials'] = Arr::only($defaultConfig, ['key', 'secret', 'token']);
        }

        $this->kinesis = new KinesisClient($this->awsConfig = $config);

        return $this;
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

    /**
     * Return the actual array used to configure the AWS Client.
     *
     * @return array
     */
    public function getAwsConfig(): array
    {
        return $this->awsConfig;
    }

    /**
     * Make sure some AWS credentials were provided to the configuration array.
     *
     * @return bool
     */
    private function configHasCredentials(array $config): bool
    {
        return Arr::has($config, ['key', 'secret'])
            && is_string(Arr::get($config, 'key'))
            && is_string(Arr::get($config, 'secret'));
    }
}
