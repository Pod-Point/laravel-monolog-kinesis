<?php

namespace PodPoint\MonologKinesis;

use Monolog\Formatter\NormalizerFormatter;

class Formatter extends NormalizerFormatter
{
    use ApplicationAwareFormatter;

    public const SIMPLE_DATE = 'Y-m-d\TH:i:s.uP';

    /**
     * Formats a log record.
     *
     * @param  array  $record  A record to format
     * @return mixed The formatted record
     */
    public function format(array $record)
    {
        $record = parent::format($record);

        return [
            'Data' => $this->toJson([
                'timestamp' => $record['datetime'] ?? gmdate('c'),
                'host' => gethostname(),
                'project' => $this->name,
                'env' => $this->environment,
                'message' => $record['message'],
                'channel' => $record['channel'],
                'level' => $record['level_name'],
                'extra' => $record['extra'],
                'context' => $record['context'],
            ]),
            'PartitionKey' => $record['channel'],
        ];
    }

    /**
     * Formats a set of log records.
     *
     * @param  array  $records  A set of records to format
     * @return mixed The formatted set of records
     */
    public function formatBatch(array $records)
    {
        return [
            'Records' => collect($records)->map([$this, 'format'])->toArray(),
        ];
    }
}
