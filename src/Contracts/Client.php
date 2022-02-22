<?php

namespace PodPoint\MonologKinesis\Contracts;

/**
 * @see \Aws\Kinesis\KinesisClient
 */
interface Client
{
    public function configure(array $channelConfig): Client;

    public function putRecord(array $args = []): \Aws\Result;

    public function putRecords(array $args = []): \Aws\Result;
}
