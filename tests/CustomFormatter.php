<?php

namespace PodPoint\MonologKinesis\Tests;

use Monolog\Formatter\FormatterInterface;

class CustomFormatter implements FormatterInterface
{
    public function format(array $record)
    {
        return [
            'Data' => ['custom_message' => $record['message']],
        ];
    }

    public function formatBatch(array $records)
    {
        return [
            'Records' => collect($records)->map([$this, 'format'])->toArray(),
        ];
    }
}
