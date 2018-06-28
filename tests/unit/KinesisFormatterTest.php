<?php

use PodPoint\KinesisLogger\Monolog\KinesisFormatter;

class KinesisFormatterTest extends TestCase
{
    /**
     * Test the formatter formats the message for Kinesis.
     */
    public function testFormat()
    {
        $record = [
            'message' => 'TEST',
            'context' => [
                'test' => 'thing'
            ],
            'level' => 400,
            'level_name' => 'ERROR',
            'channel' => 'local',
            'datetime' => new DateTime(),
            'extra' => [],
        ];

        $formatter = new KinesisFormatter('test', 'local');
        $output = $formatter->format($record);

        $this->assertArrayHasKey('Data', $output);
    }
}
