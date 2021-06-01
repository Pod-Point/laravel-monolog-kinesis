<?php

namespace PodPoint\KinesisLogger\Monolog;

use Aws\Kinesis\KinesisClient;
use Exception;
use Monolog\Formatter\FormatterInterface;
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
    private $stream;

    /**
     * KinesisHandler constructor.
     *
     * @param string $stream
     * @param string $level
     * @param bool $bubble
     */
    public function __construct(string $stream, string $level, bool $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->client = app(KinesisClient::class);
        $this->stream = $stream;
    }

    /**
     * Overrides the default line formatter.
     *
     * @return FormatterInterface
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return app(KinesisFormatter::class);
    }

    /**
     * Writes the record down to the log of the implementing handler.
     *
     * @param  array $record
     * @return void
     */
    protected function write(array $record): void
    {
        $content = $record['formatted'];
        $content['Stream'] = $this->stream;

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
        $kinesisParameters['Stream'] = $this->stream;

        try {
            $this->client->putRecords($kinesisParameters);
        } catch (Exception $ex) {
            // Fire and forget
        }
    }
}
