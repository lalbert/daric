<?php

namespace Daric\Tests\Cleaner;

use Daric\Cleaner\TrimCleaner;

class TrimCleanerTest extends \PHPUnit_Framework_TestCase
{
    public function testString()
    {
        $string = '  my string  ';
        $cleaner = new TrimCleaner();
        $this->assertEquals('my string', $cleaner->clean($string));
    }

    public function testCharlist()
    {
        $string = '---my string--';
        $cleaner = new TrimCleaner('-');
        $this->assertEquals('my string', $cleaner->clean($string));
    }

    public function testArray()
    {
        $array = [
            ' my string 1   ',
            '    My string 2',
        ];
        $cleaner = new TrimCleaner();
        $this->assertArraySubset(['my string 1', 'My string 2'], $cleaner->clean($array));
    }

    public function testComplexArray()
    {
        $array = [
            'k1' => [
                'k1.1' => [
                    ' val  1.1.1        ',
                    '    val 1.1.2',
                ],
                'k1.2' => [
                    '   val 1.2.1',
                ],
            ],
        ];

        $cleaner = new TrimCleaner();
        $result = $cleaner->clean($array);
        $this->assertEquals('val  1.1.1', $result['k1']['k1.1'][0]);
        $this->assertEquals('val 1.1.2', $result['k1']['k1.1'][1]);
        $this->assertEquals('val 1.2.1', $result['k1']['k1.2'][0]);
    }
}
