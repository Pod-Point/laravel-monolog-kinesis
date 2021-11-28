<?php

namespace PodPoint\MonologKinesis;

use Aws\Kinesis\KinesisClient;
use Illuminate\Support\Arr;
use PodPoint\MonologKinesis\Contracts\Client as MonologKinesisClientContract;

class Client implements MonologKinesisClientContract
{
    /** @var KinesisClient */
    protected $kinesis;

    public function configure(array $config): Client
    {
        $this->kinesis = $this->configureKinesisClient($config);

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

    private function configureKinesisClient(array $config): KinesisClient
    {
        if (Arr::has($config, ['key', 'secret'])) {
            $config['credentials'] = [
                'key' => $config['key'],
                'secret' => $config['secret'],
                'token' => $config['token'] ?? null,
            ];
        }

        return new KinesisClient(array_merge($config, ['version' => 'latest']));
    }
}
