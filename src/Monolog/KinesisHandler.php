<?php

namespace PodPoint\KinesisLogger\Monolog;

use Exception;
use Aws\Kinesis\KinesisClient;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

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
     * @param int $level
     * @param bool $bubble
     */
    public function __construct(string $streamName, int $level = Logger::INFO, bool $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->client = app(KinesisClient::class);
        $this->streamName = $streamName;
    }

    /**
     * Gets the default formatter.
     *
     * @return FormatterInterface
     */
    protected function getDefaultFormatter()
    {
        return new KinesisFormatter();
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     * @return void
     */
    protected function write(array $record)
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
    public function handleBatch(array $records)
    {
        $kinesisParameters = $this->getFormatter()->formatBatch($records);
        $kinesisParameters['StreamName'] = $this->streamName;

        try {
            $this->client->putRecords($kinesisParameters);
        } catch (Exception $ex) {
            // Fire and forget
        }

        return false === $this->bubble;
    }
}
