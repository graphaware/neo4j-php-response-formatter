<?php

namespace GraphAware\NeoClient\Formatter\Tests;

use GraphAware\NeoClient\Formatter\ResultFormatter;

class ResultFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testFormatTable()
    {
        $response = json_decode(file_get_contents(__DIR__.'/_resources/table-format.json'), true);
        $formatter = new ResultFormatter();
        foreach ($response['results'] as $result) {
            $formatter->formatResult($result);
        }
    }
}