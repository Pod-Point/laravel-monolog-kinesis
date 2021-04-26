<?php

namespace PodPoint\KinesisLogger\Monolog;

use Exception;
use Aws\Kinesis\KinesisClient;
use Illuminate\Support\Facades\App;
use Monolog\Formatter\FormatterInterface;
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
     * @param KinesisClient $client
     * @param string $streamName
     * @param int $level
     * @param bool $bubble
     */
    public function __construct(KinesisClient $client, string $streamName, int $level = Logger::INFO, bool $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->client = $client;
        $this->streamName = $streamName;
    }

    /**
     * Gets the default formatter.
     *
     * @return FormatterInterface
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new KinesisFormatter(config('app.name'), App::environment());
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
     * @return bool
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
