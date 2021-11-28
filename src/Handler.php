<?php

namespace PodPoint\MonologKinesis;

use Exception;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use PodPoint\MonologKinesis\Contracts\Client;

class Handler extends AbstractProcessingHandler
{
    /** @var Client */
    private $client;

    /**
     * Kinesis stream name.
     *
     * @var string
     */
    private $stream;

    public function __construct(
        Client $kinesisClient,
        string $stream,
        $level = Logger::DEBUG,
        bool $bubble = true
    ) {
        $this->client = $kinesisClient;
        $this->stream = $stream;

        parent::__construct($level, $bubble);
    }

    /**
     * Writes the record down to the log of the implementing handler.
     *
     * @param  array  $record
     * @return void
     */
    protected function write(array $record): void
    {
        $content = $record['formatted'];
        $content['StreamName'] = $this->stream;

        try {
            $this->client->putRecord($content);
        } catch (Exception $e) {
            // Fire and forget
        }
    }

    /**
     * Handles a set of records at once.
     *
     * @param  array  $records
     * @return void
     */
    public function handleBatch(array $records): void
    {
        $kinesisParameters = $this->getFormatter()->formatBatch($records);
        $kinesisParameters['StreamName'] = $this->stream;

        try {
            $this->client->putRecords($kinesisParameters);
        } catch (Exception $e) {
            // Fire and forget
        }
    }
}
