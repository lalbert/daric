<?php

namespace Daric\Tests\Formatter;

use Daric\Formatter\FloatFormatter;

class FloatFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider floatProvider
     *
     * @param  $value
     * @param  $expected
     */
    public function testFlatFormater($value, $expected)
    {
        $this->assertEquals($expected, (new FloatFormatter())->format($value));
    }

    public function floatProvider()
    {
        return [
            # Simple
            ['123.123', 123.123],
            ['123,123', 123.123],

            # Big with spaces (fr)
            ['123 123.123', 123123.123],
            ['123 123 123.123', 123123123.123],

            # Big with comma (en)
            ['123,123.123', 123123.123],
            ['123,123,123.123', 123123123.123],

            # Neg
            ['-123.123', -123.123],

            # Int
            ['123', 123.0],
        ];
    }

    public function testArrayFloat()
    {
        $array = ['key1' => '123.123', 'key2' => '123,123'];
        $formatted = (new FloatFormatter())->format($array);

        $this->assertEquals(123.123, $formatted['key1']);
        $this->assertEquals(123.123, $formatted['key2']);
    }
}
