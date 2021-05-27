<?php

namespace PodPoint\KinesisLogger\Monolog;

use Exception;
use Aws\Kinesis\KinesisClient;
use Monolog\Handler\AbstractProcessingHandler;

class KinesisHandler extends AbstractProcessingHandler
{
    /**
     * Kinesis client.
     *
     * @var KinesisClient
     */
    private $client;

    /**
     * Kinesis stream name.
     *
     * @var bool
     */
    private $streamName;

    /**
     * KinesisHandler constructor.
     *
     * @param string $streamName
     * @param string $level
     * @param bool $bubble
     */
    public function __construct(string $streamName, string $level, bool $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->client = app(KinesisClient::class);
        $this->streamName = $streamName;

        $this->formatter = app(KinesisFormatter::class);
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     * @return void
     */
    protected function write(array $record): void
    {
        $content = $record['formatted'];
        $content['StreamName'] = $this->streamName;

        try {
            $this->client->putRecord($content);
        } catch (Exception $ex) {
            // Fire and forget
        }
    }

    /**
     * Handles a set of records at once.
     *
     * @param array $records
     * @return void
     */
    public function handleBatch(array $records): void
    {
        $kinesisParameters = $this->getFormatter()->formatBatch($records);
        $kinesisParameters['StreamName'] = $this->streamName;

        try {
            $this->client->putRecords($kinesisParameters);
        } catch (Exception $ex) {
            // Fire and forget
        }
    }
}
