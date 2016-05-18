<?php

namespace Daric\Tests\Cleaner;

use Daric\Cleaner\RTrimCleaner;

class RTrimCleanerTest extends \PHPUnit_Framework_TestCase
{
    public function testString()
    {
        $string = '  my string  ';
        $cleaner = new RTrimCleaner();
        $this->assertEquals('  my string', $cleaner->clean($string));
    }

    public function testCharlist()
    {
        $string = '---my string--';
        $cleaner = new RTrimCleaner('-');
        $this->assertEquals('---my string', $cleaner->clean($string));
    }
}
