<?php

namespace Daric\Tests\Cleaner;

use Daric\Cleaner\LTrimCleaner;

class LTrimCleanerTest extends \PHPUnit_Framework_TestCase
{
    public function testString()
    {
        $string = '  my string  ';
        $cleaner = new LTrimCleaner();
        $this->assertEquals('my string  ', $cleaner->clean($string));
    }

    public function testCharlist()
    {
        $string = '---my string--';
        $cleaner = new LTrimCleaner('-');
        $this->assertEquals('my string--', $cleaner->clean($string));
    }
}
